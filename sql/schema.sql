-- LGU-Link database schema
-- Import this file via phpMyAdmin, or run:
--   "C:\xampp\mysql\bin\mysql.exe" -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS lgu_link CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lgu_link;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default accounts for local testing (change these passwords after first login):
--   admin@norzagaray.gov.ph / Admin@123
--   juan.delacruz@example.com / User@123
INSERT INTO users (full_name, email, password, role) VALUES
  ('System Admin', 'admin@norzagaray.gov.ph', '$2y$10$O4OhYIqM1atikiHf4J4yxeeJg0O4DnyrzGcKATe3tA8b1qHqN7K/6', 'admin'),
  ('Juan Dela Cruz', 'juan.delacruz@example.com', '$2y$10$Ok1.2eMQqL8t5VcCSHq1t.YaW6oy9TOtD7WV2vgBmPOwW.O4zz3JO', 'user')
ON DUPLICATE KEY UPDATE email = email;

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
