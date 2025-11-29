<?php
include 'config.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices Dashboard - Online Notice Board</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: linear-gradient(135deg, #667eea, #894ba2ff);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
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
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            max-width: 500px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .stat-description {
            color: #888;
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .notices-container {
            display: grid;
            gap: 25px;
        }
        .notice-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 6px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .notice-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .notice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .notice-card:hover::before {
            transform: scaleX(1);
        }
        .notice-card.important {
            border-left-color: #ff6b6b;
            background: linear-gradient(135deg, #fff9f9, #ffffff);
        }
        .notice-card.important::before {
            background: linear-gradient(135deg, #ff6b6b, #ff5252);
        }
        .notice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 15px;
        }
        .notice-title {
            flex: 1;
            color: #333;
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        .notice-badge {
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .notice-content {
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
            font-size: 1.05rem;
        }
        .notice-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #888;
            font-size: 0.9rem;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .notice-date {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .no-notices {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .no-notices-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .search-sort {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .sort-options select {
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
        }
        .welcome-text {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .nav-buttons {
                justify-content: center;
            }
            .search-sort {
                flex-direction: column;
            }
            .search-box {
                min-width: 100%;
            }
            .stat-card {
                padding: 20px;
            }
            .stat-number {
                font-size: 2rem;
            }
            .stats-cards {
                max-width: 100%;
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
                <header>
            <div class="header-top">
                <div class="user-welcome">
                    <div class="user-avatar">
                        <?php echo isAdmin() ? '⚙️' : '👤'; ?>
                    </div>
                    <div>
                        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h1>
                        <div class="welcome-text">
                            <?php echo isAdmin() ? 
                                'You are logged in as Administrator' : 
                                'Here are the latest announcements for you'; 
                            ?>
                        </div>
                    </div>
                </div>
                <div class="nav-buttons">
                    <?php if (isAdmin()): ?>
                        <a href="simple_admin.php" class="nav-btn">⚙️ Admin Panel</a>
                        <a href="profile.php" class="nav-btn">👤 Admin Profile</a>
                    <?php else: ?>
                        <a href="profile.php" class="nav-btn">👤 My Profile</a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-btn">🚪 Logout</a>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="stats-cards">
                <?php
                $total_notices = $conn->query("SELECT COUNT(*) as total FROM notices")->fetch_assoc()['total'];
                $important_notices = $conn->query("SELECT COUNT(*) as important FROM notices WHERE is_important = 1")->fetch_assoc()['important'];
                ?>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_notices; ?></div>
                    <div class="stat-label">Total Notices</div>
                    <div class="stat-description">
                        <?php echo isAdmin() ? 'All system announcements' : 'Available announcements'; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $important_notices; ?></div>
                    <div class="stat-label">Important</div>
                    <div class="stat-description">
                        <?php echo isAdmin() ? 'Urgent announcements' : 'Priority notices'; ?>
                    </div>
                </div>
                <?php if (isAdmin()): ?>
                <div class="stat-card">
                    <?php
                    $total_users = $conn->query("SELECT COUNT(*) as users FROM users")->fetch_assoc()['users'];
                    ?>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Users</div>
                    <div class="stat-description">Registered users</div>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Search and Sort -->
        <div class="search-sort">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search notices by title...">
            </div>
            <div class="sort-options">
                <select id="sortSelect">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="important">Important First</option>
                </select>
            </div>
        </div>

        <!-- Notices List -->
        <div class="notices-container" id="noticesContainer">
            <?php
            $sql = "SELECT * FROM notices ORDER BY is_important DESC, created_at DESC";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $important_class = $row['is_important'] ? 'important' : '';
                    echo "<div class='notice-card $important_class' data-title='".strtolower(htmlspecialchars($row['title']))."' data-date='".$row['created_at']."' data-important='".$row['is_important']."'>";
                    echo "<div class='notice-header'>";
                    echo "<h2 class='notice-title'>" . htmlspecialchars($row['title']) . "</h2>";
                    if ($row['is_important']) {
                        echo "<span class='notice-badge'>🚨 IMPORTANT</span>";
                    }
                    echo "</div>";
                    echo "<div class='notice-content'>" . nl2br(htmlspecialchars($row['content'])) . "</div>";
                    echo "<div class='notice-meta'>";
                    echo "<div class='notice-date'>";
                    echo "📅 Posted on " . date('F j, Y \a\t g:i A', strtotime($row['created_at']));
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-notices'>";
                echo "<div class='no-notices-icon'>📭</div>";
                echo "<h2>No Notices Available</h2>";
                echo "<p>There are no notices to display at the moment. Check back later!</p>";
                if (isAdmin()) {
                    echo "<a href='simple_admin.php' class='nav-btn' style='margin-top: 20px; display: inline-block; background: #667eea; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none;'>Add Your First Notice</a>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const notices = document.querySelectorAll('.notice-card');
            
            notices.forEach(notice => {
                const title = notice.getAttribute('data-title');
                if (title.includes(searchTerm)) {
                    notice.style.display = 'block';
                } else {
                    notice.style.display = 'none';
                }
            });
        });

        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function(e) {
            const sortBy = e.target.value;
            const container = document.getElementById('noticesContainer');
            const notices = Array.from(container.querySelectorAll('.notice-card'));
            
            notices.sort((a, b) => {
                if (sortBy === 'newest') {
                    return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
                } else if (sortBy === 'oldest') {
                    return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
                } else if (sortBy === 'important') {
                    return b.getAttribute('data-important') - a.getAttribute('data-important');
                }
                return 0;
            });
            
            // Clear and re-append sorted notices
            container.innerHTML = '';
            notices.forEach(notice => container.appendChild(notice));
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const notices = document.querySelectorAll('.notice-card');
            notices.forEach((notice, index) => {
                notice.style.animationDelay = (index * 0.1) + 's';
                notice.style.animation = 'fadeInUp 0.6s ease-out forwards';
            });
        });
    </script>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .notice-card {
            opacity: 0;
        }
    </style>
</body>
</html>
<?php $conn->close(); ?>