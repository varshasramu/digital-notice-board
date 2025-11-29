<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notices - Online Notice Board</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .notice {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .notice.important {
            border-left-color: #ff6b6b;
            background: #fff9f9;
        }
        .notice h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .notice p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .notice-date {
            color: #888;
            font-size: 0.9rem;
        }
        .important-badge {
            background: #ff6b6b;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <header>
            <h1>📋 All Notices</h1>
            <p>Latest announcements and updates</p>
        </header>

        <?php
        $sql = "SELECT * FROM notices ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $important_class = $row['is_important'] ? 'important' : '';
                echo "<div class='notice $important_class'>";
                echo "<h3>" . htmlspecialchars($row['title']);
                if ($row['is_important']) {
                    echo "<span class='important-badge'>IMPORTANT</span>";
                }
                echo "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
                echo "<div class='notice-date'>Posted: " . date('F j, Y g:i A', strtotime($row['created_at'])) . "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No notices available.</p>";
        }
        ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>