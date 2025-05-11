<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Ensure your DB connection file is loaded

$plain = "secure message";
$from = 'abcdefghijklmnopqrstuvwxyz';
$to = 'defghijklmnopqrstuvwxyzabc';
$cipher = strtr($plain, $from, $to);

$feedback = '';
$badgeEarned = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswer = strtolower(trim($_POST['answer']));
    if ($userAnswer === $plain) {
        $feedback = '<div class="alert alert-success mt-3">🎉 Correct! You decrypted the Caesar Cipher and earned a badge!</div>';
        $badgeEarned = true;

        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO user_game_status (user_id, game_name, completed, badge_earned)
            VALUES (?, 'caesar', 1, 1)
            ON DUPLICATE KEY UPDATE completed = 1, badge_earned = 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    } else {
        $feedback = '<div class="alert alert-danger mt-3">❌ Incorrect decryption. Try again!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Caesar Cipher Game - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff9e6;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 720px;
            margin: 60px auto;
            background: #fffef4;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #b45309;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
        .cipher-text {
            font-size: 18px;
            font-weight: bold;
            color: #d97706;
            margin-bottom: 10px;
            text-align: center;
        }
        .btn-submit {
            background-color: #f59e0b;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            border: none;
            display: block;
            margin: 0 auto;
        }
        .btn-submit:hover {
            background-color: #d97706;
        }
        .btn-back {
            margin-top: 30px;
            background-color: #f59e0b;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 24px;
            border: none;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #d97706;
        }
        .badge-earned {
            display: inline-block;
            margin-top: 15px;
            background-color: #38a169;
            color: white;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔐 Caesar Cipher Challenge</h2>

    <p class="text-center">Can you decrypt the following Caesar-shifted message?</p>
    <div class="cipher-text"><?= $cipher ?></div>
    <p class="text-center"><em>Hint: Caesar Cipher (Shift by 3)</em></p>

    <form method="post" class="text-center">
        <input type="text" name="answer" class="form-control mb-3" placeholder="Enter decrypted text..." required>
        <button type="submit" class="btn btn-submit">Submit</button>
    </form>

    <?= $feedback ?>

    <?php if ($badgeEarned): ?>
        <div class="text-center">
            <div class="badge-earned">🏅 Badge Earned: Caesar Cipher Champ</div>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <a href="games.php" class="btn-back mt-4">← Back to Games</a>
    </div>
</div>
</body>
</html>
