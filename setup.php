<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Creating Database...</h2>";

$conn->query("CREATE DATABASE IF NOT EXISTS notice_board");
$conn->select_db("notice_board");

$conn->query("DROP TABLE IF EXISTS notices");
$conn->query("DROP TABLE IF EXISTS users");

$conn->query("CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_important BOOLEAN DEFAULT FALSE
)");

$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@notice.com', '$hashed_password', 'Administrator', 'admin'),
('user1', 'user1@notice.com', '$hashed_password', 'John Doe', 'user')");

$conn->query("INSERT INTO notices (title, content, is_important) VALUES 
('Welcome to Notice Board', 'This is your online notice board system.', 1),
('System Maintenance', 'Maintenance scheduled this weekend.', 0)");

echo "<h3 style='color: green;'>✅ Setup Complete!</h3>";
echo "<p><strong>Admin Login:</strong> admin / admin123</p>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";

$conn->close();
?>