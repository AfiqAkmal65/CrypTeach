<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$userId = $_SESSION['user_id'];
$currentUser = $_SESSION['username'];

// Top 10 students for display
$result = $conn->query("SELECT username, score FROM users WHERE role='student' ORDER BY score DESC LIMIT 10");

// Get logged-in user's rank
$rankQuery = $conn->query("
    SELECT COUNT(*) AS rank FROM users 
    WHERE role='student' AND score > (SELECT score FROM users WHERE id = $userId)
");
$userRank = $rankQuery->fetch_assoc()['rank'] + 1;

// Get user's score (in case not in top 10)
$userScoreQuery = $conn->query("SELECT score FROM users WHERE id = $userId");
$userScore = $userScoreQuery->fetch_assoc()['score'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff8cc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 0;
        }

        .leaderboard-container {
            background: #fffef3;
            max-width: 750px;
            margin: auto;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            display: block;
            height: 80px;
            margin: 0 auto 20px;
        }

        h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #5a4300;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        thead tr {
            background-color: #f5cd79;
            color: #4e3400;
        }

        th, td {
            padding: 14px;
            font-size: 15px;
        }

        tr:nth-child(1) td {
            background-color: #ffe066;
            font-weight: bold;
            color: #000;
        }

        tr:nth-child(2) td {
            background-color: #d6d6d6;
            font-weight: bold;
            color: #000;
        }

        tr:nth-child(3) td {
            background-color: #cd7f32;
            font-weight: bold;
            color: #000;
        }

        tr:not(:first-child):hover {
            background-color: #fff6d3;
        }

        .highlight-user {
            background-color: #fff1b8 !important;
        }

        .btn-back {
            margin-top: 30px;
            padding: 10px 25px;
            font-size: 15px;
            background-color: #f4c430;
            color: #4a3300;
            border-radius: 8px;
            font-weight: bold;
            border: 2px solid #e1b200;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #ffd24d;
            text-decoration: none;
        }

        .glow {
            animation: glow 1.5s infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 5px #ffd700; }
            to { text-shadow: 0 0 15px #ffb300, 0 0 25px #f5c518; }
        }
    </style>
</head>
<body>
<div class="leaderboard-container">
    <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" class="logo">
    <h2 class="glow">🏆 Leaderboard 🏆</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Username</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr<?= ($row['username'] === $currentUser) ? ' class="highlight-user"' : '' ?>>
                    <td>
                        <?php
                        if ($rank == 1) echo "🥇";
                        elseif ($rank == 2) echo "🥈";
                        elseif ($rank == 3) echo "🥉";
                        else echo $rank;
                        ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['username']) ?>
                        <?= ($row['username'] === $currentUser) ? ' ⭐' : '' ?>
                    </td>
                    <td><strong><?= $row['score'] ?> pts</strong></td>
                </tr>
                <?php $rank++; ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($userRank > 10): ?>
        <p class="mt-4"><strong>👤 Your Rank:</strong> #<?= $userRank ?> — <?= $userScore ?> pts</p>
    <?php endif; ?>

    <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_home.php' : 'user_home.php' ?>" class="btn-back">← Back to Home</a>
</div>
</body>
</html>
