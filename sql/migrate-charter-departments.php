<?php
/**
 * One-time update: every Citizen's Charter service is now linked to a real department (the
 * departments table) instead of carrying a typed office name. Run once via:
 *   php sql/migrate-charter-departments.php
 *
 * - citizens_charter_services.department_id (linked to departments).
 * - The existing services are linked by their old office name (the table below). The old typed name
 *   stays in the office column; the pages show the department's own name.
 * - Fixes the spelling "Hostpital" in the department list.
 *
 * Safe to re-run. It reports any service it could not link; open that service in the
 * Administrator's Citizen's Charter page and choose its office.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

if (!$pdo->query("SHOW COLUMNS FROM citizens_charter_services LIKE 'department_id'")->fetch()) {
    $pdo->exec('ALTER TABLE citizens_charter_services ADD COLUMN department_id INT UNSIGNED NULL AFTER id');
    $pdo->exec('ALTER TABLE citizens_charter_services ADD CONSTRAINT fk_charter_department FOREIGN KEY (department_id) REFERENCES departments(id)');
    echo "citizens_charter_services.department_id added\n";
}

$fixed = $pdo->exec("UPDATE departments SET name = REPLACE(name, 'Hostpital', 'Hospital') WHERE name LIKE '%Hostpital%'");
if ($fixed) {
    echo "Fixed the spelling of Hospital in the department list\n";
}

// The names the charter used before, and the department each one is.
$byOldName = [
    'Business Permits & Licensing Office' => 'BPLO',
    "Municipal Assessor's Office" => 'ASS',
    "Municipal Civil Registrar's Office" => 'LCR',
    'Municipal Engineering Office' => 'ENGR',
    'Municipal Health Office' => 'MHO',
    'Municipal Planning & Development Office' => 'MPDO',
    'Municipal Environment & Natural Resources Office' => 'MENRO',
    'Municipal Social Welfare & Development Office' => 'MSWDO',
    "Municipal Treasurer's Office" => 'TREAS',
    'Norzagaray Municipal Hospital' => 'NMH',
];
$link = $pdo->prepare('UPDATE citizens_charter_services s JOIN departments d ON d.code = :code SET s.department_id = d.id WHERE s.office = :office AND s.department_id IS NULL');
$linked = 0;
foreach ($byOldName as $office => $code) {
    $link->execute(['code' => $code, 'office' => $office]);
    $linked += $link->rowCount();
}
// A service whose typed name is already exactly a department's name.
$linked += (int) $pdo->exec('UPDATE citizens_charter_services s JOIN departments d ON d.name = s.office SET s.department_id = d.id WHERE s.department_id IS NULL');

$left = $pdo->query('SELECT office, COUNT(*) AS n FROM citizens_charter_services WHERE department_id IS NULL GROUP BY office')->fetchAll();
echo "Linked $linked service(s) to their department\n";
foreach ($left as $row) {
    echo "NOT linked: \"{$row['office']}\" ({$row['n']} service(s)) - choose its office in the Citizen's Charter page\n";
}
echo "Charter departments ready\n";
