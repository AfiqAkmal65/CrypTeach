<?php
session_start();
include 'db.php';


if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chapter_id = $_POST['chapter_id'];
    $question = $_POST['question'];
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'];
    $d = $_POST['option_d'];
    $correct = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO quizzes (chapter_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $chapter_id, $question, $a, $b, $c, $d, $correct);
    $stmt->execute();
}

$chapters = $conn->query("SELECT id, title FROM chapters");
$quizzes = $conn->query("SELECT q.*, c.title as chapter_title FROM quizzes q JOIN chapters c ON q.chapter_id = c.id ORDER BY q.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Quizzes - CrypTeach</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Manage Quizzes</h2>

    <form method="POST">
        <label>Chapter:</label>
        <select name="chapter_id" required>
            <?php while ($ch = $chapters->fetch_assoc()): ?>
                <option value="<?= $ch['id'] ?>"><?= htmlspecialchars($ch['title']) ?></option>
            <?php endwhile; ?>
        </select><br>

        <textarea name="question" placeholder="Enter question" required></textarea><br>
        <input type="text" name="option_a" placeholder="Option A" required><br>
        <input type="text" name="option_b" placeholder="Option B" required><br>
        <input type="text" name="option_c" placeholder="Option C" required><br>
        <input type="text" name="option_d" placeholder="Option D" required><br>

        <label>Correct Option:</label>
        <select name="correct_option" required>
            <option value="a">A</option>
            <option value="b">B</option>
            <option value="c">C</option>
            <option value="d">D</option>
        </select><br>

        <button type="submit">Add Quiz</button>
    </form>

    <h3>Existing Quizzes</h3>
    <ul>
        <?php while ($q = $quizzes->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($q['chapter_title']) ?>:</strong>
                <?= htmlspecialchars($q['question']) ?>
                <br><em>Correct: <?= strtoupper($q['correct_option']) ?></em>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="admin_home.php" class="menu-item">← Back to Home</a>
</div>
</body>
</html>
