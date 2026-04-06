-- ============================================================
--  student_events.sql
--  Import this file into phpMyAdmin to set up the database
--  Steps: phpMyAdmin → Import → Choose file → Go
-- ============================================================

-- 1. Create database
CREATE DATABASE IF NOT EXISTS `student_events`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `student_events`;

-- 2. Create registrations table
CREATE TABLE IF NOT EXISTS `registrations` (
  `id`                  INT          UNSIGNED NOT NULL AUTO_INCREMENT,

  -- Personal Info
  `first_name`          VARCHAR(80)  NOT NULL,
  `last_name`           VARCHAR(80)  NOT NULL,
  `email`               VARCHAR(180) NOT NULL,
  `phone`               VARCHAR(20)  DEFAULT NULL,
  `dob`                 DATE         DEFAULT NULL,
  `gender`              ENUM('Male','Female','Other') DEFAULT NULL,

  -- Academic Info
  `student_id`          VARCHAR(30)  NOT NULL,
  `department`          VARCHAR(100) NOT NULL,
  `year_sem`            VARCHAR(50)  NOT NULL,
  `cgpa`                VARCHAR(20)  DEFAULT NULL,
  `advisor`             VARCHAR(120) DEFAULT NULL,

  -- Event Info
  `event_name`          VARCHAR(150) NOT NULL,
  `event_date`          DATE         NOT NULL,
  `participation_type`  ENUM('Individual','Team') NOT NULL,
  `team_name`           VARCHAR(100) DEFAULT NULL,
  `interests`           VARCHAR(255) DEFAULT NULL,  -- comma-separated
  `remarks`             TEXT         DEFAULT NULL,

  -- Upload
  `id_proof_path`       VARCHAR(300) DEFAULT NULL,

  -- Meta
  `registered_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE  KEY `uq_student_event` (`student_id`, `event_name`),   -- prevent duplicate registration
  INDEX   `idx_email`       (`email`),
  INDEX   `idx_event_name`  (`event_name`),
  INDEX   `idx_department`  (`department`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Stores student event registrations';


-- ============================================================
--  3. Optional: seed a few sample records for testing
-- ============================================================
INSERT INTO `registrations`
  (first_name, last_name, email, phone, dob, gender,
   student_id, department, year_sem, cgpa, advisor,
   event_name, event_date, participation_type, team_name,
   interests, remarks, registered_at)
VALUES
  ('Arjun',   'Sharma',   'arjun@uni.edu',  '9876543210', '2003-05-14', 'Male',
   'STU2024001', 'Computer Science',       '3rd Year – Sem 5', '8.7', 'Prof. Mehta',
   'Hackathon 24H', '2025-02-20', 'Team', 'Code Wizards',
   'Competition, Workshop', 'Need vegetarian meals', NOW()),

  ('Priya',   'Patel',    'priya@uni.edu',  '9988776655', '2004-11-03', 'Female',
   'STU2024002', 'Information Technology', '2nd Year – Sem 3', '9.1', 'Dr. Joshi',
   'Tech Symposium 2025', '2025-03-10', 'Individual', NULL,
   'Seminar, Networking', NULL, NOW()),

  ('Rohan',   'Verma',    'rohan@uni.edu',  NULL,          '2002-08-22', 'Male',
   'STU2023015', 'Electronics & Comm.',    '4th Year – Sem 7', '7.5', NULL,
   'Workshop on AI & ML', '2025-04-05', 'Individual', NULL,
   'Workshop', 'Hearing impaired – need front seat', NOW());


-- ============================================================
--  4. Useful admin views
-- ============================================================

-- All registrations newest-first
CREATE OR REPLACE VIEW `v_all_registrations` AS
SELECT
    r.id,
    CONCAT(r.first_name, ' ', r.last_name) AS full_name,
    r.email, r.phone,
    r.student_id, r.department, r.year_sem,
    r.event_name, r.event_date,
    r.participation_type, r.team_name,
    r.interests, r.remarks,
    r.registered_at
FROM `registrations` r
ORDER BY r.registered_at DESC;

-- Count per event
CREATE OR REPLACE VIEW `v_event_counts` AS
SELECT
    event_name,
    event_date,
    COUNT(*) AS total_registrations,
    SUM(participation_type = 'Individual') AS individual_count,
    SUM(participation_type = 'Team')       AS team_count
FROM `registrations`
GROUP BY event_name, event_date
ORDER BY event_date;

-- Department-wise summary
CREATE OR REPLACE VIEW `v_department_summary` AS
SELECT
    department,
    COUNT(*) AS total_students,
    COUNT(DISTINCT event_name) AS events_participated
FROM `registrations`
GROUP BY department
ORDER BY total_students DESC;


-- ============================================================
--  5. Create a limited-privilege app user (recommended)
-- ============================================================
-- Run these in phpMyAdmin SQL tab (as root) for production:
--
-- CREATE USER 'events_app'@'localhost' IDENTIFIED BY 'StrongP@ssw0rd!';
-- GRANT SELECT, INSERT, UPDATE ON student_events.* TO 'events_app'@'localhost';
-- FLUSH PRIVILEGES;
--
-- Then update DB_USER / DB_PASS in register.php accordingly.
-- ============================================================
