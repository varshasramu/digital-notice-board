<?php
include 'config.php';

// STRICT ADMIN CHECK - No bypass
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DOUBLE SECURITY CHECK
if ($_SESSION['user_role'] !== 'admin') {
    // Log unauthorized access attempt
    error_log("Unauthorized admin access attempt by user: " . $_SESSION['username']);
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial; background: linear-gradient(135deg, #667eea, #764ba2); min-height: 100vh; color: white;'>
            <div style='background: white; padding: 40px; border-radius: 15px; color: #333; max-width: 500px; margin: 50px auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);'>
                <h1 style='color: #ff6b6b; font-size: 2.5rem; margin-bottom: 20px;'>🚫 ACCESS DENIED</h1>
                <p style='font-size: 1.2rem; margin-bottom: 10px;'><strong>Administrator privileges required!</strong></p>
                <p style='color: #666; margin-bottom: 30px;'>Please login with an admin account to access this page.</p>
                <div style='display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;'>
                    <a href='login.php' style='background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: 500;'>
                        🔐 Go to Login
                    </a>
                    <a href='index.php' style='background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: 500;'>
                        🏠 Back to Home
                    </a>
                    <a href='view_notices.php' style='background: #ff6b6b; color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: 500;'>
                        📋 View Notices
                    </a>
                </div>
            </div>
        </div>
    ");
}

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
    <title>Admin Panel - Notice Board</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .admin-info {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .nav-buttons {
            margin: 20px 0;
        }
        .nav-buttons a {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .nav-buttons a:hover {
            background: rgba(255,255,255,0.3);
        }
        .content {
            padding: 30px;
        }
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .admin-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .form-section, .notices-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            height: 150px;
            resize: vertical;
        }
        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .notice-item {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .notice-item.important {
            border-left-color: #ff6b6b;
            background: #fff9f9;
        }
        .notice-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .delete-btn {
            background: #ff6b6b;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .delete-btn:hover {
            background: #ff5252;
        }
        .important-badge {
            background: #ff6b6b;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .admin-panel {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>⚙️ ADMIN PANEL</h1>
            <div class="admin-info">
                <h3>Welcome, <?php echo $_SESSION['full_name']; ?>! (Administrator)</h3>
                <p>You have full control over the notice board</p>
            </div>
            <div class="nav-buttons">
                <a href="index.php">🏠 Home</a>
                <a href="view_notices.php">📋 View Notices</a>
                <a href="dashboard.php">📊 Dashboard</a>
                <a href="profile.php">👤 My Profile</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </header>

        <div class="content">
            <div class="security-notice">
                <strong>🔒 SECURE ADMIN AREA:</strong> All actions are logged and require administrator authentication.
            </div>
            
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
                            <div class="checkbox">
                                <input type="checkbox" id="is_important" name="is_important" value="1">
                                <label for="is_important">Mark as Important Notice</label>
                            </div>
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
                                <div class="notice-actions">
                                    <a href="?delete=<?php echo $notice['id']; ?>" class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this notice?')">
                                       🗑️ Delete
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No notices found. Add your first notice using the form!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>