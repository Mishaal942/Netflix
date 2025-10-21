<?php
session_start();
require 'db.php';
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
$movie_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM movies WHERE id=?");
$stmt->bind_param("i",$movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();
// Add to watchlist
if(isset($_POST['add_watchlist'])){
    $stmt2 = $conn->prepare("INSERT IGNORE INTO watchlist (user_id,movie_id) VALUES (?,?)");
    $stmt2->bind_param("ii",$user_id,$movie_id);
    $stmt2->execute();
}
// Convert YouTube link to embed link
$video_id = '';
if (preg_match('#(?:v=|youtu\.be/)([a-zA-Z0-9_-]+)#', $movie['video_url'], $matches)) {
    $video_id = $matches[1];
}
$embed_url = "https://www.youtube.com/embed/".$video_id."?autoplay=1&controls=1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?> - Netflix Clone</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(to bottom, #000000, #0a0a0a);
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
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

        /* Movie Title */
        .movie-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 42px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #e5e5e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
            animation: titleAppear 1s ease-out;
        }

        @keyframes titleAppear {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Video Wrapper */
        .video-wrapper {
            position: relative;
            padding-top: 56.25%;
            margin-bottom: 30px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8),
                        0 0 100px rgba(229, 9, 20, 0.2);
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            animation: videoAppear 1.2s ease-out;
        }

        @keyframes videoAppear {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .video-wrapper::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #e50914, #ff6b6b, #e50914);
            border-radius: 14px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .video-wrapper:hover::before {
            opacity: 0.5;
            animation: borderGlow 2s infinite;
        }

        @keyframes borderGlow {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 0.6;
            }
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            border-radius: 12px;
        }

        /* Action Section */
        .action-section {
            text-align: center;
            margin: 40px 0;
            animation: fadeInUp 1.4s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Button Styles */
        button {
            padding: 14px 40px;
            background: linear-gradient(135deg, #e50914 0%, #ff1a1a 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        button:hover::before {
            width: 300px;
            height: 300px;
        }

        button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(229, 9, 20, 0.6);
            background: linear-gradient(135deg, #ff1a1a 0%, #e50914 100%);
        }

        button:active {
            transform: translateY(-1px) scale(1.02);
        }

        button span {
            position: relative;
            z-index: 1;
        }

        /* Description Section */
        .description-section {
            max-width: 900px;
            margin: 50px auto 0;
            padding: 30px;
            background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1.6s ease-out;
            backdrop-filter: blur(10px);
        }

        .description-label {
            font-size: 20px;
            font-weight: 700;
            color: #e50914;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        p.description {
            font-size: 18px;
            line-height: 1.8;
            color: #e5e5e5;
            text-align: justify;
            letter-spacing: 0.3px;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.2), rgba(56, 142, 60, 0.2));
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #4caf50;
            padding: 15px 25px;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 500px;
            text-align: center;
            font-weight: 600;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            .container {
                padding: 30px 15px;
            }

            .movie-title {
                font-size: 28px;
                margin-bottom: 20px;
            }

            .video-wrapper {
                border-radius: 8px;
                margin-bottom: 25px;
            }

            button {
                padding: 12px 30px;
                font-size: 14px;
            }

            .description-section {
                padding: 20px;
                margin-top: 30px;
            }

            .description-label {
                font-size: 16px;
            }

            p.description {
                font-size: 15px;
                line-height: 1.6;
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
                padding: 20px 10px;
            }

            .movie-title {
                font-size: 24px;
            }

            button {
                padding: 10px 25px;
                font-size: 13px;
            }

            .description-section {
                padding: 15px;
            }

            p.description {
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
    <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
    
    <!-- Netflix Style Embedded Video Player -->
    <div class="video-wrapper">
        <iframe src="<?php echo $embed_url; ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
    
    <div class="action-section">
        <form method="post">
            <button type="submit" name="add_watchlist">
                <span>➕ Add to Watchlist</span>
            </button>
        </form>
    </div>
    
    <div class="description-section">
        <div class="description-label">About This Movie</div>
        <p class="description"><?php echo htmlspecialchars($movie['description']); ?></p>
    </div>
</div>
</body>
</html>
