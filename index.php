<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Notice Board</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #333;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background Image with Overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                /* Fallback gradient */
                linear-gradient(135deg, #667eea 0%, #764ba2 100%),
                /* Background image */
                url('https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover;
            background-blend-mode: overlay;
            z-index: -2;
            filter: brightness(0.7) contrast(1.1);
        }
        
        /* Additional overlay for better readability */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }
        
        header {
            text-align: center;
            padding: 80px 20px 60px;
            color: white;
            animation: fadeInDown 1s ease-out;
            position: relative;
        }
        
        .main-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 25px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .title-icon {
            font-size: 4.5rem;
            animation: bounce 2s infinite;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.4));
        }
        
        .title-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        
        .title-main {
            font-size: 5rem;
            font-weight: 900;
            color: #ffffff;
            text-shadow: 
                3px 3px 0px rgba(0,0,0,0.8),
                6px 6px 0px rgba(0, 0, 0, 0.4);
            letter-spacing: 2px;
            line-height: 1;
            position: relative;
            display: inline-block;
            -webkit-text-stroke: 1px rgba(0,0,0,0.3);
        }
        
        .title-main::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 5%;
            width: 90%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #ffffff, transparent);
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        .title-sub {
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            text-shadow: 
                2px 2px 0px rgba(0,0,0,0.6),
                4px 4px 0px rgba(0, 0, 0, 0.3);
            letter-spacing: 3px;
            text-transform: uppercase;
            background: rgba(0,0,0,0.4);
            padding: 8px 25px;
            border-radius: 30px;
            border: 2px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        
        .title-sparkle {
            font-size: 3.5rem;
            animation: sparkle 3s infinite;
            filter: drop-shadow(0 0 15px rgba(255,255,255,0.5));
        }
        
        @keyframes sparkle {
            0%, 100% { 
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
            25% { 
                opacity: 0.7;
                transform: scale(1.2) rotate(90deg);
            }
            50% { 
                opacity: 1;
                transform: scale(1) rotate(180deg);
            }
            75% { 
                opacity: 0.7;
                transform: scale(1.2) rotate(270deg);
            }
        }
        
        .tagline {
            font-size: 1.5rem;
            margin-bottom: 40px;
            font-weight: 600;
            color: #ffffff;
            text-shadow: 
                1px 1px 0px rgba(0,0,0,0.6),
                2px 2px 0px rgba(0, 0, 0, 0.3);
            background: rgba(0,0,0,0.4);
            padding: 15px 35px;
            border-radius: 50px;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .access-boxes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 40px 0;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .access-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 35px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 
                0 15px 30px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255,255,255,0.4);
            position: relative;
            overflow: hidden;
        }
        
        .access-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.6s;
        }
        
        .access-box:hover::before {
            left: 100%;
        }
        
        .access-box:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.2),
                0 0 0 1px rgba(255,255,255,0.4);
        }
        
        .access-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
            animation: bounce 2s infinite;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .access-box h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 600;
            background: linear-gradient(45deg, #2c3e50, #34495e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .access-box p {
            color: #7f8c8d;
            margin-bottom: 25px;
            line-height: 1.5;
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .btn {
            padding: 14px 30px;
            font-size: 1.1rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin: 12px 0;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6fd8, #6a42a0);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .user-box {
            border-top: 5px solid #28a745;
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248, 255, 249, 0.98));
        }
        
        .admin-box {
            border-top: 5px solid #ff6b6b;
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255, 248, 248, 0.98));
        }
        
        .user-icon {
            color: #28a745;
            text-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .admin-icon {
            color: #ff6b6b;
            text-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        footer {
            text-align: center;
            padding: 60px 20px;
            color: white;
            margin-top: 80px;
            border-top: 1px solid rgba(255,255,255,0.3);
            animation: fadeIn 1s ease-out 0.6s both;
            backdrop-filter: blur(10px);
            background: rgba(0,0,0,0.2);
            border-radius: 20px;
            margin: 80px 20px 0;
        }
        
        footer p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
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
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .access-boxes {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .main-title {
                flex-direction: column;
                gap: 15px;
            }
            
            .title-main {
                font-size: 3.5rem;
            }
            
            .title-sub {
                font-size: 1.3rem;
            }
            
            .access-box {
                padding: 30px 25px;
            }
            
            .access-icon {
                font-size: 3.5rem;
            }
            
            .access-box h2 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .title-main {
                font-size: 2.8rem;
            }
            
            .access-box {
                padding: 25px 20px;
            }
            
            .btn {
                padding: 12px 25px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="main-title">
                <span class="title-icon">📢</span>
                <h1 class="title-text">
                    <span class="title-main">Online Notice Board</span>
                    <span class="title-sub">Stay Connected, Stay Informed</span>
                </h1>
                <div class="title-sparkle">✨</div>
            </div>
            <p class="tagline">Your Gateway to Important Announcements and Updates</p>
        </header>

        <div class="access-boxes">
            <!-- User Access Box - ONLY View Notices -->
            <div class="access-box user-box">
                <div class="access-icon user-icon">👤</div>
                <h2>User Access</h2>
                <p>View all public notices and announcements instantly</p>
                <a href="view_notices.php" class="btn btn-primary">📋 View All Notices</a>
            </div>

            <!-- Admin Access Box - Login + Admin Panel + Profile -->
            <div class="access-box admin-box">
                <div class="access-icon admin-icon">⚙️</div>
                <h2>Admin Access</h2>
                <p>Manage notices and system settings with full control</p>
                
                <!-- Always show Admin Login -->
                <a href="login.php" class="btn btn-secondary">Admin Login</a>
                
                <!-- Show Admin Panel and Profile only if admin is logged in -->
                <?php if (isLoggedIn() && isAdmin()): ?>
                    <a href="simple_admin.php" class="btn btn-secondary">Admin Panel</a>
                    <a href="profile.php" class="btn btn-secondary">Admin Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            <p>&copy; 2025 Online Notice Board. All rights reserved.</p>
            <p style="margin-top: 10px; opacity: 0.9;">Stay connected, stay informed! 📢</p>
        </footer>
    </div>
</body>
</html>