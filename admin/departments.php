<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$activePage = 'departments';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Norzagaray Portal - 27 Departments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>27 LGU Offices & Departments</h1>
        <p>Manage municipal office personnel, department heads, and service status.</p>
      </div>
      <button class="btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Office/Sub-Office</button>
    </div>

    <!-- STATS CARDS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total LGU Offices</p>
          <h2 id="cnt-total">0</h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Active Departments</p>
          <h2 id="cnt-active">0</h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Total Active Staff</p>
          <h2 id="cnt-staff">0</h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-users"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>System Online Rate</p>
          <h2>100%</h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-bolt"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchInput" placeholder="Search department name or office head..." onkeyup="applyFilters()">
        </div>

        <select id="sectorSelect" onchange="applyFilters()">
          <option value="all">All Sectors</option>
          <option value="Executive">Executive & Administrative</option>
          <option value="Finance">Finance & Revenue</option>
          <option value="Social">Social & Health Services</option>
          <option value="Infrastructure">Infrastructure & Environment</option>
          <option value="Public Safety">Public Safety & Legislative</option>
        </select>

        <select id="statusSelect" onchange="applyFilters()">
          <option value="all">All Statuses</option>
          <option value="Active">Active</option>
          <option value="On Leave">On Leave / Closed</option>
        </select>
      </div>

      <!-- DATA TABLE FOR DEPARTMENTS -->
      <div class="table-container">
        <table id="deptTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Department Name</th>
              <th>Sector</th>
              <th>Department Head</th>
              <th>Active Staff</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>

            <!-- EXECUTIVE & ADMINISTRATIVE -->
            <tr>
              <td><strong>MO</strong></td>
              <td>Office of the Municipal Mayor</td>
              <td>Executive</td>
              <td>Hon. Maria Elena Santos</td>
              <td>18 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>VM</strong></td>
              <td>Office of the Vice Mayor & Sanggunian</td>
              <td>Public Safety</td>
              <td>Hon. Juan Dela Cruz</td>
              <td>12 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>LCR</strong></td>
              <td>Local Civil Registrar (LCR)</td>
              <td>Executive</td>
              <td>Engr. Clara Fernando</td>
              <td>10 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>HRMO</strong></td>
              <td>Human Resource Management Office</td>
              <td>Executive</td>
              <td>Ms. Teresa Alonzo</td>
              <td>8 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>MPDO</strong></td>
              <td>Municipal Planning & Dev. Office</td>
              <td>Executive</td>
              <td>Arch. Carlos Mendoza</td>
              <td>9 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>PIO</strong></td>
              <td>Public Information Office</td>
              <td>Executive</td>
              <td>Mr. Mark Villanueva</td>
              <td>5 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>LEGAL</strong></td>
              <td>Municipal Legal Office</td>
              <td>Executive</td>
              <td>Atty. Francisco Baltazar</td>
              <td>4 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>GSO</strong></td>
              <td>General Services Office</td>
              <td>Executive</td>
              <td>Mr. Renato Garcia</td>
              <td>15 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>

            <!-- FINANCE & REVENUE -->
            <tr>
              <td><strong>BPLO</strong></td>
              <td>Business Permits & Licensing Office</td>
              <td>Finance</td>
              <td>Atty. Roberto Cruz</td>
              <td>12 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>TREAS</strong></td>
              <td>Municipal Treasurer's Office</td>
              <td>Finance</td>
              <td>Mr. Gabriel Torres</td>
              <td>11 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>ASS</strong></td>
              <td>Municipal Assessor's Office</td>
              <td>Finance</td>
              <td>Ms. Beatriz Ramos</td>
              <td>9 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>ACCT</strong></td>
              <td>Municipal Accounting Office</td>
              <td>Finance</td>
              <td>CPA Maria Dizon</td>
              <td>10 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>BUDGET</strong></td>
              <td>Municipal Budget Office</td>
              <td>Finance</td>
              <td>Mr. Eduardo Santos</td>
              <td>7 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>

            <!-- SOCIAL & HEALTH SERVICES -->
            <tr>
              <td><strong>MSWDO</strong></td>
              <td>Social Welfare & Development</td>
              <td>Social</td>
              <td>Dr. Teresa Aquino</td>
              <td>15 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>MHO</strong></td>
              <td>Municipal Health Office (MHO)</td>
              <td>Social</td>
              <td>Dr. Manuel Reyes</td>
              <td>22 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>AGRIC</strong></td>
              <td>Municipal Agriculture Office</td>
              <td>Social</td>
              <td>Engr. Danilo Perez</td>
              <td>11 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>OSCA</strong></td>
              <td>Office of Senior Citizens Affairs</td>
              <td>Social</td>
              <td>Mr. Pedro Pascual</td>
              <td>6 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>PDAO</strong></td>
              <td>Persons with Disability Affairs</td>
              <td>Social</td>
              <td>Ms. Elena Roxas</td>
              <td>5 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>PESO</strong></td>
              <td>Public Employment Service Office</td>
              <td>Social</td>
              <td>Mr. Arnel Castro</td>
              <td>8 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>LYDO</strong></td>
              <td>Local Youth Development Office</td>
              <td>Social</td>
              <td>Ms. Nicole Soriano</td>
              <td>4 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>

            <!-- INFRASTRUCTURE & ENVIRONMENT -->
            <tr>
              <td><strong>CEO</strong></td>
              <td>Municipal Engineering Office</td>
              <td>Infrastructure</td>
              <td>Engr. Ramon Mercado</td>
              <td>14 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>MENRO</strong></td>
              <td>Environment & Natural Resources</td>
              <td>Infrastructure</td>
              <td>For. Antonio Reyes</td>
              <td>8 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>TOUR</strong></td>
              <td>Tourism & Cultural Affairs</td>
              <td>Infrastructure</td>
              <td>Ms. Sofia Bautista</td>
              <td>6 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>MEEO</strong></td>
              <td>Economic Enterprises Office (Markets)</td>
              <td>Infrastructure</td>
              <td>Mr. Jaime Tan</td>
              <td>13 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>

            <!-- PUBLIC SAFETY & LEGISLATIVE -->
            <tr>
              <td><strong>MDRRMO</strong></td>
              <td>Disaster Risk Reduction Management</td>
              <td>Public Safety</td>
              <td>Capt. Joseph Domingo</td>
              <td>20 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>POSO</strong></td>
              <td>Public Order & Safety Office</td>
              <td>Public Safety</td>
              <td>Chief Fernando Laurel</td>
              <td>25 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>
            <tr>
              <td><strong>BAPO</strong></td>
              <td>Barangay Affairs & Operations Office</td>
              <td>Public Safety</td>
              <td>Mr. Gabriel Ocampo</td>
              <td>7 Staff</td>
              <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
              <td>
                <button class="btn-action edit" onclick="editDept(this)">Edit</button>
                <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

    </section>

  </main>

  <!-- ADD / EDIT DEPARTMENT MODAL -->
  <div class="modal-overlay" id="deptModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="modalTitle">Department Details</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('deptModal')"></i>
      </div>
      <form id="deptForm" onsubmit="handleDeptSubmit(event)">
        <div class="form-group">
          <label>Department Code</label>
          <input type="text" id="deptCode" placeholder="e.g. MO, BPLO" required>
        </div>

        <div class="form-group">
          <label>Department Name</label>
          <input type="text" id="deptName" placeholder="Enter department name" required>
        </div>

        <div class="form-group">
          <label>Sector Category</label>
          <select id="deptSector" required>
            <option value="Executive">Executive & Administrative</option>
            <option value="Finance">Finance & Revenue</option>
            <option value="Social">Social & Health Services</option>
            <option value="Infrastructure">Infrastructure & Environment</option>
            <option value="Public Safety">Public Safety & Legislative</option>
          </select>
        </div>

        <div class="form-group">
          <label>Department Head</label>
          <input type="text" id="deptHead" placeholder="Name of Department Head" required>
        </div>

        <div class="form-group">
          <label>Active Staff Count</label>
          <input type="number" id="deptStaff" min="0" placeholder="0" required>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('deptModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save Office</button>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-departments.js"></script>
</body>

</html>
