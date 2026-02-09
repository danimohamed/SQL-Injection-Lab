-- ============================================================
-- SQL INJECTION LAB - Database Setup Script
-- FOR EDUCATIONAL PURPOSES ONLY - Run on localhost only
-- ============================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS sql_injection_lab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sql_injection_lab;

-- Drop table if it exists (for clean re-runs)
DROP TABLE IF EXISTS users;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB;

-- Insert test users
-- NOTE: Passwords are stored in plaintext intentionally to demonstrate
-- another common vulnerability. In production, ALWAYS hash passwords.
INSERT INTO users (username, password, role) VALUES
    ('admin',  'admin123',    'admin'),
    ('user1',  'password1',   'user'),
    ('user2',  'letmein',     'user');

-- Verify the data
SELECT * FROM users;
