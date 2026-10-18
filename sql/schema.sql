-- LGU-Link database schema
-- Import this file via phpMyAdmin, or run:
--   "C:\xampp\mysql\bin\mysql.exe" -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS lgu_link CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lgu_link;

-- The 29 LGU offices/departments, each with its current department head (system admins
-- change it on sysadmin/departments.php). Also the source of truth for
-- office-account assignment and appointment routing. Seeded via INSERT IGNORE below.
CREATE TABLE IF NOT EXISTS departments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  sector VARCHAR(50) NOT NULL,
  department_head VARCHAR(150) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO departments (code, name, sector, department_head) VALUES
  ('MO', 'Mayor''s Office', 'Executive', 'Hon. Maria Elena L. Germar'),
  ('MAO', 'Municipal Administrator''s Office', 'Executive', 'Atty. Jhorolar N. Andres'),
  ('BUDGET', 'Budget Office', 'Finance', 'Fernando DG. Cruz'),
  ('ACCT', 'Accounting Office', 'Finance', 'Manuel R. Marcial'),
  ('TOUR', 'Tourism Office', 'Infrastructure', 'Hon. Maria Elena L. Germar'),
  ('PIO', 'Public Information Office', 'Executive', 'Hon. Maria Elena L. Germar'),
  ('LYDO', 'Local Youth Development Office', 'Social', 'Wilmie Kate Z. Logo'),
  ('MHRMO', 'Municipal Human Resource Management Office', 'Executive', 'Fernando DG. Cruz'),
  ('TREAS', 'Treasurer''s Office', 'Finance', 'Shena Liz P. Alonzo'),
  ('LCR', 'Local Civil Registrar', 'Executive', 'Filipina DG. Pascual'),
  ('MPDO', 'Municipal Planning and Development Office', 'Executive', 'Mark Anthony Francisco'),
  ('MENRO', 'Municipal Environment and Natural Resources Office', 'Infrastructure', 'Mark Anthony Francisco'),
  ('ASS', 'Assessor''s Office', 'Finance', 'Portia H. German'),
  ('BPLO', 'Business Permit and Licensing Office', 'Finance', 'Jemelee John De Silva'),
  ('MDRRMO', 'Municipal Disaster Risk Reduction and Management Office', 'Public Safety', 'Jerry R. Sumbillo'),
  ('NTMPSO', 'Norzagaray Traffic Management and Public Safety Office', 'Public Safety', 'Hon. Maria Elena L. Germar'),
  ('SB', 'Sangguniang Bayan', 'Public Safety', 'Hon. Giulius Ceazar S. Iapino'),
  ('OSCA', 'Office of the Senior Citizens'' Affairs', 'Social', 'Hon. Maria Elena L. Germar'),
  ('PDAO', 'Persons with Disability Affairs Office', 'Social', 'Hon. Maria Elena L. Germar'),
  ('POP', 'Population Office', 'Social', 'Hon. Maria Elena L. Germar'),
  ('NUTR', 'Nutrition Office', 'Social', 'Christilina B. Torres'),
  ('GSO', 'General Services Office', 'Executive', 'Marilyn C. Ocampo'),
  ('MSWDO', 'Municipal Social Welfare and Development Office', 'Social', 'Annalyn Joy C. San Pedro'),
  ('PESO', 'Public Employment Service Office', 'Social', 'Atty. Rochelle Marie G. Torres'),
  ('ENGR', 'Engineering Office', 'Infrastructure', NULL),
  ('AGRIC', 'Agriculture Office', 'Social', NULL),
  ('NMH', 'Norzagaray Municipal Hostpital', 'Social', 'Dr. Joselito C. Santiago'),
  ('MHO', 'Municipal Health Office', 'Social', 'Dr. George Peppard C. Paitim'),
  ('NC', 'Norzagaray College', 'Social', 'Dr. Maria Liberty DG. Pascual');

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'sysadmin', 'office', 'user') NOT NULL DEFAULT 'user',
  department_id INT UNSIGNED NULL,
  -- Suspended accounts can't log in (and are logged out on their next page load).
  status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  suspended_at DATETIME NULL,
  suspended_reason VARCHAR(255) NULL,
  -- Set when the system admin resets a password: forces a new one at next login.
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  -- Each department has at most one office account (NULL for non-office users).
  office_department_id INT UNSIGNED AS (IF(role = 'office', department_id, NULL)) PERSISTENT,
  UNIQUE KEY one_office_account_per_department (office_department_id),
  FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- Default accounts for local testing (change these passwords after first login):
--   admin@norzagaray.gov.ph / Admin@123      (Municipal Administrator)
--   sysadmin@norzagaray.gov.ph / Sysadmin@123 (System Admin)
--   juan.delacruz@example.com / User@123
INSERT INTO users (full_name, email, password, role) VALUES
  ('Municipal Administrator', 'admin@norzagaray.gov.ph', '$2y$10$O4OhYIqM1atikiHf4J4yxeeJg0O4DnyrzGcKATe3tA8b1qHqN7K/6', 'admin'),
  ('System Admin', 'sysadmin@norzagaray.gov.ph', '$2y$10$jVNh/c/2.xFEF1hnXSaR2OCZ3YlDGzxkR9Fubc/2xtc.Fgwi59GRq', 'sysadmin'),
  ('Juan Dela Cruz', 'juan.delacruz@example.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', 'user')
