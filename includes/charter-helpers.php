<?php
/**
 * Helper functions for browsing and searching the Citizen's Charter data
 * (citizens_charter_services DB table) across the citizen portal —
 * category grouping, slugs for clean URLs, and the site search index.
 * Admins manage this data via admin/citizens-charter.php.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Every service row from the DB, each with `keywords` and `requirements`
 * split back into arrays (stored one item per line). Cached per-request.
 */
function getCharterServices(): array
{
    static $services = null;
    if ($services === null) {
        $pdo = getDbConnection();
        $rows = $pdo->query('SELECT * FROM citizens_charter_services ORDER BY sort_order ASC, id ASC')->fetchAll();

        $services = array_map(static function (array $row): array {
            return [
                'id' => (int) $row['id'],
                'office' => $row['office'],
                'title' => $row['title'],
                'keywords' => charterSplitLines($row['keywords']),
                'requirements' => charterSplitLines($row['requirements']),
                'fee' => $row['fee'],
                'processing_time' => $row['processing_time'],
            ];
        }, $rows);
    }

    return $services;
}

/** Splits a newline-separated DB column back into a trimmed, non-empty list. */
function charterSplitLines(string $text): array
{
    return array_values(array_filter(
        array_map('trim', explode("\n", $text)),
        fn($line) => $line !== ''
    ));
}

/**
 * URL-safe slug from a title or office name, e.g. "Senior Citizen's ID"
 * -> "senior-citizens-id". Used instead of numeric IDs so links stay
 * readable and stable even if entries are reordered in the data file.
 */
function charterSlugify(string $text): string
{
    $slug = strtolower($text);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    return trim($slug, '-');
}

function getCharterOfficeIcon(string $office): string
{
    $icons = [
        "Business Permits & Licensing Office" => 'fa-store',
        "Municipal Assessor's Office" => 'fa-map-location-dot',
        "Municipal Civil Registrar's Office" => 'fa-file-signature',
        "Municipal Engineering Office" => 'fa-drafting-compass',
        "Municipal Health Office" => 'fa-briefcase-medical',
        "Municipal Planning & Development Office" => 'fa-city',
        "Municipal Environment & Natural Resources Office (MENRO)" => 'fa-leaf',
        "Municipal Social Welfare & Development Office (MSWDO)" => 'fa-hand-holding-heart',
        "Municipal Treasurer's Office" => 'fa-coins',
        "Norzagaray Municipal Hospital" => 'fa-hospital',
    ];

    return $icons[$office] ?? 'fa-building';
}

/**
 * Every service, each carrying its title slug and office slug — the
 * shape every page in this feature builds on.
 */
function getCharterServicesWithSlugs(): array
{
    static $withSlugs = null;
    if ($withSlugs !== null) {
        return $withSlugs;
    }

    $withSlugs = [];
    foreach (getCharterServices() as $service) {
        $service['slug'] = charterSlugify($service['title']);
        $service['office_slug'] = charterSlugify($service['office']);
        $withSlugs[] = $service;
    }

    return $withSlugs;
}

/**
 * Distinct offices in file order, each with a slug, an icon, and how
 * many services fall under it — the category grid on the landing page.
 */
function getCharterOffices(): array
{
    $offices = [];
    foreach (getCharterServicesWithSlugs() as $service) {
        $slug = $service['office_slug'];
        if (!isset($offices[$slug])) {
            $offices[$slug] = [
                'name' => $service['office'],
                'slug' => $slug,
                'icon' => getCharterOfficeIcon($service['office']),
                'count' => 0,
            ];
        }
        $offices[$slug]['count']++;
    }

    return array_values($offices);
}

function getCharterOfficeBySlug(string $officeSlug): ?array
{
    foreach (getCharterOffices() as $office) {
        if ($office['slug'] === $officeSlug) {
            return $office;
        }
    }

    return null;
}

/** All services belonging to one office, matched by office slug. */
function getCharterServicesByOfficeSlug(string $officeSlug): array
{
    return array_values(array_filter(
        getCharterServicesWithSlugs(),
        fn($service) => $service['office_slug'] === $officeSlug
    ));
}

