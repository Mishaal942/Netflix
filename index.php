<?php
session_start();
require 'db.php';
// Fetch all movies dynamically
$sql = "SELECT * FROM movies ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Clone</title>
    <style>
        /* Reset */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(to bottom, #000000, #1a0a0a);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            background: linear-gradient(180deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 100%);
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        header h1 {
            background: linear-gradient(90deg, #e50914 0%, #ff6b6b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 36px;
            font-weight: 900;
            cursor: pointer;
            letter-spacing: -1px;
            transition: all 0.3s ease;
            text-shadow: 0 0 30px rgba(229, 9, 20, 0.5);
        }

        header h1:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 15px rgba(229, 9, 20, 0.8));
        }

        /* Navigation */
        nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        nav a {
            color: #e5e5e5;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            padding: 8px 18px;
            border-radius: 4px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(229, 9, 20, 0.3), transparent);
            transition: left 0.5s ease;
        }

        nav a:hover::before {
            left: 100%;
        }

        nav a:hover {
            color: #fff;
            background: rgba(229, 9, 20, 0.2);
            transform: translateY(-2px);
        }

        /* Section Title */
        h2 {
            margin: 40px 50px 20px;
            font-size: 30px;
            font-weight: 700;
            color: #fff;
            position: relative;
            display: inline-block;
            animation: fadeInUp 0.8s ease-out;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #e50914, transparent);
            border-radius: 2px;
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

        /* Carousel */
        .carousel {
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 25px 50px 40px;
            gap: 20px;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #e50914 #141414;
        }

        .carousel::-webkit-scrollbar {
            height: 8px;
        }

        .carousel::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .carousel::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #e50914, #ff6b6b);
            border-radius: 10px;
            transition: background 0.3s;
        }

        .carousel::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #ff0a16, #ff8585);
        }

        /* Movie Card */
        .movie {
            min-width: 240px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-shrink: 0;
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            position: relative;
            animation: fadeIn 0.6s ease-out backwards;
        }

        .movie:nth-child(1) { animation-delay: 0.1s; }
        .movie:nth-child(2) { animation-delay: 0.2s; }
        .movie:nth-child(3) { animation-delay: 0.3s; }
        .movie:nth-child(4) { animation-delay: 0.4s; }
        .movie:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .movie::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(229, 9, 20, 0) 0%, rgba(229, 9, 20, 0.3) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
            pointer-events: none;
        }

        .movie:hover::before {
            opacity: 1;
        }

        .movie:hover {
            transform: scale(1.15) translateY(-10px);
            box-shadow: 0 20px 40px rgba(229, 9, 20, 0.4),
                        0 0 50px rgba(229, 9, 20, 0.2);
            z-index: 10;
        }

        .movie img {
            width: 100%;
            height: 340px;
            object-fit: cover;
            display: block;
            transition: all 0.4s ease;
            filter: brightness(0.9);
        }

        .movie:hover img {
            filter: brightness(1.1) contrast(1.1);
        }

        .movie p {
            text-align: center;
            padding: 15px 10px;
            font-weight: 600;
            font-size: 16px;
            background: linear-gradient(180deg, #0f0f0f 0%, #000000 100%);
            position: relative;
            z-index: 2;
            letter-spacing: 0.5px;
            text-transform: capitalize;
        }

        /* Empty State */
        .empty-state {
            margin: 50px;
            padding: 40px;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .empty-state p {
            font-size: 18px;
            color: #999;
        }

        /* Responsive Design */
        @media(max-width: 768px) {
            header { 
                padding: 15px 25px;
                flex-wrap: wrap;
            }
            
            header h1 {
                font-size: 28px;
            }

            nav {
                gap: 10px;
            }

            nav a {
                font-size: 13px;
                padding: 6px 12px;
            }
            
            h2 { 
                margin: 25px 25px 15px; 
                font-size: 24px; 
            }
            
            .carousel { 
                padding: 20px 25px 35px; 
                gap: 15px; 
            }
            
            .movie { 
                min-width: 170px; 
            }
            
            .movie img { 
                height: 250px; 
            }

            .movie p {
                font-size: 14px;
                padding: 12px 8px;
            }
        }

        @media(max-width: 480px) {
            header {
                padding: 12px 15px;
            }

            header h1 {
                font-size: 24px;
            }

            nav a {
                font-size: 12px;
                padding: 5px 10px;
            }

            h2 {
                margin: 20px 15px 10px;
                font-size: 20px;
            }

            .carousel {
                padding: 15px 15px 30px;
                gap: 12px;
            }

            .movie {
                min-width: 140px;
            }

            .movie img {
                height: 210px;
            }

            .movie p {
                font-size: 13px;
                padding: 10px 6px;
            }
        }
    </style>
</head>
<body>
<header>
    <h1 onclick="window.location.href='index.php'">NETFLIX</h1>
    <?php if(isset($_SESSION['user_id'])): ?>
        <nav>
            <a href="watchlist.php">Watchlist</a>
            <a href="logout.php">Logout</a>
        </nav>
    <?php else: ?>
        <nav>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        </nav>
    <?php endif; ?>
</header>
<h2>All Movies</h2>
<div class="carousel">
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="movie" onclick="window.location.href='watch.php?id=<?php echo $row['id']; ?>'">
                <img src="<?php echo $row['thumbnail']; ?>" alt="<?php echo $row['title']; ?>">
                <p><?php echo $row['title']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>No movies found.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
