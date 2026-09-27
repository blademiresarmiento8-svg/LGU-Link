-- LGU-Link database schema
-- Import this file via phpMyAdmin, or run:
--   "C:\xampp\mysql\bin\mysql.exe" -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS lgu_link CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lgu_link;

-- The 27 LGU offices/departments (shown statically on admin/departments.php
-- today; this table is the real source of truth for office-account
-- assignment and appointment routing). Seeded once via INSERT IGNORE below.
CREATE TABLE IF NOT EXISTS departments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  sector VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO departments (code, name, sector) VALUES
  ('MO', 'Office of the Municipal Mayor', 'Executive'),
  ('VM', 'Office of the Vice Mayor & Sanggunian', 'Public Safety'),
  ('LCR', 'Local Civil Registrar (LCR)', 'Executive'),
  ('HRMO', 'Human Resource Management Office', 'Executive'),
  ('MPDO', 'Municipal Planning & Dev. Office', 'Executive'),
  ('PIO', 'Public Information Office', 'Executive'),
  ('LEGAL', 'Municipal Legal Office', 'Executive'),
  ('GSO', 'General Services Office', 'Executive'),
  ('BPLO', 'Business Permits & Licensing Office', 'Finance'),
  ('TREAS', 'Municipal Treasurer''s Office', 'Finance'),
  ('ASS', 'Municipal Assessor''s Office', 'Finance'),
  ('ACCT', 'Municipal Accounting Office', 'Finance'),
  ('BUDGET', 'Municipal Budget Office', 'Finance'),
  ('MSWDO', 'Social Welfare & Development', 'Social'),
  ('MHO', 'Municipal Health Office (MHO)', 'Social'),
  ('AGRIC', 'Municipal Agriculture Office', 'Social'),
  ('OSCA', 'Office of Senior Citizens Affairs', 'Social'),
  ('PDAO', 'Persons with Disability Affairs', 'Social'),
  ('PESO', 'Public Employment Service Office', 'Social'),
  ('LYDO', 'Local Youth Development Office', 'Social'),
  ('CEO', 'Municipal Engineering Office', 'Infrastructure'),
  ('MENRO', 'Environment & Natural Resources', 'Infrastructure'),
  ('TOUR', 'Tourism & Cultural Affairs', 'Infrastructure'),
  ('MEEO', 'Economic Enterprises Office (Markets)', 'Infrastructure'),
  ('MDRRMO', 'Disaster Risk Reduction Management', 'Public Safety'),
  ('POSO', 'Public Order & Safety Office', 'Public Safety'),
  ('BAPO', 'Barangay Affairs & Operations Office', 'Public Safety');

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'office', 'user') NOT NULL DEFAULT 'user',
  department_id INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- Default accounts for local testing (change these passwords after first login):
--   admin@norzagaray.gov.ph / Admin@123
--   juan.delacruz@example.com / User@123
INSERT INTO users (full_name, email, password, role) VALUES
  ('System Admin', 'admin@norzagaray.gov.ph', '$2y$10$O4OhYIqM1atikiHf4J4yxeeJg0O4DnyrzGcKATe3tA8b1qHqN7K/6', 'admin'),
  ('Juan Dela Cruz', 'juan.delacruz@example.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', 'user')
ON DUPLICATE KEY UPDATE email = email;

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
