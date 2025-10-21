<?php
session_start();
require 'db.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT movies.* FROM movies INNER JOIN watchlist ON movies.id=watchlist.movie_id WHERE watchlist.user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Watchlist - Netflix Clone</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(to bottom, #000000, #0f0a0a);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            background: linear-gradient(180deg, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.75) 100%);
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
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

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 50px 50px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Page Title */
        .page-title {
            text-align: left;
            margin-bottom: 40px;
            font-size: 38px;
            font-weight: 700;
            color: #fff;
            position: relative;
            display: inline-block;
            animation: titleAppear 1s ease-out;
        }

        @keyframes titleAppear {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #e50914, transparent);
            border-radius: 2px;
        }

        .title-count {
            color: #999;
            font-size: 20px;
            font-weight: 400;
            margin-left: 15px;
        }

        /* Movies Grid */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        /* Movie Card */
        .movie {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            position: relative;
            animation: cardAppear 0.6s ease-out backwards;
        }

        .movie:nth-child(1) { animation-delay: 0.1s; }
        .movie:nth-child(2) { animation-delay: 0.15s; }
        .movie:nth-child(3) { animation-delay: 0.2s; }
        .movie:nth-child(4) { animation-delay: 0.25s; }
        .movie:nth-child(5) { animation-delay: 0.3s; }
        .movie:nth-child(6) { animation-delay: 0.35s; }
        .movie:nth-child(7) { animation-delay: 0.4s; }
        .movie:nth-child(8) { animation-delay: 0.45s; }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(30px);
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
            transform: scale(1.08) translateY(-10px);
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
            text-align: center;
            padding: 80px 20px;
            margin-top: 50px;
            background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.8s ease-out;
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

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h2 {
            font-size: 28px;
            color: #e5e5e5;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .empty-state p {
            font-size: 18px;
            color: #999;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .empty-state .browse-btn {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #e50914 0%, #ff1a1a 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4);
        }

        .empty-state .browse-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(229, 9, 20, 0.6);
        }

        /* Responsive Design */
        @media(max-width: 1024px) {
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 25px;
            }
        }

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

            .container {
                padding: 30px 25px;
            }

            .page-title {
                font-size: 28px;
            }

            .title-count {
                font-size: 16px;
                display: block;
                margin-left: 0;
                margin-top: 5px;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 20px;
            }

            .movie img {
                height: 240px;
            }

            .movie p {
                font-size: 14px;
                padding: 12px 8px;
            }

            .empty-state {
                padding: 60px 20px;
            }

            .empty-icon {
                font-size: 60px;
            }

            .empty-state h2 {
                font-size: 22px;
            }

            .empty-state p {
                font-size: 16px;
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

            .container {
                padding: 20px 15px;
            }

            .page-title {
                font-size: 24px;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
            }

            .movie img {
                height: 210px;
            }

            .movie p {
                font-size: 13px;
                padding: 10px 6px;
            }

            .empty-state {
                padding: 40px 15px;
            }

            .empty-icon {
                font-size: 50px;
            }

            .empty-state h2 {
                font-size: 20px;
            }

            .empty-state p {
                font-size: 14px;
            }

            .empty-state .browse-btn {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<header>
    <h1 onclick="window.location.href='index.php'">NETFLIX</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="watchlist.php">Watchlist</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">
        My Watchlist
        <span class="title-count">(<?php echo $result->num_rows; ?> movies)</span>
    </h1>
    
    <?php if($result->num_rows > 0): ?>
        <div class="movies-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="movie" onclick="window.location.href='watch.php?id=<?php echo $row['id']; ?>'">
                    <img src="<?php echo htmlspecialchars($row['thumbnail']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <p><?php echo htmlspecialchars($row['title']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📺</div>
            <h2>Your Watchlist is Empty</h2>
            <p>Start adding movies to your watchlist to watch them later!</p>
            <a href="index.php" class="browse-btn">Browse Movies</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
