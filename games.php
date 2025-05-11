<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cryptography Games - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #fffde7;
        font-family: 'Segoe UI', sans-serif;
    }

    .container {
        max-width: 850px;
        margin: 60px auto;
        background: #fffef4;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-weight: bold;
        font-size: 30px;
        margin-bottom: 35px;
        text-align: center;
        color: #bf360c;
    }

    .game-card {
        background-color: #fff9db;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        transition: 0.3s ease;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .game-card:hover {
        background-color: #fff3c4;
        transform: scale(1.02);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .game-card h5 {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 20px;
        color: #b45309;
    }

    .game-card p {
        margin-bottom: 12px;
        color: #444;
    }

    .play-btn {
        background-color: #f59e0b;
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
    }

    .play-btn:hover {
        background-color: #d97706;
    }

    .game-badge {
        position: absolute;
        top: 15px;
        right: 20px;
        background-color: #fcd34d;
        color: #78350f;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: bold;
        border-radius: 20px;
    }

    .game-meta {
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
    }

    .back-link {
        display: inline-block;
        margin-top: 40px;
        text-align: center;
        background-color: #f59e0b;
        color: #fff;
        padding: 10px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease;
    }

    .back-link:hover {
        background-color: #d97706;
    }

    .text-center {
        text-align: center;
    }
</style>

</head>
<body>
<div class="container">
    <h2>🎮 Cryptography Games</h2>

    <!-- Game 1 -->
    <div class="game-card">
        <div class="game-badge">Beginner</div>
        <h5>🔐 Caesar Cipher Challenge</h5>
        <p>Decrypt words encrypted with a Caesar shift. Great for beginners.</p>
        <ul class="game-meta">
            <li>🕐 Estimated Time: ~2 minutes</li>
            <li>🧠 Strengthens pattern recognition</li>
        </ul>
        <a href="game_caesar.php" class="play-btn">Play Now</a>
    </div>

    <!-- Game 2 -->
    <div class="game-card">
        <div class="game-badge">Intermediate</div>
        <h5>🧩 Base64 Decoder</h5>
        <p>Decode Base64-encoded secrets. Unlock hidden messages in seconds!</p>
        <ul class="game-meta">
            <li>🧬 Understand encoding basics</li>
        </ul>
        <a href="game_base64.php" class="play-btn">Play Now</a>
    </div>

    <!-- Game 3 -->
    <div class="game-card">
        <div class="game-badge">Intermediate</div>
        <h5>🔁 Reverse Cipher Riddle</h5>
        <p>Flip the message, flip your thinking. Can you read backward?</p>
        <ul class="game-meta">
            <li>🌀 Great for warm-up rounds</li>
        </ul>
        <a href="game_reverse.php" class="play-btn">Play Now</a>
    </div>

    <div class="text-center">
        <a href="user_home.php" class="back-link">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
