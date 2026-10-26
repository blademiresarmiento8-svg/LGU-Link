<?php
/**
 * One-time update: replaces the departments table contents with the client
 * LGU's real office list (see sql/schema.sql). Run once via:
 *   php sql/migrate-departments.php
 *
 * Rows are matched by code, so existing ids (and any users/appointments
 * pointing at them) are kept. Old offices that are not in the real list are
 * deleted unless something still references them.
 */

require_once __DIR__ . '/../config/database.php';

$offices = [
    ['MO', "Mayor's Office", 'Executive'],
    ['MAO', "Municipal Administrator's Office", 'Executive'],
    ['BUDGET', "Budget Office", 'Finance'],
    ['ACCT', "Accounting Office", 'Finance'],
    ['TOUR', "Tourism Office", 'Infrastructure'],
    ['PIO', "Public Information Office", 'Executive'],
    ['LYDO', "Local Youth Development Office", 'Social'],
    ['MHRMO', "Municipal Human Resource Management Office", 'Executive'],
    ['TREAS', "Treasurer's Office", 'Finance'],
    ['LCR', "Local Civil Registrar", 'Executive'],
    ['MPDO', "Municipal Planning and Development Office", 'Executive'],
    ['MENRO', "Municipal Environment and Natural Resources Office", 'Infrastructure'],
    ['ASS', "Assessor's Office", 'Finance'],
    ['BPLO', "Business Permit and Licensing Office", 'Finance'],
    ['MDRRMO', "Municipal Disaster Risk Reduction and Management Office", 'Public Safety'],
    ['NTMPSO', "Norzagaray Traffic Management and Public Safety Office", 'Public Safety'],
    ['SB', "Sangguniang Bayan", 'Public Safety'],
    ['OSCA', "Office of the Senior Citizens' Affairs", 'Social'],
    ['PDAO', "Persons with Disability Affairs Office", 'Social'],
    ['POP', "Population Office", 'Social'],
    ['NUTR', "Nutrition Office", 'Social'],
    ['GSO', "General Services Office", 'Executive'],
    ['MSWDO', "Municipal Social Welfare and Development Office", 'Social'],
    ['PESO', "Public Employment Service Office", 'Social'],
    ['ENGR', "Engineering Office", 'Infrastructure'],
    ['AGRIC', "Agriculture Office", 'Social'],
    ['NMH', "Norzagaray Municipal Hospital", 'Social'],
    ['MHO', "Municipal Health Office", 'Social'],
    ['NC', "Norzagaray College", 'Social'],
];

// Old code => new code, so existing rows are renamed in place.
$renames = ['VM' => 'SB', 'HRMO' => 'MHRMO', 'CEO' => 'ENGR', 'POSO' => 'NTMPSO'];

$pdo = getDbConnection();

// Older databases predate the department_head column.
if (!$pdo->query("SHOW COLUMNS FROM departments LIKE 'department_head'")->fetch()) {
    $pdo->exec('ALTER TABLE departments ADD COLUMN department_head VARCHAR(150) NULL AFTER sector');
}

// Department heads, from the client's records. Only filled in where a head
// isn't already set, so re-running this never overwrites an admin's edits.
$heads = [
    'MO' => 'Hon. Maria Elena L. Germar',
    'MAO' => 'Atty. Jhorolar N. Andres',
    'BUDGET' => 'Fernando DG. Cruz',
    'ACCT' => 'Manuel R. Marcial',
    'GSO' => 'Marilyn C. Ocampo',
    'TREAS' => 'Shena Liz P. Alonzo',
    'BPLO' => 'Jemelee John De Silva',
    'MHRMO' => 'Fernando DG. Cruz',
    'MENRO' => 'Mark Anthony Francisco',
    'LCR' => 'Filipina DG. Pascual',
    'MPDO' => 'Mark Anthony Francisco',
    'LYDO' => 'Wilmie Kate Z. Logo',
    'TOUR' => 'Hon. Maria Elena L. Germar',
    'PIO' => 'Hon. Maria Elena L. Germar',
    'ASS' => 'Portia H. German',
    'MDRRMO' => 'Jerry R. Sumbillo',
    'NTMPSO' => 'Hon. Maria Elena L. Germar',
    'SB' => 'Hon. Giulius Ceazar S. Iapino',
    'OSCA' => 'Hon. Maria Elena L. Germar',
    'PDAO' => 'Hon. Maria Elena L. Germar',
    'POP' => 'Hon. Maria Elena L. Germar',
    'NUTR' => 'Christilina B. Torres',
    'MSWDO' => 'Annalyn Joy C. San Pedro',
    'PESO' => 'Atty. Rochelle Marie G. Torres',
    'ENGR' => null,
    'AGRIC' => null,
    'NMH' => 'Dr. Joselito C. Santiago',
    'NC' => 'Dr. Maria Liberty DG. Pascual',
    'MHO' => 'Dr. George Peppard C. Paitim',
];
$pdo->beginTransaction();

foreach ($renames as $old => $new) {
    $pdo->prepare('UPDATE departments SET code = :new WHERE code = :old')->execute(['new' => $new, 'old' => $old]);
}

$upsert = $pdo->prepare(
    'INSERT INTO departments (code, name, sector) VALUES (:code, :name, :sector)
     ON DUPLICATE KEY UPDATE name = VALUES(name), sector = VALUES(sector)'
);
foreach ($offices as [$code, $name, $sector]) {
    $upsert->execute(['code' => $code, 'name' => $name, 'sector' => $sector]);
}

$keep = array_column($offices, 0);
$placeholders = implode(',', array_fill(0, count($keep), '?'));
$stale = $pdo->prepare("SELECT id, code, name FROM departments WHERE code NOT IN ($placeholders)");
$stale->execute($keep);

$inUse = $pdo->prepare(
    'SELECT (SELECT COUNT(*) FROM users WHERE department_id = :id1) + (SELECT COUNT(*) FROM appointments WHERE department_id = :id2)'
);
$delete = $pdo->prepare('DELETE FROM departments WHERE id = :id');
foreach ($stale->fetchAll() as $row) {
    $inUse->execute(['id1' => $row['id'], 'id2' => $row['id']]);
    if ((int) $inUse->fetchColumn() > 0) {
        echo "Kept {$row['code']} ({$row['name']}): still referenced by users/appointments.
";
        continue;
    }
    $delete->execute(['id' => $row['id']]);
}

$setHead = $pdo->prepare('UPDATE departments SET department_head = :head WHERE code = :code AND department_head IS NULL');
foreach ($heads as $code => $head) {
    if ($head !== null) {
        $setHead->execute(['head' => $head, 'code' => $code]);
    }
}

$pdo->commit();
echo 'Departments now: ' . $pdo->query('SELECT COUNT(*) FROM departments')->fetchColumn() . "
";
