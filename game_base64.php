<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Ensure this file connects to your database

$encoded = base64_encode("secretmessage");
$feedback = '';
$badgeEarned = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = strtolower(trim($_POST['answer']));
    if ($input === "secretmessage") {
        $feedback = '<div class="alert alert-success mt-3">✅ Correct! You cracked the Base64 message and earned a badge!</div>';
        $badgeEarned = true;

        // Save game completion and badge
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO user_game_status (user_id, game_name, completed, badge_earned)
            VALUES (?, 'base64', 1, 1)
            ON DUPLICATE KEY UPDATE completed = 1, badge_earned = 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    } else {
        $feedback = '<div class="alert alert-danger mt-3">❌ Try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Base64 Decoder - CrypTeach</title>
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    <h2>🧩 Base64 Decoder</h2>
    <p>Decode this Base64 string: <strong><?= $encoded ?></strong></p>

    <form method="post">
        <input type="text" name="answer" class="form-control mb-3" placeholder="Enter the decoded message..." required>
        <button class="btn btn-submit">Submit</button>
    </form>

    <?= $feedback ?>

    <?php if ($badgeEarned): ?>
        <div class="badge-earned">🏅 Badge Earned: Base64 Master</div>
    <?php endif; ?>

    <div class="text-center">
        <a href="games.php" class="btn-back">← Back to Games</a>
    </div>
</div>
</body>
</html>
