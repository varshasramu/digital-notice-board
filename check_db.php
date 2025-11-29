<?php
// check_db.php - Check if database and tables exist
$servername = "localhost";
$username = "root";
$password = "";

echo "<h2>Database Check</h2>";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✓ Connected to MySQL server<br>";

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE 'notice_board'");
if ($result->num_rows > 0) {
    echo "✓ Database 'notice_board' exists<br>";
    
    // Select database
    $conn->select_db("notice_board");
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✓ Table 'users' exists<br>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE users");
        echo "<h3>Users Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "✗ Table 'users' does not exist<br>";
    }
    
    // Check if notices table exists
    $result = $conn->query("SHOW TABLES LIKE 'notices'");
    if ($result->num_rows > 0) {
        echo "✓ Table 'notices' exists<br>";
    } else {
        echo "✗ Table 'notices' does not exist<br>";
    }
} else {
    echo "✗ Database 'notice_board' does not exist<br>";
}

$conn->close();

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='setup.php'>Run Setup</a> - Create database and tables</li>";
echo "<li><a href='register.php'>Try Registration</a> - Test user registration</li>";
echo "<li><a href='login.php'>Test Login</a> - Test user login</li>";
echo "</ul>";
?>