-- Database Schema for Event Management System
-- Database: event_management

CREATE DATABASE IF NOT EXISTS `event_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `event_management`;

-- 1. Events Table
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATETIME NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Registrations Table
CREATE TABLE IF NOT EXISTS `registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `user_email` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_registrations_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Initial Seed Data: Events
INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`) VALUES
(1, 'Global Tech Innovation Summit 2026', 'A premier gathering of industry pioneers, tech leaders, and entrepreneurs discussing AI advancements, cloud architecture, and next-gen engineering.', DATE_ADD(NOW(), INTERVAL 7 DAY), 'Grand Convention Center, Hall A'),
(2, 'Full-Stack Web Dev Hackathon', 'Collaborate, build, and deploy production-ready web applications within 48 hours. Mentors and prize pools included for winning prototypes.', DATE_ADD(NOW(), INTERVAL 14 DAY), 'LevelX Innovation Hub & Online'),
(3, 'Cybersecurity & Ethical Hacking Workshop', 'Hands-on live security testing lab covering modern web vulnerabilities, zero-day threat models, defensive coding, and pen-testing essentials.', DATE_ADD(NOW(), INTERVAL 21 DAY), 'Tech Tower Auditorium 3'),
(4, 'UI/UX Design Masterclass & Portfolio Review', 'Learn human-centered product design principles, design systems in Figma, micro-interactions, and get personalized reviews from lead designers.', DATE_ADD(NOW(), INTERVAL 30 DAY), 'Creative Arts Studio, 5th Floor')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 5. Initial Seed Data: Admin (username: admin | password: admin123)
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$12$A7SKWePxsC./xosxH1gUUeFkoJfSsrWxASMyJT1DA9emcqNV32c0m')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);
