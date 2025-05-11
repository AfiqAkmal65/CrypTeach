<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Book pages
$pages = [
    1 => ['title' => 'Introduction to Cryptography', 'content' => "Cryptography is the art of secure communication.\n\nIt has a rich history, from Caesar ciphers to modern encryption methods. Cryptography ensures confidentiality, integrity, and authenticity."],
    2 => ['title' => 'Symmetric Encryption', 'content' => "Symmetric encryption uses the same key for both encryption and decryption.\n\nPopular algorithms: AES, DES, Blowfish.\n\nIt is fast, but key sharing must be secure."],
    3 => ['title' => 'Public Key Cryptography', 'content' => "Public key systems use two keys: public and private.\n\nUsed in HTTPS, email encryption, digital signatures.\n\nExamples: RSA, ECC, Diffie-Hellman."],
    4 => ['title' => 'Ancient Egyption Cryptography', 'content' => "Cryptography didn’t begin with computers — it began with civilizations like Ancient Egypt, where hiding meaning had religious, ceremonial, and political purposes.\n\nEgyptians were among the earliest known users of secret writing (around 1900 BCE).\n\nHieroglyphs were already a complex writing system, but scribes occasionally used alternative symbols for stylistic or secretive reasons.\n\nThese variations are called “non-standard hieroglyphs” or cryptographic hieroglyphs."]
];

// Tips
$tips = [
    "Cryptography comes from the Greek words *kryptos* (hidden) and *graphein* (to write).",
    "The Caesar cipher was named after Julius Caesar, who used it in military communications.",
    "Symmetric key encryption is extremely fast and best for large amounts of data.",
    "Public key cryptography allows secure key exchange over an insecure channel.",
    "The Enigma machine used by Nazi Germany was cracked by Allied cryptanalysts during WWII.",
    "Cryptography is not just about secrecy—it also ensures authenticity and data integrity."
];

$current = $pages[$page] ?? null;
if (!$current) {
    die("<strong>Invalid page.</strong>");
}

$random_tip = $tips[array_rand($tips)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($current['title']) ?> - Digital Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fffde7;
            font-family: 'Segoe UI', sans-serif;
        }
        .reader-container {
            max-width: 800px;
            margin: 60px auto;
            background: #fffef4;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        h2 {
            color: #6d4c41;
            margin-bottom: 20px;
            font-weight: bold;
        }
        pre {
            white-space: pre-wrap;
            font-size: 16px;
            color: #5d4037;
            line-height: 1.7;
        }
        .tips-box {
            margin-top: 30px;
            padding: 15px 20px;
            background: #fff8e1;
            border-left: 5px solid #f9a825;
            border-radius: 10px;
            color: #5d4037;
        }
        .tips-box h5 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* Button Styling */
        .btn {
            transition: all 0.3s ease;
            padding: 8px 20px;
            font-weight: bold;
            border-radius: 10px;
        }

        .custom-btn-filled {
            background-color: #f9a825;
            color: white;
            border: none;
        }
        .custom-btn-filled:hover {
            background-color: #f57f17;
            transform: scale(1.05);
        }

        .custom-btn-outline {
            background-color: transparent;
            border: 2px solid #f9a825;
            color: #f57f17;
        }
        .custom-btn-outline:hover {
            background-color: #f9a825;
            color: white;
            transform: scale(1.05);
        }

        .custom-btn-dark {
            background-color: #6d4c41;
            color: white;
            border: none;
        }
        .custom-btn-dark:hover {
            background-color: #5d4037;
            transform: scale(1.05);
        }

        .btn-group-centered {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="reader-container">
    <h2>📖 <?= htmlspecialchars($current['title']) ?></h2>
    <pre><?= htmlspecialchars($current['content']) ?></pre>

    <div class="tips-box">
        <h5>💡 Did You Know?</h5>
        <p><?= htmlspecialchars($random_tip) ?></p>
    </div>

    <div class="btn-group-centered">
        <?php if (isset($pages[$page - 1])): ?>
            <a href="book.php?page=<?= $page - 1 ?>" class="btn custom-btn-outline">← Previous</a>
        <?php endif; ?>

        <a href="user_home.php" class="btn custom-btn-dark">🏠 Home</a>

        <?php if (isset($pages[$page + 1])): ?>
            <a href="book.php?page=<?= $page + 1 ?>" class="btn custom-btn-filled">Next →</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
