-- ============================================================
-- SQL INJECTION LAB - Table Setup Script
-- FOR EDUCATIONAL PURPOSES ONLY
-- ============================================================

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
-- NOTE: Passwords stored in plaintext intentionally (for lab only)
INSERT INTO users (username, password, role) VALUES
    ('admin',  'admin123',    'admin'),
    ('user1',  'password1',   'user'),
    ('user2',  'letmein',     'user');

-- Verify data
SELECT * FROM users;
