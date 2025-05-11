<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$chapter_id = $_GET['chapter'] ?? 0;

// Get quiz questions
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE chapter_id = ?");
$stmt->bind_param("i", $chapter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No quizzes found for this chapter.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total = count($_POST['answers']);

    foreach ($_POST['answers'] as $question_id => $answer) {
        $check = $conn->prepare("SELECT correct_option FROM quizzes WHERE id = ?");
        $check->bind_param("i", $question_id);
        $check->execute();
        $correct = $check->get_result()->fetch_assoc();
        if ($correct['correct_option'] === $answer) {
            $score++;
        }
    }

    $user_id = $_SESSION['user_id'];
    $update = $conn->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $update->bind_param("ii", $score, $user_id);
    $update->execute();

    echo "<div style='text-align:center; padding: 50px; font-family: Segoe UI;'>
            <h2 style='color:#1d4ed8;'>🎉 Quiz Completed!</h2>
            <p style='font-size:18px;'>You scored <strong>$score</strong> out of <strong>$total</strong></p>
            <a href='user_home.php' style='margin-top:20px; display:inline-block; padding:10px 20px; background:#3b82f6; color:white; text-decoration:none; border-radius:8px;'>← Back to Dashboard</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .quiz-container {
            max-width: 850px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
        h2 {
            color: #1d4ed8;
            margin-bottom: 30px;
            text-align: center;
        }
        .quiz-question {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background: #f9fafb;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .quiz-question p {
            font-weight: 600;
            font-size: 18px;
        }
        label {
            display: block;
            margin: 8px 0;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        label:hover {
            background: #eef2ff;
        }
        button[type="submit"], .btn-back {
            margin-top: 20px;
            padding: 12px 25px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background-color: #3b82f6;
            color: white;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover, .btn-back:hover {
            background-color: #2563eb;
        }
        .btn-back {
            margin-left: 15px;
            background-color: #6b7280;
        }
        .btn-back:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>

<div class="quiz-container">
    <h2>📝 Chapter Quiz</h2>
    <form method="POST" action="quiz.php?chapter=<?= $chapter_id ?>">
        <?php while ($quiz = $result->fetch_assoc()): ?>
            <div class="quiz-question">
                <p><?= htmlspecialchars($quiz['question']) ?></p>
                <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                    <label>
                        <input type="radio" name="answers[<?= $quiz['id'] ?>]" value="<?= $opt ?>" required>
                        <?= htmlspecialchars($quiz["option_$opt"]) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>

        <div class="d-flex justify-content-center">
            <button type="submit">Submit Quiz</button>
            <a href="user_home.php" class="btn-back text-white text-decoration-none">← Back to Dashboard</a>
        </div>
    </form>
</div>

</body>
</html>
