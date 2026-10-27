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
  queue_enabled TINYINT(1) NOT NULL DEFAULT 0,
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
  ('NMH', 'Norzagaray Municipal Hospital', 'Social', 'Dr. Joselito C. Santiago'),
  ('MHO', 'Municipal Health Office', 'Social', 'Dr. George Peppard C. Paitim'),
  ('NC', 'Norzagaray College', 'Social', 'Dr. Maria Liberty DG. Pascual');

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  -- Citizens fill these in when they sign up; full_name is composed from them.
  first_name VARCHAR(60) NULL,
  middle_name VARCHAR(60) NULL,
  last_name VARCHAR(60) NULL,
  suffix VARCHAR(10) NULL,
  mobile VARCHAR(20) NULL,
  organization VARCHAR(100) NULL,
  position_title VARCHAR(100) NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  privacy_consent_at DATETIME NULL,
  role ENUM('admin', 'sysadmin', 'office', 'user') NOT NULL DEFAULT 'user',
  department_id INT UNSIGNED NULL,
  -- Suspended accounts can't log in (and are logged out on their next page load).
  status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  suspended_at DATETIME NULL,
  suspended_reason VARCHAR(255) NULL,
  -- Set when the system admin resets a password: forces a new one at next login.
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the synthetic-data loader
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
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_mail_log_created (created_at)
) ENGINE=InnoDB;

