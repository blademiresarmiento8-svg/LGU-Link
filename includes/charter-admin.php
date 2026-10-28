<?php
/**
 * Admin-only write operations for the citizens_charter_services table.
 * Requires includes/charter-helpers.php (getDbConnection(), charterSlugify(),
 * getCharterServicesWithSlugs()) and includes/department-helpers.php (findDepartmentById())
 * to already be loaded.
 */

require_once __DIR__ . '/input-limits.php';
require_once __DIR__ . '/department-helpers.php';

/**
 * Validates and saves a service. Pass $id = null to create a new row,
 * or an existing service's id to update it in place.
 *
 * @param array<string, mixed> $data Raw $_POST fields: department_id (the office), title,
 *   who_may_avail, keywords (newline-separated), requirements (newline-separated),
 *   steps (newline-separated), fee,
 *   processing_time.
 * @return array{success: bool, errors: string[]}
 */
function saveCharterService(?int $id, array $data): array
{
    $departmentId = (int) ($data['department_id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    $fee = trim((string) ($data['fee'] ?? ''));
    $processingTime = trim((string) ($data['processing_time'] ?? ''));
    $keywordLines = charterSplitLines((string) ($data['keywords'] ?? ''));
    $requirementLines = charterSplitLines((string) ($data['requirements'] ?? ''));
    $stepLines = charterSplitLines((string) ($data['steps'] ?? ''));
    $whoMayAvail = trim((string) ($data['who_may_avail'] ?? ''));

    $errors = [];
    $department = $departmentId > 0 ? findDepartmentById($departmentId) : null;
    if ($department === null) {
        $errors[] = 'Please choose the office this service belongs to.';
    }
    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (($tooLong = tooLongMessage('The title', $title, 200)) !== null) {
        $errors[] = $tooLong;
    }
    if ($fee === '') {
        $errors[] = 'Fee is required.';
    } elseif (($tooLong = tooLongMessage('The fee', $fee, 255)) !== null) {
        $errors[] = $tooLong;
    }
    if ($processingTime === '') {
        $errors[] = 'Processing time is required.';
    } elseif (($tooLong = tooLongMessage('The processing time', $processingTime, 255)) !== null) {
        $errors[] = $tooLong;
    }
    foreach (['The keywords' => $keywordLines, 'The requirements' => $requirementLines, 'The steps' => $stepLines] as $label => $lines) {
        if (($tooLong = tooLongMessage($label, implode("\n", $lines), LIST_TEXT_MAX_LENGTH)) !== null) {
            $errors[] = $tooLong;
        }
    }
    if (empty($requirementLines)) {
        $errors[] = 'At least one requirement is required.';
    }
    if (mb_strlen($whoMayAvail) > 500) {
        $errors[] = 'Who may avail must be 500 characters or fewer.';
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
            'INSERT INTO citizens_charter_services (department_id, office, title, who_may_avail, keywords, requirements, steps, fee, processing_time, sort_order)
             VALUES (:department_id, :office, :title, :who_may_avail, :keywords, :requirements, :steps, :fee, :processing_time, :sort_order)'
        );
        $stmt->execute([
            'department_id' => $departmentId,
            'office' => $department['name'],
            'title' => $title,
            'who_may_avail' => $whoMayAvail === '' ? null : $whoMayAvail,
            'keywords' => implode("\n", $keywordLines),
            'requirements' => implode("\n", $requirementLines),
            'steps' => empty($stepLines) ? null : implode("\n", $stepLines),
            'fee' => $fee,
            'processing_time' => $processingTime,
            'sort_order' => $maxOrder + 1,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE citizens_charter_services
             SET department_id = :department_id, office = :office, title = :title, who_may_avail = :who_may_avail,
                 keywords = :keywords, requirements = :requirements, steps = :steps, fee = :fee, processing_time = :processing_time
             WHERE id = :id'
        );
        $stmt->execute([
            'department_id' => $departmentId,
            'office' => $department['name'],
            'title' => $title,
            'who_may_avail' => $whoMayAvail === '' ? null : $whoMayAvail,
            'keywords' => implode("\n", $keywordLines),
            'requirements' => implode("\n", $requirementLines),
            'steps' => empty($stepLines) ? null : implode("\n", $stepLines),
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
