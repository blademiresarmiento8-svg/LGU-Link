<?php
/**
 * Admin-only write operations for the citizens_charter_services table.
 * Requires includes/charter-helpers.php (getDbConnection(), charterSlugify(),
 * getCharterServicesWithSlugs()) to already be loaded.
 */

/**
 * Validates and saves a service. Pass $id = null to create a new row,
 * or an existing service's id to update it in place.
 *
 * @param array<string, mixed> $data Raw $_POST fields: office, title,
 *   keywords (newline-separated), requirements (newline-separated), fee,
 *   processing_time.
 * @return array{success: bool, errors: string[]}
 */
function saveCharterService(?int $id, array $data): array
{
    $office = trim((string) ($data['office'] ?? ''));
    $title = trim((string) ($data['title'] ?? ''));
    $fee = trim((string) ($data['fee'] ?? ''));
    $processingTime = trim((string) ($data['processing_time'] ?? ''));
    $keywordLines = charterSplitLines((string) ($data['keywords'] ?? ''));
    $requirementLines = charterSplitLines((string) ($data['requirements'] ?? ''));

    $errors = [];
    if ($office === '') {
        $errors[] = 'Office is required.';
    }
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($fee === '') {
        $errors[] = 'Fee is required.';
    }
    if ($processingTime === '') {
        $errors[] = 'Processing time is required.';
    }
    if (empty($requirementLines)) {
        $errors[] = 'At least one requirement is required.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    if (empty($keywordLines)) {
        $keywordLines = [$title];
    }

    // A duplicate title would collide with an existing service's slug and
    // break the citizen-facing detail-page URL for one of them.
    $newSlug = charterSlugify($title);
    foreach (getCharterServicesWithSlugs() as $existing) {
        if ($existing['slug'] === $newSlug && $existing['id'] !== $id) {
            return [
                'success' => false,
                'errors' => ["Another service already has a matching title (\"{$existing['title']}\"). Use a more specific title."],
            ];
        }
    }

    $pdo = getDbConnection();

    if ($id === null) {
        $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), -1) FROM citizens_charter_services')->fetchColumn();
        $stmt = $pdo->prepare(
            'INSERT INTO citizens_charter_services (office, title, keywords, requirements, fee, processing_time, sort_order)
             VALUES (:office, :title, :keywords, :requirements, :fee, :processing_time, :sort_order)'
        );
        $stmt->execute([
            'office' => $office,
            'title' => $title,
            'keywords' => implode("\n", $keywordLines),
            'requirements' => implode("\n", $requirementLines),
            'fee' => $fee,
            'processing_time' => $processingTime,
            'sort_order' => $maxOrder + 1,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE citizens_charter_services
             SET office = :office, title = :title, keywords = :keywords,
                 requirements = :requirements, fee = :fee, processing_time = :processing_time
             WHERE id = :id'
        );
        $stmt->execute([
            'office' => $office,
            'title' => $title,
            'keywords' => implode("\n", $keywordLines),
            'requirements' => implode("\n", $requirementLines),
            'fee' => $fee,
            'processing_time' => $processingTime,
            'id' => $id,
        ]);
    }

    return ['success' => true, 'errors' => []];
}

function deleteCharterService(int $id): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM citizens_charter_services WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}
