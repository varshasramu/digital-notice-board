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

// MANUALLY SET ADMIN SESSION (BYPASS LOGIN)
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';
$_SESSION['full_name'] = 'Administrator';

echo "🔧 MANUAL ADMIN SESSION SET:<br>";
echo "user_id: " . $_SESSION['user_id'] . "<br>";
echo "username: " . $_SESSION['username'] . "<br>";
echo "user_role: " . $_SESSION['user_role'] . "<br>";
echo "full_name: " . $_SESSION['full_name'] . "<br>";

$message = '';
$error = '';

// Handle Add Notice
if (isset($_POST['add_notice'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_important = isset($_POST['is_important']) ? 1 : 0;
    
    $sql = "INSERT INTO notices (title, content, is_important) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $is_important);
    
    if ($stmt->execute()) {
        $message = "✅ Notice added successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
    $stmt->close();
}

// Handle Delete Notice
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM notices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "✅ Notice deleted successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
    $stmt->close();
}

// Get all notices
$notices_result = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PANEL - NO CHECK</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); overflow: hidden; }
        header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .admin-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .form-section, .notices-section { background: #f8f9fa; padding: 25px; border-radius: 10px; border: 2px solid #e9ecef; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 16px; }
        textarea { height: 150px; resize: vertical; }
        .btn { background: #667eea; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #5a6fd8; }
        .notice-item { background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #667eea; }
        .notice-item.important { border-left-color: #ff6b6b; background: #fff9f9; }
        .delete-btn { background: #ff6b6b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }
        .delete-btn:hover { background: #ff5252; }
        .important-badge { background: #ff6b6b; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        @media (max-width: 768px) { .admin-panel { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>⚙️ ADMIN PANEL - NO ACCESS CHECKS</h1>
            <p>Session manually set - Full access granted</p>
        </header>

        <div class="content">
            <?php if ($message): ?>
                <div class="alert success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="admin-panel">
                <!-- Add Notice Form -->
                <div class="form-section">
                    <h2>📝 ADD NEW NOTICE</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="title">Notice Title:</label>
                            <input type="text" id="title" name="title" placeholder="Enter notice title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Notice Content:</label>
                            <textarea id="content" name="content" placeholder="Enter notice content" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_important" value="1">
                                Mark as Important Notice
                            </label>
                        </div>
                        
                        <button type="submit" name="add_notice" class="btn">➕ Add Notice</button>
                    </form>
                </div>

                <!-- Manage Notices -->
                <div class="notices-section">
                    <h2>📋 MANAGE NOTICES</h2>
                    <p>Total Notices: <strong><?php echo $notices_result->num_rows; ?></strong></p>
                    
                    <?php if ($notices_result->num_rows > 0): ?>
                        <?php while($notice = $notices_result->fetch_assoc()): ?>
                            <div class="notice-item <?php echo $notice['is_important'] ? 'important' : ''; ?>">
                                <h3><?php echo htmlspecialchars($notice['title']); ?>
                                    <?php if ($notice['is_important']): ?>
                                        <span class="important-badge">IMPORTANT</span>
                                    <?php endif; ?>
                                </h3>
                                <p><?php echo nl2br(htmlspecialchars($notice['content'])); ?></p>
                                <div style="color: #666; font-size: 0.9em; margin-top: 10px;">
                                    Posted: <?php echo date('M j, Y g:i A', strtotime($notice['created_at'])); ?>
                                </div>
                                <a href="?delete=<?php echo $notice['id']; ?>" class="delete-btn" 
                                   onclick="return confirm('Delete this notice?')">
                                   🗑️ Delete
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No notices found. Add your first notice!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 10px;">
                <h3>🔧 Debug Info:</h3>
                <p>Session user_id: <?php echo $_SESSION['user_id']; ?></p>
                <p>Session user_role: <?php echo $_SESSION['user_role']; ?></p>
                <p>Session username: <?php echo $_SESSION['username']; ?></p>
                <p><a href="logout.php" style="color: #667eea;">Logout</a> | <a href="index.php" style="color: #667eea;">View Notice Board</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>