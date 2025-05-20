<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'db.php';
include 'gemini_config.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "<h3 style='text-align:center;margin-top:50px;'>No chapter selected.</h3>";
    exit();
}

$chapterId = intval($_GET['id']);
$result = $conn->query("SELECT * FROM chapters WHERE id = $chapterId");

if ($result->num_rows === 0) {
    echo "<h3 style='text-align:center;margin-top:50px;'>Chapter not found.</h3>";
    exit();
}

$chapter = $result->fetch_assoc();

// Progress tracking
$check = $conn->prepare("SELECT progress FROM user_chapter_progress WHERE user_id = ? AND chapter_id = ?");
$check->bind_param("ii", $user_id, $chapterId);
$check->execute();
$res = $check->get_result();
$current_progress = ($res->num_rows > 0) ? $res->fetch_assoc()['progress'] : 0;

if ($res->num_rows === 0) {
    $progress = 25;
    $insert = $conn->prepare("INSERT INTO user_chapter_progress (user_id, chapter_id, progress) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $chapterId, $progress);
    $insert->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($chapter['title']) ?> - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff9e6; font-family: 'Segoe UI', sans-serif; padding: 30px; }
        .chapter-container { max-width: 850px; margin: auto; background: white; padding: 35px; border-radius: 18px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
        .chapter-title { font-size: 28px; color: #d97706; font-weight: bold; display: flex; align-items: center; gap: 12px; }
        .chapter-description { margin-top: 25px; font-size: 18px; color: #374151; line-height: 1.7; }
        .chapter-highlight { background-color: #fef3c7; padding: 15px 20px; margin-top: 30px; border-left: 5px solid #f59e0b; border-radius: 10px; font-style: italic; color: #92400e; }
        .btn-back { margin-top: 35px; background-color: #f59e0b; color: white; font-weight: 600; padding: 10px 20px; border-radius: 10px; text-decoration: none; transition: background-color 0.3s ease, transform 0.2s ease; }
        .btn-back:hover { background-color: #d97706; transform: scale(1.03); }
        .btn-success { background-color: #10b981; border: none; }
        .btn-success:hover { background-color: #059669; }
        .btn-primary { background-color: #3b82f6; border: none; }
        .btn-primary:hover { background-color: #2563eb; }
    </style>
</head>
<body>
<div class="chapter-container">
    <div class="chapter-title">📘 <?= htmlspecialchars($chapter['title']) ?></div>

    <div class="chapter-description"><?= nl2br(htmlspecialchars($chapter['description'])) ?></div>

    <div class="chapter-highlight">
        💡 Did you know? Cryptography plays a vital role in securing online communication like emails, chats, and bank transactions!
    </div>

    <?php if (!empty($chapter['content_file'])): ?>
        <div class="mt-4">
            <?php
            $file = $chapter['content_file'];
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            ?>
            <?php if ($ext === 'pdf'): ?>
                <iframe src="<?= htmlspecialchars($file) ?>" width="100%" height="600px" style="border: none;"></iframe>
            <?php else: ?>
                <p class="text-danger fw-bold">⚠️ Can't display this file. Please download manually:</p>
                <a href="<?= htmlspecialchars($file) ?>" class="btn btn-warning" download>⬇ Download Document</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <?php if ($current_progress == 100): ?>
            <span class="badge bg-success p-2 fs-6">✅ Completed</span>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="mark_complete" value="1">
                <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded">✅ Mark as Completed</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <form method="POST">
            <input type="hidden" name="ai_generate_quiz" value="1">
            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded">🧠 Generate AI Quiz</button>
        </form>
    </div>

    <div class="text-center mt-4">
        <a href="all_chapters.php" class="btn-back">← Back to All Chapters</a>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $stmt = $conn->prepare("UPDATE user_chapter_progress SET progress = 100 WHERE user_id = ? AND chapter_id = ?");
    $stmt->bind_param("ii", $user_id, $chapterId);
    $stmt->execute();
    echo "<script>alert('Chapter marked as completed!'); window.location.href='chapters.php?id=$chapterId';</script>";
    exit();
}

// AI Quiz Generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ai_generate_quiz'])) {
    $prompt = "Generate 3 multiple choice questions from this content. Each should have options a, b, c, d and a correct answer labeled.\n\n" . $chapter['description'];

    $geminiData = [
        'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$GEMINI_API_KEY",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($geminiData)
    ]);

       $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode === 200 && $response !== false) {
        $responseData = json_decode($response, true);
        $quizText = $responseData['candidates'][0]['content']['parts'][0]['text'];

        $questions = preg_split("/\n\d+\./", $quizText);
        foreach ($questions as $q) {
            if (strlen(trim($q)) < 10) continue;

            preg_match("/\*\*(.*?)\*\*.*?a\)(.*?)b\)(.*?)c\)(.*?)d\)(.*?)\*\*Correct Answer: ([a-d])\*\*/is", $q, $matches);

            if (count($matches) === 7) {
                $question = trim($matches[1]);
                $option_a = trim($matches[2]);
                $option_b = trim($matches[3]);
                $option_c = trim($matches[4]);
                $option_d = trim($matches[5]);
                $correct_option = strtolower(trim($matches[6]));

                $stmt = $conn->prepare("INSERT INTO quizzes (chapter_id, question, option_a, option_b, option_c, option_d, correct_option, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssss", $chapterId, $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $correct_option);
                $stmt->execute();
            }
        }

        echo "<script>alert('Quiz generated and saved!'); window.location.href='quiz.php?chapter=$chapterId';</script>";
        exit();
    } else {
        echo "<p class='text-danger text-center mt-3'>❌ Gemini API error (HTTP $httpCode)</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }

}
?>
</body>
</html>
