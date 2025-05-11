<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
include 'db.php';
$result = $conn->query("SELECT * FROM quizzes");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quizzes - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fffde7; /* soft yellow */
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 60px auto;
            padding: 30px;
            background: #fffef4;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        h2 {
            font-weight: 700;
            color: #333;
        }
        .quiz-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }
        .quiz-card {
            background-color: #fff8c5; /* light yellow tone */
            padding: 18px 20px;
            border-radius: 12px;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
        }
        .quiz-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .quiz-card a {
            text-decoration: none;
            color: #d35400;
            font-size: 18px;
            font-weight: 600;
        }
        .quiz-meta {
            font-size: 14px;
            color: #666;
            margin-top: 6px;
        }
        .quiz-icon {
            font-size: 22px;
            margin-right: 8px;
        }
        .back-btn {
    display: inline-block;
    padding: 10px 25px;
    color: #fff;
    background-color: #d35400;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}
.back-btn:hover {
    background-color: #bf360c;
    text-decoration: none;
}


    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">🧠 Take a Quiz</h2>
    <div class="quiz-grid">
        <?php while ($quiz = $result->fetch_assoc()): ?>
            <div class="quiz-card">
                <a href="quiz.php?chapter=<?= $quiz['chapter_id'] ?>">
                    <span class="quiz-icon">📄</span><?= htmlspecialchars($quiz['question']) ?>
                </a>
                <div class="quiz-meta">
                    Level: <strong>Easy</strong> • 3 Questions • ~2 min
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="text-center mt-4"> <!-- Added mt-4 -->
    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_home.php' : 'user_home.php' ?>" class="back-btn">← Back to Home</a>
</div>



</div>
</body>
</html>
