<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Define multiple levels
$levels = [
    1 => [
        'glyph' => "𓅓𓇋𓅓𓊪𓉔𓏭𓋴", // Memphis
        'answer' => "memphis",
        'hints' => [
            "This city was once the capital of Ancient Egypt.",
            "The glyphs start with the sound 'M'.",
            "The answer is 7 letters long and ends in 'is'."
        ]
    ],
    2 => [
        'glyph' => "🔐📜📬", // cipher
        'answer' => "cipher",
        'hints' => [
            "A method used to encrypt messages.",
            "Starts with a 'c'.",
            "Often paired with the word 'text'."
        ]
    ],
    3 => [
        'glyph' => "🔑📫", // key
        'answer' => "key",
        'hints' => [
            "Used to decrypt a cipher.",
            "It's a 3-letter word.",
            "Also found on your keyboard."
        ]
    ]
];

$currentLevel = isset($_GET['level']) ? intval($_GET['level']) : 1;
if (!isset($levels[$currentLevel])) {
    $currentLevel = 1;
}

$encrypted = $levels[$currentLevel]['glyph'];
$correctAnswer = $levels[$currentLevel]['answer'];
$hints = $levels[$currentLevel]['hints'];

$feedback = '';
$showHint = isset($_POST['show_hint']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $attempt = strtolower(trim($_POST['answer']));
    if ($attempt === $correctAnswer) {
        $nextLevel = $currentLevel + 1;
        if (isset($levels[$nextLevel])) {
            header("Location: ?level=$nextLevel");
            exit();
        } else {
            $feedback = '<div class="alert alert-success mt-3">🎉 Congratulations! You completed all levels!</div>';
        }
    } else {
        $feedback = '<div class="alert alert-danger mt-3">❌ Not quite. Try again!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🪄 Decode the Tomb - CrypTeach Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff9e6;
            font-family: 'Segoe UI', sans-serif;
        }
        .game-container {
            max-width: 720px;
            margin: 60px auto;
            background: #fffef8;
            border: 3px solid #f5d287;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1s ease-in;
        }
        h2 {
            color: #b45309;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .glyph {
            font-size: 42px;
            text-align: center;
            margin: 25px 0;
        }
        .btn-submit {
            background-color: #f59e0b;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
            border: none;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        .btn-submit:hover {
            background-color: #d97706;
        }
        .btn-hint {
            background-color: #fde68a;
            color: #78350f;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
            border: 1px solid #fcd34d;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .btn-hint:hover {
            background-color: #fcd34d;
            color: #92400e;
        }
        .hint-box {
            background: #fffbea;
            border-left: 5px solid #facc15;
            padding: 15px 20px;
            margin-top: 20px;
            border-radius: 10px;
            font-size: 15px;
        }
        .btn-back {
            margin-top: 30px;
            background-color: #f59e0b;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 24px;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        .btn-back:hover {
            background-color: #d97706;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="game-container animate__animated animate__fadeIn">
        <h2>🏺 Decode the Tomb Message (Level <?= $currentLevel ?>)</h2>
        <p>You've discovered a scroll. Can you decode this secret message?</p>

        <div class="glyph"><?= $encrypted ?></div>

        <form method="post">
            <input type="text" name="answer" class="form-control mb-4" placeholder="Enter the decoded word..." required>
            <div class="text-center">
                <button type="submit" class="btn btn-submit me-2">Submit</button>
                <button type="submit" name="show_hint" class="btn btn-hint">🔍 Show Hint</button>
            </div>
        </form>

        <?= $feedback ?>

        <?php if ($showHint): ?>
            <div class="hint-box animate__animated animate__fadeInUp mt-3">
                <?php foreach ($hints as $index => $hint): ?>
                    <strong>Hint <?= $index + 1 ?>:</strong> <?= $hint ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="user_home.php" class="btn btn-back mt-4">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
