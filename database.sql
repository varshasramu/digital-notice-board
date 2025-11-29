-- database.sql
CREATE DATABASE IF NOT EXISTS notice_board;
USE notice_board;

CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_important BOOLEAN DEFAULT FALSE
);

-- Insert some sample notices
INSERT INTO notices (title, content, is_important) VALUES 
('Welcome to Notice Board', 'This is your online notice board system. You can add important announcements here.', 1),
('System Maintenance', 'The system will undergo maintenance on Saturday from 2 AM to 4 AM.', 0),
('Holiday Announcement', 'Office will remain closed on Monday for a public holiday.', 1);