<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>COMPLETE DATABASE RESET & SETUP</h2>";

// Create database
if ($conn->query("CREATE DATABASE IF NOT EXISTS notice_board")) {
    echo "✓ Database created successfully<br>";
} else {
    echo "✗ Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("notice_board");

// Drop tables if they exist
$conn->query("DROP TABLE IF EXISTS notices");
$conn->query("DROP TABLE IF EXISTS users");

// Create users table
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "✓ Users table created successfully<br>";
} else {
    echo "✗ Error creating users table: " . $conn->error . "<br>";
}

// Create notices table
$sql = "CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_important BOOLEAN DEFAULT FALSE
)";

if ($conn->query($sql)) {
    echo "✓ Notices table created successfully<br>";
} else {
    echo "✗ Error creating notices table: " . $conn->error . "<br>";
}

// Create default admin user
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@notice.com', '$hashed_password', 'Administrator', 'admin'),
('user1', 'user1@notice.com', '$hashed_password', 'John Doe', 'user')";

if ($conn->query($sql)) {
    echo "✓ Default users created successfully<br>";
} else {
    echo "✗ Error creating users: " . $conn->error . "<br>";
}

// Create sample notices
$sql = "INSERT INTO notices (title, content, is_important) VALUES 
('Welcome to Notice Board', 'This is the online notice board system. Admin users can add, edit and delete notices. Regular users can view notices.', 1),
('Important Update', 'System maintenance scheduled for Saturday 2AM-4AM. Service may be unavailable during this time.', 1),
('Holiday Notice', 'Office will be closed on Monday for public holiday.', 0),
('New Features Added', 'We have added user registration and admin features to the notice board.', 0)";

if ($conn->query($sql)) {
    echo "✓ Sample notices created successfully<br>";
} else {
    echo "✗ Error creating notices: " . $conn->error . "<br>";
}

echo "<hr>";
echo "<h3 style='color: green;'>SETUP COMPLETED SUCCESSFULLY! 🎉</h3>";
echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h4>🔑 LOGIN CREDENTIALS:</h4>";
echo "<p><strong>ADMIN Account:</strong><br>Username: <code>admin</code><br>Password: <code>admin123</code></p>";
echo "<p><strong>USER Account:</strong><br>Username: <code>user1</code><br>Password: <code>admin123</code></p>";
echo "</div>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h4>🚀 QUICK START:</h4>";
echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>🔐 Login as Admin</a>";
echo "<a href='index.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>📋 View Notice Board</a>";
echo "<a href='admin.php' style='background: #FF9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;'>⚙️ Go to Admin Panel</a></p>";
echo "</div>";

$conn->close();
?>