ON DUPLICATE KEY UPDATE email = email;

-- Who did what to which account (password resets, suspensions, account
-- creation/deletion). Names are copied in so the log stays readable after an
-- account is deleted.
CREATE TABLE IF NOT EXISTS audit_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  actor_user_id INT UNSIGNED NULL,
  actor_name VARCHAR(150) NOT NULL,
  action VARCHAR(50) NOT NULL,
  target_user_id INT UNSIGNED NULL,
  target_name VARCHAR(150) NULL,
  details VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Every email the portal tried to send (sent / failed / skipped), so failures
-- are visible on sysadmin/audit-log.php instead of silent.
CREATE TABLE IF NOT EXISTS mail_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  recipient VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  status ENUM('sent', 'failed', 'skipped') NOT NULL,
  error VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Citizen appointment/concern requests. Citizens submit (user/appointments.php,
-- Phase 3); the Municipal Administrator (the 'admin' role) triages and
-- forwards to a department (admin/appointments.php, Phase 4); the assigned
-- office account sets the actual schedule (office/dashboard.php, Phase 5).
CREATE TABLE IF NOT EXISTS appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  category VARCHAR(100) NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  attachment_path VARCHAR(255) NULL,
  attachment_original_name VARCHAR(255) NULL,
  contact_number VARCHAR(30) NOT NULL,
  email VARCHAR(150) NOT NULL,
  preferred_date DATE NULL,
  preferred_time TIME NULL,
  department_id INT UNSIGNED NULL,
  status ENUM('pending_review', 'forwarded', 'scheduled', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending_review',
  scheduled_date DATE NULL,
  scheduled_time TIME NULL,
  admin_notes TEXT NULL,
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  scheduled_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (department_id) REFERENCES departments(id),
  FOREIGN KEY (reviewed_by) REFERENCES users(id),
  FOREIGN KEY (scheduled_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- Citizen's Charter services: requirements/fee/processing time per LGU
-- office, editable by admins (admin/citizens-charter.php). Read by the
-- citizen-facing charter pages, the site search, and the chatbot.
-- keywords/requirements are stored one item per line.
CREATE TABLE IF NOT EXISTS citizens_charter_services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  office VARCHAR(150) NOT NULL,
  title VARCHAR(200) NOT NULL,
  keywords TEXT NOT NULL,
  requirements TEXT NOT NULL,
  fee VARCHAR(255) NOT NULL,
  processing_time VARCHAR(255) NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- After importing this schema, seed the table above from the original
-- data file by running:
--   php sql/migrate-citizens-charter-data.php

-- In-app notifications (the bell in the citizen / administrator / office
-- headers). Created when requests are submitted, forwarded, rejected,
-- scheduled, completed or cancelled; Pusher (optional) just tells an open
-- page to refresh its bell.
CREATE TABLE IF NOT EXISTS notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  message VARCHAR(255) NOT NULL,
  link VARCHAR(255) NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX notifications_user_unread (user_id, is_read, id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Department-to-department meetings. An office requests one; the Municipal
-- Administrator must approve it (pending_approval -> approved | rejected)
-- before the invited departments are told; they can only acknowledge it (no decline).
CREATE TABLE IF NOT EXISTS meetings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  requester_user_id INT UNSIGNED NOT NULL,
  requester_department_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  agenda TEXT NOT NULL,
  venue VARCHAR(150) NULL,
  meeting_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NULL,
  status ENUM('pending_approval', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_approval',
  admin_notes TEXT NULL,
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (requester_user_id) REFERENCES users(id),
  FOREIGN KEY (requester_department_id) REFERENCES departments(id),
  FOREIGN KEY (reviewed_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS meeting_invitees (
  meeting_id INT UNSIGNED NOT NULL,
  department_id INT UNSIGNED NOT NULL,
  response ENUM('pending', 'acknowledged') NOT NULL DEFAULT 'pending',
  response_note VARCHAR(255) NULL,
  responded_at DATETIME NULL,
  PRIMARY KEY (meeting_id, department_id),
  FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- Working calendar (edited on sysadmin/work-schedule.php): which days/hours can
-- be booked, plus holidays. Recurring holidays repeat every year on the same
-- month/day (the year in holiday_date is ignored); others cover holiday_date
-- through end_date. Seed the fixed-date Philippine holidays by running:
--   php sql/migrate-work-schedule.php
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(50) PRIMARY KEY,
  setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS holidays (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  holiday_date DATE NOT NULL,
  end_date DATE NULL,
  name VARCHAR(100) NOT NULL,
  is_recurring TINYINT(1) NOT NULL DEFAULT 0,
  source ENUM('manual', 'api') NOT NULL DEFAULT 'manual', -- 'api' = pulled in by Sync holidays
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Public homepage news/announcements: admin-managed (admin/news.php).
-- Featured posts appear in the homepage's top image carousel; every post
-- appears in the "Latest News" grid below it. Images are uploaded to
-- assets/uploads/news/ and referenced here by relative path.
CREATE TABLE IF NOT EXISTS news_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(60) NOT NULL DEFAULT 'Official Announcement',
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
