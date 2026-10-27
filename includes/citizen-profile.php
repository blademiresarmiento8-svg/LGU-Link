<?php
/**
 * A citizen changing their own details (user/profile.php). The email is the login and stays fixed.
 *
 * Appointment requests do not keep a copy of the citizen's name: they show whatever the account says now.
 * So every change is written to the Activity Log (old and new), and the Municipal Administrator's citizen
 * details popup lists a citizen's earlier names (citizenNameHistory()).
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/audit-helpers.php';
require_once __DIR__ . '/registration-helpers.php';
require_once __DIR__ . '/organization-fields.php';

const CITIZEN_NAME_CHANGED = 'citizen_name_changed';
const CITIZEN_PROFILE_UPDATED = 'citizen_profile_updated';

/** The details a citizen can change, as saved now. */
function getCitizenProfile(int $id): ?array
{
    $stmt = getDbConnection()->prepare(
        "SELECT id, full_name, first_name, middle_name, last_name, suffix, mobile, organization, position_title, email
         FROM users WHERE id = :id AND role = 'user'"
    );
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

/**
 * Saves a citizen's name, mobile number, organization and position.
 *
 * @param array<string, mixed> $input Raw form fields.
 * @return array{success: bool, errors: string[], changed: bool}
 */
function updateCitizenProfile(int $id, array $input): array
{
    $current = getCitizenProfile($id);
    if ($current === null) {
        return ['success' => false, 'errors' => ['Your account could not be found.'], 'changed' => false];
    }

    $identity = validateCitizenIdentity($input);
    $organization = validateOrganizationFields($input);
    $errors = array_merge($identity['errors'], $organization['errors']);
    if ($errors) {
        return ['success' => false, 'errors' => $errors, 'changed' => false];
    }

    $new = [
        'first_name' => $identity['first'],
        'middle_name' => $identity['middle'] === '' ? null : $identity['middle'],
        'last_name' => $identity['last'],
        'suffix' => $identity['suffix'] === '' ? null : $identity['suffix'],
        'mobile' => $identity['mobile'],
        'organization' => $organization['organization'],
        'position_title' => $organization['position_title'],
    ];

    $nameChanged = $identity['full_name'] !== $current['full_name'];
    $others = [];
    foreach (['mobile' => 'Mobile number', 'organization' => 'Organization', 'position_title' => 'Position'] as $field => $label) {
        if ((string) $new[$field] !== (string) $current[$field]) {
            $others[] = $label . ': ' . (((string) $current[$field]) === '' ? '(none)' : $current[$field]) . ' → ' . (((string) $new[$field]) === '' ? '(none)' : $new[$field]);
        }
    }
    $partsChanged = $nameChanged
        || (string) $new['first_name'] !== (string) $current['first_name'] || (string) $new['middle_name'] !== (string) $current['middle_name']
        || (string) $new['last_name'] !== (string) $current['last_name'] || (string) $new['suffix'] !== (string) $current['suffix'];

    if (!$partsChanged && !$others) {
        return ['success' => true, 'errors' => [], 'changed' => false];
    }

    getDbConnection()->prepare(
        'UPDATE users SET full_name = :full_name, first_name = :first_name, middle_name = :middle_name, last_name = :last_name, suffix = :suffix,
                mobile = :mobile, organization = :organization, position_title = :position_title
         WHERE id = :id'
    )->execute($new + ['full_name' => $identity['full_name'], 'id' => $id]);

    if ($nameChanged) {
        $cut = fn(string $name) => mb_substr($name, 0, 100);
        logAudit(CITIZEN_NAME_CHANGED, $id, $identity['full_name'], $cut((string) $current['full_name']) . ' → ' . $cut($identity['full_name']));
    }
    if ($others) {
        logAudit(CITIZEN_PROFILE_UPDATED, $id, $identity['full_name'], mb_substr(implode('; ', $others), 0, 255));
    }

    return ['success' => true, 'errors' => [], 'changed' => true];
}

/**
 * The earlier names of a citizen account, newest change first.
 *
 * @return array<int, array{from: string, to: string, at: string}>
 */
function citizenNameHistory(int $id): array
{
    $stmt = getDbConnection()->prepare(
        'SELECT details, created_at FROM audit_log WHERE action = :action AND target_user_id = :id ORDER BY id DESC LIMIT 20'
    );
    $stmt->execute(['action' => CITIZEN_NAME_CHANGED, 'id' => $id]);

    $history = [];
    foreach ($stmt->fetchAll() as $row) {
        $parts = explode(' → ', (string) $row['details'], 2);
        if (count($parts) === 2) {
            $history[] = ['from' => $parts[0], 'to' => $parts[1], 'at' => $row['created_at']];
        }
    }

    return $history;
}
