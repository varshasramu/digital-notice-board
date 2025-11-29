<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notice_board";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>🔧 Fixing Admin Account</h2>";

// Check if admin exists
$result = $conn->query("SELECT * FROM users WHERE username = 'admin'");
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "✓ Admin user found<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Role: " . $admin['role'] . "<br>";
    
    // Test password
    if (password_verify('admin123', $admin['password'])) {
        echo "✅ Password working: admin123<br>";
    } else {
        echo "❌ Password not working, resetting...<br>";
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$new_hash' WHERE username = 'admin'");
        echo "✅ Password reset to: admin123<br>";
    }
} else {
    echo "❌ Admin user not found! Creating...<br>";
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, full_name, role) VALUES 
    ('admin', 'admin@notice.com', '$hashed_password', 'Administrator', 'admin')";
    
    if ($conn->query($sql)) {
        echo "✅ Admin user created successfully<br>";
    } else {
        echo "❌ Error creating admin: " . $conn->error . "<br>";
    }
}

echo "<hr>";
echo "<h3 style='color: green;'>✅ Fix completed!</h3>";
echo "<p><a href='login.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Now</a></p>";

$conn->close();
?>