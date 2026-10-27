<?php
/**
 * Helper functions for browsing and searching the Citizen's Charter data
 * (citizens_charter_services DB table) across the citizen portal —
 * category grouping, slugs for clean URLs, and the site search index.
 * Admins manage this data via admin/citizens-charter.php.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Every service row from the DB, each with `keywords`, `requirements` and `steps`
 * split back into arrays (stored one item per line). Cached per-request.
 */
function getCharterServices(): array
{
    static $services = null;
    if ($services === null) {
        $pdo = getDbConnection();
        $rows = $pdo->query(
            'SELECT s.*, d.name AS department_name, d.code AS department_code
             FROM citizens_charter_services s LEFT JOIN departments d ON d.id = s.department_id
             ORDER BY s.sort_order ASC, s.id ASC'
        )->fetchAll();

        $services = array_map(static function (array $row): array {
            return [
                'id' => (int) $row['id'],
                // The department's own name; the typed name only for a service not linked to one.
                'office' => $row['department_name'] ?? $row['office'],
                'office_legacy' => $row['office'],
                'department_id' => $row['department_id'] === null ? null : (int) $row['department_id'],
                'office_code' => $row['department_code'],
                'title' => $row['title'],
                'keywords' => charterSplitLines($row['keywords']),
                'requirements' => charterSplitLines($row['requirements']),
                'steps' => charterSplitLines((string) ($row['steps'] ?? '')),
                'who_may_avail' => trim((string) ($row['who_may_avail'] ?? '')),
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

/** Icon for an office, by department code (so it survives renaming). */
const CHARTER_OFFICE_ICONS = [
    'BPLO' => 'fa-store', 'ASS' => 'fa-map-location-dot', 'LCR' => 'fa-file-signature', 'ENGR' => 'fa-drafting-compass',
    'MHO' => 'fa-briefcase-medical', 'MPDO' => 'fa-city', 'MENRO' => 'fa-leaf', 'MSWDO' => 'fa-hand-holding-heart',
    'TREAS' => 'fa-coins', 'NMH' => 'fa-hospital', 'ACCT' => 'fa-calculator', 'AGRIC' => 'fa-wheat-awn',
    'BUDGET' => 'fa-wallet', 'GSO' => 'fa-boxes-stacked', 'LYDO' => 'fa-people-group', 'MO' => 'fa-landmark',
    'MAO' => 'fa-user-tie', 'MDRRMO' => 'fa-triangle-exclamation', 'MHRMO' => 'fa-users', 'NC' => 'fa-graduation-cap',
    'NTMPSO' => 'fa-traffic-light', 'NUTR' => 'fa-apple-whole', 'OSCA' => 'fa-person-cane', 'PDAO' => 'fa-wheelchair',
    'POP' => 'fa-people-roof', 'PESO' => 'fa-briefcase', 'PIO' => 'fa-bullhorn', 'SB' => 'fa-gavel', 'TOUR' => 'fa-umbrella-beach',
];

function getCharterOfficeIcon(string $office): string
{
    static $codeByName = null;
    if ($codeByName === null) {
        $codeByName = [];
        foreach (getCharterServices() as $service) {
            if (!empty($service['office_code'])) {
                $codeByName[$service['office']] = $service['office_code'];
            }
        }
    }

    return CHARTER_OFFICE_ICONS[$codeByName[$office] ?? ''] ?? 'fa-building';
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

/**
 * The office page address uses the office name, which is now the department's name. A link made
 * with the old typed name (a bookmark, a shared link) still finds the right office.
 */
function resolveCharterOfficeSlug(string $officeSlug): string
{
    foreach (getCharterServicesWithSlugs() as $service) {
        if ($service['office_slug'] === $officeSlug) {
            return $officeSlug;
        }
    }
    foreach (getCharterServicesWithSlugs() as $service) {
        if (charterSlugify($service['office_legacy']) === $officeSlug) {
            return $service['office_slug'];
        }
    }

    return $officeSlug;
}

function getCharterOfficeBySlug(string $officeSlug): ?array
{
    $officeSlug = resolveCharterOfficeSlug($officeSlug);
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
    $officeSlug = resolveCharterOfficeSlug($officeSlug);

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
 * title, office, keyword phrases, requirements, steps, fee, and processing time.
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

            foreach ($service['steps'] as $step) {
                if (str_contains(strtolower($step), $term)) {
                    $score += 1;
                    $snippet ??= $step;
                    $snippetLabel ??= 'Step';
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
