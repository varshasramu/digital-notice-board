<?php
include 'config.php';
requireLogin();
requireAdmin();

// Get admin profile
$admin_profile = getUserProfile($conn, $_SESSION['user_id']);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new notice
    if (isset($_POST['add_notice'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $is_important = isset($_POST['is_important']) ? 1 : 0;
        
        if (!empty($title) && !empty($content)) {
            $sql = "INSERT INTO notices (title, content, is_important) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $title, $content, $is_important);
            
            if ($stmt->execute()) {
                $message = "Notice added successfully!";
            } else {
                $error = "Error adding notice: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error = "Title and content are required!";
        }
    }
    
    // Update notice
    if (isset($_POST['update_notice'])) {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $is_important = isset($_POST['is_important']) ? 1 : 0;
        
        if (!empty($title) && !empty($content)) {
            $sql = "UPDATE notices SET title = ?, content = ?, is_important = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $title, $content, $is_important, $id);
            
            if ($stmt->execute()) {
                $message = "Notice updated successfully!";
            } else {
                $error = "Error updating notice: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error = "Title and content are required!";
        }
    }
    
    // Delete notice
    if (isset($_POST['delete_notice'])) {
        $id = $_POST['id'];
        
        $sql = "DELETE FROM notices WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Notice deleted successfully!";
        } else {
            $error = "Error deleting notice: " . $conn->error;
        }
        $stmt->close();
    }
}

// Get notice for editing
$edit_notice = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT * FROM notices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_notice = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Online Notice Board</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .edit-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }
        
        .edit-btn:hover {
            background: linear-gradient(135deg, #45a049 0%, #4CAF50 100%);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .close-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }
        
        .close-modal:hover {
            color: #000;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1>Admin Panel</h1>
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Administrator)</p>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="user-profile">
                        <img src="images/profiles/<?php echo $_SESSION['profile_picture']; ?>" 
                             alt="Profile" class="profile-pic-small">
                        <span>Admin</span>
                    </div>
                    <a href="index.php" class="admin-btn">View Notice Board</a>
                    <a href="profile.php" class="admin-btn">My Profile</a>
                    <a href="logout.php" class="admin-btn logout-btn">Logout</a>
                </div>
            </div>
        </header>
        
        <?php if ($message): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-container">
            <?php
            // Get statistics
            $total_notices = $conn->query("SELECT COUNT(*) as total FROM notices")->fetch_assoc()['total'];
            $important_notices = $conn->query("SELECT COUNT(*) as important FROM notices WHERE is_important = 1")->fetch_assoc()['important'];
            $total_users = $conn->query("SELECT COUNT(*) as users FROM users")->fetch_assoc()['users'];
            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_notices; ?></div>
                <div class="stat-label">Total Notices</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $important_notices; ?></div>
                <div class="stat-label">Important Notices</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
        </div>
        
        <div class="admin-panel">
            <!-- Add/Edit Notice Form -->
            <div class="add-notice-form">
                <h2><?php echo $edit_notice ? 'Edit Notice' : 'Add New Notice'; ?></h2>
                <form method="POST">
                    <?php if ($edit_notice): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_notice['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" 
                               value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['title']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea id="content" name="content" rows="5" required><?php echo $edit_notice ? htmlspecialchars($edit_notice['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_important" value="1" 
                                   <?php echo $edit_notice && $edit_notice['is_important'] ? 'checked' : ''; ?>>
                            Mark as Important
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <?php if ($edit_notice): ?>
                            <button type="submit" name="update_notice" class="btn">Update Notice</button>
                            <a href="admin.php" class="btn" style="background: #666; margin-left: 10px;">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_notice" class="btn">Add Notice</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Existing Notices -->
            <div class="existing-notices">
                <h2>Manage Notices</h2>
                <?php
                $sql = "SELECT * FROM notices ORDER BY created_at DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='notice-admin'>";
                        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
                        echo "<div class='notice-meta'>";
                        echo "<span>Posted: " . date('M j, Y g:i A', strtotime($row['created_at'])) . "</span>";
                        if ($row['is_important']) {
                            echo "<span class='important-badge'>Important</span>";
                        }
                        echo "<div class='admin-actions'>";
                        echo "<a href='admin.php?edit=" . $row['id'] . "' class='btn edit-btn'>Edit</a>";
                        echo "<form method='POST' class='delete-form'>";
                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                        echo "<button type='submit' name='delete_notice' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this notice?\")'>Delete</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No notices available.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Function to confirm deletion
        function confirmDelete() {
            return confirm("Are you sure you want to delete this notice?");
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                window.location.href = 'admin.php';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>