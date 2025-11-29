<?php
session_start();
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

echo "<h2>🔐 LOGIN DEBUG</h2>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = 'admin';
    $password = 'admin123';
    
    echo "Testing login for: $username<br>";
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo "✓ User found in database<br>";
        echo "User ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
        echo "Password hash: " . $user['password'] . "<br>";
        
        if (password_verify($password, $user['password'])) {
            echo "✅ PASSWORD VERIFICATION: SUCCESS<br>";
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            echo "✅ SESSION VARIABLES SET:<br>";
            echo "user_id: " . $_SESSION['user_id'] . "<br>";
            echo "username: " . $_SESSION['username'] . "<br>";
            echo "user_role: " . $_SESSION['user_role'] . "<br>";
            echo "full_name: " . $_SESSION['full_name'] . "<br>";
            
            echo "<hr>";
            echo "<h3 style='color: green;'>✅ LOGIN SUCCESSFUL!</h3>";
            echo "<p><a href='simple_admin.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a></p>";
            
        } else {
            echo "❌ PASSWORD VERIFICATION: FAILED<br>";
        }
    } else {
        echo "❌ User not found in database<br>";
    }
    
    $stmt->close();
}

$conn->close();
?>