<?php
include 'config.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    
    // Update user in database
    $sql = "UPDATE users SET full_name = ?, email = ?, username = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $email, $username, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['full_name'] = $full_name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        $message = "✅ Profile updated successfully!";
    } else {
        $error = "❌ Error updating profile: " . $conn->error;
    }
    $stmt->close();
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current user data to verify password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $message = "✅ Password changed successfully!";
                } else {
                    $error = "❌ Error changing password: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error = "❌ New password must be at least 6 characters long!";
            }
        } else {
            $error = "❌ New passwords do not match!";
        }
    } else {
        $error = "❌ Current password is incorrect!";
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Online Notice Board</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .nav-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .nav-btn {
            padding: 12px 25px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .nav-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .profile-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        .profile-info h2 {
            color: #333;
            margin-bottom: 5px;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            background: <?php echo ($user['role'] === 'admin') ? '#ff6b6b' : '#667eea'; ?>;
            color: white;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 5px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        input:disabled {
            background: #f8f9fa;
            color: #666;
            cursor: not-allowed;
        }
        .btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            text-align: left;
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
        .account-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            .nav-buttons {
                justify-content: center;
            }
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>👤 My Profile</h1>
            <p>Manage your account settings and preferences</p>
            <div class="nav-buttons">
                <a href="dashboard.php" class="nav-btn">📋 Dashboard</a>
                <?php if (isAdmin()): ?>
                    <a href="simple_admin.php" class="nav-btn">⚙️ Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-btn">🚪 Logout</a>
            </div>
        </header>

        <div class="profile-content">
            <!-- Profile Information Section -->
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <div class="role-badge">
                            <?php echo ucfirst($user['role']); ?> Account
                        </div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <h3 style="margin-bottom: 20px; color: #333;">Personal Information</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn">💾 Update Profile</button>
                </form>

                <!-- Account Information -->
                <div class="account-info">
                    <h4 style="margin-bottom: 15px; color: #333;">Account Information</h4>
                    <div class="info-item">
                        <span class="info-label">User ID:</span>
                        <span class="info-value">#<?php echo $user['id']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Type:</span>
                        <span class="info-value" style="color: <?php echo ($user['role'] === 'admin') ? '#ff6b6b' : '#667eea'; ?>;">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Status:</span>
                        <span class="info-value" style="color: #28a745;">Active ✅</span>
                    </div>
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="profile-section">
                <h3 style="margin-bottom: 20px; color: #333;">Security Settings</h3>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn">🔒 Change Password</button>
                </form>

                <!-- Role Information -->
                <div style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 10px;">
                    <h4 style="margin-bottom: 15px; color: #333;">
                        <?php echo ($user['role'] === 'admin') ? '⚙️ Administrator Account' : '👤 User Account'; ?>
                    </h4>
                    <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
                        <?php if ($user['role'] === 'admin'): ?>
                            You have <strong>administrator privileges</strong>. You can manage all notices, add new announcements, and delete existing ones through the Admin Panel.
                        <?php else: ?>
                            You have a <strong>standard user account</strong>. You can view all notices and manage your personal profile settings.
                        <?php endif; ?>
                    </p>
                    <?php if ($user['role'] === 'admin'): ?>
                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <a href="simple_admin.php" class="btn" style="padding: 12px 20px; text-decoration: none; display: inline-block;">
                                ⚙️ Go to Admin Panel
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>