<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$chapter_id = isset($_GET['chapter']) ? intval($_GET['chapter']) : 0;

// Fetch quiz questions for this chapter (shared)
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE chapter_id = ?");
$stmt->bind_param("i", $chapter_id);
$stmt->execute();
$result = $stmt->get_result();

// If no quiz exists, generate one using Gemini
if ($result->num_rows === 0) {
    $chapterQuery = $conn->prepare("SELECT description FROM chapters WHERE id = ?");
    $chapterQuery->bind_param("i", $chapter_id);
    $chapterQuery->execute();
    $chapterResult = $chapterQuery->get_result();
    $chapterRow = $chapterResult->fetch_assoc();

    if ($chapterRow) {
        $description = $chapterRow['description'];
        $prompt = "Generate 3 multiple choice questions from this content. Each should have options a, b, c, d and a correct answer labeled. Format like this:\n
1. What is X?\na) A\nb) B\nc) C\nd) D\nAnswer: a\n\nContent:\n" . $description;

        $geminiData = [
            'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_GEMINI_API_KEY",
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
            $text = $responseData['candidates'][0]['content']['parts'][0]['text'];

            // Debug file for verification
            file_put_contents("gemini_output_chapter_{$chapter_id}.txt", $text);

            $lines = explode("\n", $text);
            $questions = [];
            $current = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^\d+\./', $line)) {
                    if (!empty($current)) $questions[] = $current;
                    $current = ['question' => trim(substr($line, strpos($line, '.') + 1))];
                } elseif (preg_match('/^a\)/i', $line)) $current['option_a'] = trim(substr($line, 2));
                elseif (preg_match('/^b\)/i', $line)) $current['option_b'] = trim(substr($line, 2));
                elseif (preg_match('/^c\)/i', $line)) $current['option_c'] = trim(substr($line, 2));
                elseif (preg_match('/^d\)/i', $line)) $current['option_d'] = trim(substr($line, 2));
                elseif (preg_match('/^Answer:\s*([a-d])/i', $line, $matches)) {
                    $current['correct_option'] = strtolower($matches[1]);
                }
            }
            if (!empty($current)) $questions[] = $current;

            // Insert into DB (no user_id now, shared quiz)
            foreach ($questions as $qz) {
                if (
                    !empty($qz['question']) &&
                    !empty($qz['option_a']) &&
                    !empty($qz['option_b']) &&
                    !empty($qz['option_c']) &&
                    !empty($qz['option_d']) &&
                    !empty($qz['correct_option'])
                ) {
                    $insert = $conn->prepare("INSERT INTO quizzes (chapter_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $insert->bind_param(
                        "issssss",
                        $chapter_id,
                        $qz['question'],
                        $qz['option_a'],
                        $qz['option_b'],
                        $qz['option_c'],
                        $qz['option_d'],
                        $qz['correct_option']
                    );
                    $insert->execute();
                }
            }

            // Re-fetch the newly inserted quiz
            $stmt = $conn->prepare("SELECT * FROM quizzes WHERE chapter_id = ?");
            $stmt->bind_param("i", $chapter_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    }
}

// Show fallback message if still no quiz found
if ($result->num_rows === 0) {
    echo "<div style='text-align:center; margin-top: 80px; font-family: Segoe UI; background:#fff8e1; padding:40px; border-radius:12px; width:80%; margin:auto; box-shadow:0 6px 20px rgba(0,0,0,0.1);'>
        <h2 style='color:#d32f2f;'>❌ No Quiz Available</h2>
        <p>Quiz could not be generated. Please try again later.</p>
        <a href='quiz_list.php' style='margin-top:20px; display:inline-block; padding:10px 20px; background:#6b7280; color:white; text-decoration:none; border-radius:8px;'>← Back to Quiz List</a>
    </div>";
    exit();
}

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total = count($_POST['answers']);
    foreach ($_POST['answers'] as $question_id => $userAnswer) {
        $check = $conn->prepare("SELECT correct_option FROM quizzes WHERE id = ?");
        $check->bind_param("i", $question_id);
        $check->execute();
        $correct = $check->get_result()->fetch_assoc();
        if (strtolower($correct['correct_option']) === strtolower($userAnswer)) {
            $score++;
        }
    }

    // Update user score
    $user_id = $_SESSION['user_id'];
    $update = $conn->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $update->bind_param("ii", $score, $user_id);
    $update->execute();

    echo "<div style='text-align:center; padding: 50px; font-family: Segoe UI; background:#e0f7fa; width: 80%; margin: 80px auto; border-radius:12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);'>
        <h2 style='color:#0288d1;'>🎉 Quiz Completed!</h2>
        <p style='font-size:18px;'>You scored <strong>$score</strong> out of <strong>$total</strong></p>
        <a href='user_home.php' style='margin-top:20px; display:inline-block; padding:10px 20px; background:#0288d1; color:white; text-decoration:none; border-radius:8px;'>← Back to Dashboard</a>
    </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chapter Quiz - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fffde7, #fff3e0);
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
            color: #f59e0b;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }
        .quiz-question {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background: #fefce8;
            box-shadow: 0 4px 8px rgba(0,0,0,0.04);
        }
        .quiz-question p {
            font-weight: 600;
            font-size: 18px;
        }
        label {
            display: block;
            margin: 8px 0;
            cursor: pointer;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        label:hover {
            background: #fffde7;
        }
        input[type="radio"]:checked + span {
            font-weight: bold;
            color: #1e3a8a;
        }
        button[type="submit"],
        .btn-back {
            display: inline-block;
            height: 48px;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.5;
            border: none;
            border-radius: 8px;
            vertical-align: middle;
        }
        button[type="submit"] {
            background-color: #f59e0b;
            color: white;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #d97706;
        }
        .btn-back {
            background-color: #9ca3af;
            color: white !important;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #6b7280;
        }
    </style>
</head>
<body>

<div class="quiz-container">
    <h2>📝 Chapter <?= $chapter_id ?> Quiz</h2>
    <form method="POST" action="quiz.php?chapter=<?= $chapter_id ?>">
        <?php while ($quiz = $result->fetch_assoc()): ?>
            <div class="quiz-question">
                <p><?= htmlspecialchars($quiz['question']) ?></p>
                <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                    <label>
                        <input type="radio" name="answers[<?= $quiz['id'] ?>]" value="<?= $opt ?>" required>
                        <span><?= htmlspecialchars($quiz["option_$opt"]) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>
        <div class="d-flex justify-content-center gap-3">
            <button type="submit">Submit Quiz</button>
            <a href="quiz_list.php" class="btn-back">← Back to List</a>
        </div>
    </form>
</div>

</body>
</html>