/** Look up a single service by its title slug. Null if not found. */
function findCharterServiceBySlug(string $slug): ?array
{
    foreach (getCharterServicesWithSlugs() as $service) {
        if ($service['slug'] === $slug) {
            return $service;
        }
    }

    return null;
}

/** Look up a single service by its DB id. Null if not found. */
function findCharterServiceById(int $id): ?array
{
    foreach (getCharterServicesWithSlugs() as $service) {
        if ($service['id'] === $id) {
            return $service;
        }
    }

    return null;
}

/** Distinct office names currently in use, for the admin form's autocomplete. */
function getCharterAllOfficeNames(): array
{
    $names = [];
    foreach (getCharterServices() as $service) {
        $names[$service['office']] = true;
    }

    return array_keys($names);
}

/**
 * Splits a search query into lowercase terms for matching/highlighting.
 */
function charterSearchTerms(string $query): array
{
    return array_values(array_filter(preg_split('/\s+/', strtolower(trim($query)))));
}

/**
 * Site search across the Citizen's Charter: matches the query against
 * title, office, keyword phrases, requirements, fee, and processing time.
 * Returns matching services ordered by relevance, each carrying a
 * `_snippet` (the matched line) and `_snippet_label` (where it was found)
 * so the results page can show citizens exactly why a page matched.
 */
function searchCharterServices(string $query): array
{
    $terms = charterSearchTerms($query);
    if (empty($terms)) {
        return [];
    }

    $results = [];

    foreach (getCharterServicesWithSlugs() as $service) {
        $score = 0;
        $snippet = null;
        $snippetLabel = null;

        $titleLower = strtolower($service['title']);
        $officeLower = strtolower($service['office']);
        $feeLower = strtolower($service['fee']);
        $timeLower = strtolower($service['processing_time']);

        foreach ($terms as $term) {
            if (str_contains($titleLower, $term)) {
                $score += 10;
                $snippet ??= $service['title'];
                $snippetLabel ??= 'Service title';
            }

            foreach ($service['keywords'] as $keyword) {
                if (str_contains(strtolower($keyword), $term)) {
                    $score += 5;
                    break;
                }
            }

            foreach ($service['requirements'] as $requirement) {
                if (str_contains(strtolower($requirement), $term)) {
                    $score += 2;
                    $snippet ??= $requirement;
                    $snippetLabel ??= 'Requirement';
                    break;
                }
            }

            if (str_contains($officeLower, $term)) {
                $score += 3;
            }

            if (str_contains($feeLower, $term)) {
                $score += 1;
                $snippet ??= 'Fee: ' . $service['fee'];
                $snippetLabel ??= 'Fee';
            }

            if (str_contains($timeLower, $term)) {
                $score += 1;
                $snippet ??= 'Processing time: ' . $service['processing_time'];
                $snippetLabel ??= 'Processing time';
            }
        }

        if ($score > 0) {
            $service['_score'] = $score;
            $service['_snippet'] = $snippet ?? $service['title'];
            $service['_snippet_label'] = $snippetLabel ?? 'Service title';
            $results[] = $service;
        }
    }

    usort($results, fn($a, $b) => $b['_score'] <=> $a['_score']);

    return $results;
}

/**
 * HTML-escapes $text, then wraps every case-insensitive occurrence of a
 * search term in <mark>. Returns already-escaped, render-ready markup —
 * echo directly, do not htmlspecialchars() the result again.
 */
function charterHighlight(string $text, array $terms): string
{
    $escaped = htmlspecialchars($text);
    if (empty($terms)) {
        return $escaped;
    }

    $pattern = implode('|', array_map(
        fn($term) => preg_quote(htmlspecialchars($term), '/'),
        $terms
    ));

    if ($pattern === '') {
        return $escaped;
    }

    return preg_replace('/(' . $pattern . ')/iu', '<mark>$1</mark>', $escaped);
}
