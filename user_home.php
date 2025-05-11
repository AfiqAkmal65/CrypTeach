<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
include 'db.php';

date_default_timezone_set('Asia/Kuala_Lumpur');
// Dynamic greeting
$hour = date('H');
if ($hour < 12) $greet = "Good morning";
elseif ($hour < 18) $greet = "Good afternoon";
else $greet = "Good evening";

$last_chapter = "Chapter 2: Symmetric Encryption";
$completion = 60;
$crypto_tip = "RSA keys are stronger when they use at least 2048 bits.";

// 🔄 Fetch earned badges from DB
$badges = [];
$result = $conn->query("SELECT game_name FROM user_game_status WHERE user_id = {$_SESSION['user_id']} AND badge_earned = 1");
while ($row = $result->fetch_assoc()) {
    $name = ucfirst($row['game_name']);
    $badgeIcon = match($row['game_name']) {
        'caesar' => '🔐',
        'base64' => '🧬',
        'reverse' => '🔁',
        default => '🏅'
    };
    $badges[] = "$badgeIcon " . $name . " Master";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fffde7;
            font-family: 'Segoe UI', sans-serif;
        }
        header {
            background: linear-gradient(to right, #fbbf24, #fcd34d);
    padding: 18px 40px;
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    font-family: 'Segoe UI', sans-serif;
        }
        .section-title {
            margin-top: 30px;
            font-weight: bold;
        }
        .card-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background: #ffffffcc;
            border-radius: 10px;
            padding: 20px;
            flex: 1;
            min-width: 260px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#ffb300 <?= $completion ?>%, #ddd <?= $completion ?>%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin: 0 auto;
        }
        .badge-list span {
            background: #fff3cd;
            border-radius: 20px;
            padding: 6px 12px;
            margin: 5px;
            display: inline-block;
            color: #795548;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #ffa000;
            border: none;
        }
        .btn-primary:hover {
            background-color: #ff8f00;
        }
         .wave-emoji {
        display: inline-block;
        animation: wave 2s infinite;
        transform-origin: 70% 70%;
    }

    @keyframes wave {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(14deg); }
        20% { transform: rotate(-8deg); }
        30% { transform: rotate(14deg); }
        40% { transform: rotate(-4deg); }
        50% { transform: rotate(10deg); }
        60% { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }
    </style>
</head>
<body>

<header>
  <!-- Left: Logo + Greeting -->
    <div style="display: flex; align-items: center; gap: 20px;">
        <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo"
             style="height: 64px; width: 64px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
        <div>
            <div style="font-size: 24px; font-weight: 600; color: white;">
                <?= $greet ?>, <span style="text-transform: capitalize;"><?= htmlspecialchars($_SESSION['username']) ?></span> 
                <span class="wave-emoji">👋🏻</span>
            </div>
            <div style="font-size: 14px; color: #fffbe8;">Welcome back to your learning hub</div>
        </div>
    </div>

    <!-- Right: Logout Button -->
    <a href="logout.php" style="
        background-color: white;
        color: #333;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease, transform 0.2s ease;
    " onmouseover="this.style.backgroundColor='#f1f1f1'; this.style.transform='scale(1.05)'" onmouseout="this.style.backgroundColor='white'; this.style.transform='scale(1)'">
        Logout
    </a>
</header>




