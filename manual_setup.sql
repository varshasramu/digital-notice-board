-- manual_setup.sql
-- Run this in phpMyAdmin

CREATE DATABASE IF NOT EXISTS notice_board;
USE notice_board;

DROP TABLE IF EXISTS notices;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    profile_picture VARCHAR(255) DEFAULT 'default_profile.png',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_important BOOLEAN DEFAULT FALSE
);

INSERT INTO notices (title, content, is_important) VALUES 
('Welcome to Online Notice Board', 'This is your online notice board system. You can add important announcements and notices here. The admin panel allows you to manage all notices easily.', 1),
('System Maintenance Schedule', 'There will be scheduled maintenance on Saturday from 2:00 AM to 4:00 AM. The system might be temporarily unavailable during this period.', 0),
('Holiday Announcement', 'The office will remain closed next Monday for a public holiday. All operations will resume on Tuesday as usual.', 1);

INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@noticeboard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'user');