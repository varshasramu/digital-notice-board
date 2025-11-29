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

echo "Connected to MySQL successfully!<br>";

// Create database
if ($conn->query("CREATE DATABASE IF NOT EXISTS notice_board")) {
    echo "Database created/selected successfully!<br>";
} else {
    echo "Error with database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("notice_board");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Users table created successfully!<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

echo "<h3>Setup Complete! <a href='register.php'>Try Registration Now</a></h3>";
?>