<div class="container mt-4">

    <h4 class="section-title">🚀 Quick Access</h4>
    <div class="card-grid mb-4">
          <a href="all_chapters.php" class="card text-decoration-none text-dark">
            <h5>📚 View Chapters</h5>
            <p>Browse all learning chapters.</p>
        </a>
        <a href="quiz_list.php" class="card text-decoration-none text-dark">
            <h5>🧪 Take a Quiz</h5>
            <p>Practice with interactive questions.</p>
        </a>
        <a href="videos.php" class="card text-decoration-none text-dark">
            <h5>📺 Watch Videos</h5>
            <p>Visual explanations for complex topics.</p>
        </a>
        <a href="leaderboard.php" class="card text-decoration-none text-dark">
            <h5>🏆 Leaderboard</h5>
            <p>See where you rank!</p>
        </a>
        <a href="games.php" class="card text-decoration-none text-dark">
            <h5>🎮 Crypto Games</h5>
            <p>Play and learn cryptography!</p>
        </a>
        <a href="game_egypt.php" class="card text-decoration-none text-dark">
            <h5>🪄 Decode the Tomb</h5>
            <p>Crack the ancient Egyptian message!</p>
        </a>
        <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
            <h5>🛠️ Encrypt Tools</h5>
            <p>Securely hash or hieroglyphify your text!</p>
        </a>
    </div>

    <h4 class="section-title">📍 Continue Learning</h4>
    <div class="card mb-4 text-center">
        <h5><?= $last_chapter ?></h5>
        <p>You're <?= $completion ?>% done. Keep going!</p>
        <div class="progress-circle mb-3"><?= $completion ?>%</div>
        <a href="chapters.php?id=2" class="btn btn-primary">Continue</a>
    </div>

    <h4 class="section-title">📰 Crypto Insight</h4>
    <div class="card mb-4">
        <p><?= $crypto_tip ?></p>
    </div>

    <h4 class="section-title">🏅 Your Badges</h4>
    <div class="card badge-list mb-4">
        <?php if (empty($badges)): ?>
            <p>You haven't earned any badges yet. Play the games to unlock them!</p>
        <?php else: ?>
            <?php foreach ($badges as $badge): ?>
                <span><?= $badge ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h4 class="section-title">📖 Digital Book</h4>
    <div class="card-grid mb-4">
        <a href="book.php?page=1" class="card text-decoration-none text-dark">
            <h6>📄 Introduction to Cryptography</h6>
            <p>Explore the foundations and history of cryptography.</p>
        </a>
        <a href="book.php?page=2" class="card text-decoration-none text-dark">
            <h6>🔐 Symmetric Encryption</h6>
            <p>Understand encryption techniques using a single key.</p>
        </a>
        <a href="book.php?page=3" class="card text-decoration-none text-dark">
            <h6>🗝️ Public Key Cryptography</h6>
            <p>Dive into RSA, Diffie-Hellman, and more.</p>
        </a>
        <a href="book.php?page=4" class="card text-decoration-none text-dark">
            <h6>🏺 Ancient Egyptian Cryptography</h6>
            <p>Learn how scribes used symbols to hide sacred messages.</p>
        </a>
    </div>

    <h4 class="section-title">🧰 Quick Tools</h4>
    <div class="card-grid mb-4">
        <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
            <h6>🔢 Base Converter</h6>
            <p>Convert between binary, hex, and more.</p>
        </a>
        <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
            <h6>🔐 Hash Generator</h6>
            <p>Generate SHA-256, MD5 hashes easily.</p>
        </a>
        <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
            <h6>🧮 Key Generator</h6>
            <p>Create strong encryption keys.</p>
        </a>
    </div>

    <h4 class="section-title">🔓 Weekly Challenge</h4>
    <div class="card mb-5">
        <p><strong>Hint:</strong> Caesar Cipher, Shift by 2</p>
        <p><strong>Encrypted:</strong> jgnnq</p>
        <form method="post">
            <input type="text" name="answer" placeholder="Enter decrypted word..." class="form-control mb-2" required>
            <button class="btn btn-success">Submit</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attempt = strtolower(trim($_POST['answer']));
            echo $attempt === 'hello'
                ? '<div class="alert alert-success mt-2">✅ Correct! You cracked the cipher!</div>'
                : '<div class="alert alert-danger mt-2">❌ Not quite. Try again!</div>';
        }
        ?>
    </div>
</div>

<footer style="background-color: #fff8d6; text-align: center; padding: 20px; font-size: 14px; color: #555; border-top: 1px solid #ccc; margin-top: 40px;">
    © 2025, <strong>CrypTeach</strong> — Learn, Encrypt, Empower.
</footer>

</body>
</html>