-- One-time email codes used during citizen sign-up (hashed; expire after 10 minutes).
CREATE TABLE IF NOT EXISTS email_verifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  code_hash VARCHAR(255) NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  expires_at DATETIME NOT NULL,
  verified_at DATETIME NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX email_verifications_email (email, id),
  INDEX email_verifications_ip (ip_address, created_at)
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
  organization VARCHAR(100) NULL,
  position_title VARCHAR(100) NULL,
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
  reviewed_at DATETIME NULL, -- when the administrator forwarded or rejected it
  scheduled_at DATETIME NULL, -- when the office set the schedule
  completed_at DATETIME NULL,
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the demo-data loader (includes/demo-data.php)
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
-- keywords/requirements/steps are stored one item per line.
CREATE TABLE IF NOT EXISTS citizens_charter_services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NULL, -- the office this service belongs to; its name is what citizens see
  office VARCHAR(150) NOT NULL, -- the name typed in the past; kept so old links still work
  title VARCHAR(200) NOT NULL,
  who_may_avail VARCHAR(500) NULL, -- who can apply, from the Citizen's Charter
  keywords TEXT NOT NULL,
  requirements TEXT NOT NULL,
  steps TEXT NULL, -- how to apply, one step per line
  fee VARCHAR(255) NOT NULL,
  processing_time VARCHAR(255) NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_charter_department FOREIGN KEY (department_id) REFERENCES departments(id)
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
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the demo-data loader
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
-- The lists behind the news form's Type and Category dropdowns, edited by the system
-- admin (sysadmin/news-options.php). Each active type is a section on the homepage.
-- Seed them by running:  php sql/migrate-news-options.php
CREATE TABLE IF NOT EXISTS news_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(30) NOT NULL UNIQUE,
  label VARCHAR(40) NOT NULL,
  description VARCHAR(120) NULL,
  icon VARCHAR(30) NOT NULL DEFAULT 'newspaper',
  color VARCHAR(10) NOT NULL DEFAULT 'blue',
  home_limit TINYINT UNSIGNED NOT NULL DEFAULT 6,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE,
  color VARCHAR(10) NOT NULL DEFAULT 'blue',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(60) NOT NULL DEFAULT 'Official Announcement',
  post_type VARCHAR(30) NOT NULL DEFAULT 'news', -- a slug from news_types
  department_id INT UNSIGNED NULL, -- the office that posted it; NULL = the Municipal Government (administrator)
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATE NOT NULL,
  go_live_at DATETIME NULL, -- exact time it becomes visible (NULL = at published_at 00:00)
  show_until DATETIME NULL, -- exact time it stops being visible (NULL = stays up)
  is_activity TINYINT(1) NOT NULL DEFAULT 0,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  venue VARCHAR(150) NULL,
  requirements TEXT NULL, -- one requirement per line
  contact_info VARCHAR(150) NULL,
  notified_at DATETIME NULL, -- when this post's subscriber emails were queued (NULL = not yet)
  archived_at DATETIME NULL, -- when the Municipal Administrator archived (took down) this post (NULL = not archived)
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the demo-data loader
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT news_posts_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Office-written news/announcements. Each draft, with the Municipal Administrator's
-- review, is a submission; approving one creates or updates the live post above.
-- draft -> pending_review -> (awaiting_office <-> pending_review)* -> approved | rejected
CREATE TABLE IF NOT EXISTS news_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NULL, -- set when this proposes changes to an already-live post
  department_id INT UNSIGNED NOT NULL,
  author_user_id INT UNSIGNED NOT NULL,
  status ENUM('draft', 'pending_review', 'awaiting_office', 'approved', 'rejected', 'withdrawn') NOT NULL DEFAULT 'draft',
  post_type VARCHAR(30) NOT NULL DEFAULT 'news',
  category VARCHAR(60) NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NULL,
  is_activity TINYINT(1) NOT NULL DEFAULT 0,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  venue VARCHAR(150) NULL,
  requirements TEXT NULL,
  contact_info VARCHAR(150) NULL,
  go_live_at DATETIME NULL,
  show_until DATETIME NULL,
  reject_reason TEXT NULL,
  decided_at DATETIME NULL,
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the demo-data loader
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX news_submissions_status (status, updated_at),
  FOREIGN KEY (post_id) REFERENCES news_posts(id) ON DELETE SET NULL,
  FOREIGN KEY (department_id) REFERENCES departments(id),
  FOREIGN KEY (author_user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Everything that happened to a submission, with a snapshot of its content at that
-- point, so the office and the administrator both see every change.
CREATE TABLE IF NOT EXISTS news_submission_events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  submission_id INT UNSIGNED NOT NULL,
  actor_user_id INT UNSIGNED NULL,
  actor_name VARCHAR(150) NOT NULL,
  actor_role ENUM('office', 'admin') NOT NULL,
  action VARCHAR(30) NOT NULL,
  note TEXT NULL,
  snapshot MEDIUMTEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX news_events_submission (submission_id, id),
  FOREIGN KEY (submission_id) REFERENCES news_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- QR queueing (sql/migrate-queue.php): per-office queues, services, the staff names shown to
-- the public, and one ticket per person. Add the on/off switch for an office with:
--   ALTER TABLE departments ADD COLUMN queue_enabled TINYINT(1) NOT NULL DEFAULT 0;
-- (already included below for a fresh install). Numbers come from queue_counters, one row per
-- queue per day, incremented atomically inside the ticket-creating transaction.
CREATE TABLE IF NOT EXISTS queue_staff (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_staff_name (department_id, name),
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS queue_services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  letter CHAR(1) NOT NULL,
  staff_id INT UNSIGNED NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_service_name (department_id, name),
  UNIQUE KEY uq_service_letter (department_id, letter),
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  FOREIGN KEY (staff_id) REFERENCES queue_staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- general_flag is 1 for an office's everyday queue and NULL for events, so the unique key
-- allows one general queue per office but any number of events.
CREATE TABLE IF NOT EXISTS queues (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NOT NULL,
  kind ENUM('general', 'event') NOT NULL DEFAULT 'general',
  general_flag TINYINT NULL,
  name VARCHAR(120) NOT NULL,
  prefix VARCHAR(8) NOT NULL,
  max_tickets INT UNSIGNED NULL,
  require_mobile TINYINT(1) NOT NULL DEFAULT 1, -- events: ask for a mobile number
  opens_at DATETIME NULL,
  closes_at DATETIME NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  qr_salt CHAR(32) NULL, -- secret the QR code of the day is made from
  qr_printed_on DATE NULL, -- last day the office printed or showed the code
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_one_general (department_id, general_flag),
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS queue_counters (
  queue_id INT UNSIGNED NOT NULL,
  queue_day DATE NOT NULL,
  last_number INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (queue_id, queue_day),
  FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- phone_key is filled only for event tickets, so the unique key lets a mobile number hold one
-- number per event and the database itself refuses a second. The everyday queue has no such rule.
-- is_priority: the person ticked "I need priority" (or staff marked it); staff choose when to call them.
CREATE TABLE IF NOT EXISTS queue_tickets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  queue_id INT UNSIGNED NOT NULL,
  queue_day DATE NOT NULL,
  number INT UNSIGNED NOT NULL,
  code VARCHAR(20) NOT NULL,
  access_key CHAR(32) NOT NULL,
  service_id INT UNSIGNED NULL,
  service_name VARCHAR(80) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  mobile VARCHAR(20) NULL,
  user_id INT UNSIGNED NULL,
  source ENUM('self', 'staff') NOT NULL DEFAULT 'self',
  status ENUM('waiting', 'called', 'serving', 'done', 'skipped', 'cancelled', 'expired') NOT NULL DEFAULT 'waiting',
  staff_id INT UNSIGNED NULL,
  staff_name VARCHAR(80) NULL,
  is_demo TINYINT(1) NOT NULL DEFAULT 0, -- 1 = made by the demo-data loader
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  called_at DATETIME NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  phone_key VARCHAR(20) NULL,
  is_priority TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_ticket_number (queue_id, queue_day, number),
  UNIQUE KEY uq_ticket_key (access_key),
  UNIQUE KEY uq_event_phone (queue_id, phone_key),
  KEY idx_line (queue_id, queue_day, status, number),
  FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES queue_services(id) ON DELETE SET NULL,
  FOREIGN KEY (staff_id) REFERENCES queue_staff(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Request counters used to slow down repeated logins, chatbot questions and queue numbers
-- (includes/rate-limit.php).
CREATE TABLE IF NOT EXISTS rate_limits (
  bucket VARCHAR(120) NOT NULL PRIMARY KEY,
  hits INT UNSIGNED NOT NULL DEFAULT 0,
  window_start DATETIME NOT NULL
) ENGINE=InnoDB;

-- Email notifications for news and announcements (sql/migrate-news-email.php). Citizens choose which
-- news types they want; posting only fills email_outbox, and a background worker sends from it in
-- small batches. The unique key stops the same post being queued twice for the same person.
CREATE TABLE IF NOT EXISTS news_subscriptions (
  user_id INT UNSIGNED NOT NULL PRIMARY KEY,
  unsubscribe_token CHAR(32) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_unsubscribe_token (unsubscribe_token),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news_subscription_types (
  user_id INT UNSIGNED NOT NULL,
  type_slug VARCHAR(30) NOT NULL,
  PRIMARY KEY (user_id, type_slug),
  KEY idx_type_slug (type_slug),
  FOREIGN KEY (user_id) REFERENCES news_subscriptions(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS email_outbox (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kind VARCHAR(20) NOT NULL DEFAULT 'news',
  ref_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  to_email VARCHAR(150) NOT NULL,
  status ENUM('queued', 'sending', 'sent', 'failed', 'skipped') NOT NULL DEFAULT 'queued',
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  last_error VARCHAR(255) NULL,
  available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sent_at DATETIME NULL,
  UNIQUE KEY uq_outbox_once (kind, ref_id, user_id),
  KEY idx_outbox_due (status, available_at),
  KEY idx_outbox_ref (kind, ref_id, status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
