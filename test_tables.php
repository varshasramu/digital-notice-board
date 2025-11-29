<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notice_board";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "✓ Users table exists!<br>";
    
    // Show table structure
    $result = $conn->query("DESCRIBE users");
    echo "<h3>Users Table Structure:</h3>";
    while($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']}<br>";
    }
} else {
    echo "✗ Users table does NOT exist!<br>";
}

$conn->close();
?>