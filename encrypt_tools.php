<?php
$egypt_map = [
    'a' => '𓂀', 'b' => '𓃠', 'c' => '𓆑', 'd' => '𓂧', 'e' => '𓇋',
    'f' => '𓆓', 'g' => '𓎼', 'h' => '𓉔', 'i' => '𓏭', 'j' => '𓆇',
    'k' => '𓎡', 'l' => '𓃭', 'm' => '𓅓', 'n' => '𓈖', 'o' => '𓅱',
    'p' => '𓊪', 'q' => '𓐎', 'r' => '𓂋', 's' => '𓋴', 't' => '𓏏',
    'u' => '𓍯', 'v' => '𓆘', 'w' => '𓅨', 'x' => '𓐍', 'y' => '𓆰', 'z' => '𓊃', ' ' => ' '
];

function binaryToText($binary) {
    $text = '';
    foreach (explode(' ', $binary) as $bin) {
        $text .= chr(bindec($bin));
    }
    return $text;
}

function textToBinary($text) {
    $binary = [];
    foreach (str_split($text) as $char) {
        $binary[] = str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }
    return implode(' ', $binary);
}

function generateKey($length = 16) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $key;
}

$mode = $_POST['mode'] ?? '';
$text = trim($_POST['text'] ?? '');
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($mode) {
        case 'password_hash':
            $result = password_hash($text, PASSWORD_DEFAULT);
            break;
        case 'verify_hash':
            $parts = explode('|', $text);
            if (count($parts) === 2) {
                $plain = trim($parts[0]);
                $hash = trim($parts[1]);
                $result = password_verify($plain, $hash) ? '✅ Match!' : '❌ Does not match.';
            } else {
                $result = "⚠️ Format: plain|hashed_value";
            }
            break;
        case 'encrypt_egypt':
            foreach (str_split(strtolower($text)) as $char) {
                $result .= $egypt_map[$char] ?? $char;
            }
            break;
        case 'decrypt_egypt':
            $flipped = array_flip($egypt_map);
            foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $char) {
                $result .= $flipped[$char] ?? $char;
            }
            break;
        case 'binary_to_text':
            $result = binaryToText($text);
            break;
        case 'text_to_binary':
            $result = textToBinary($text);
            break;
        case 'generate_key':
            $result = generateKey();
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🔧 Text Encryption Tools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #fffde7, #fff8e1);
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }
        .container {
            max-width: 850px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            color: #ff8f00;
        }
        textarea.form-control {
            font-size: 1.1em;
            border-radius: 12px;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-check {
            margin-bottom: 10px;
        }
        .form-check-label {
            font-size: 1.05em;
        }
        .btn-yellow {
            background-color: #ffb300;
            border: none;
            font-weight: bold;
            padding: 10px 24px;
            color: white;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-yellow:hover {
            background-color: #ffa000;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }
        .result-box {
            background: #fff8dc;
            border-left: 6px solid #ffb300;
            padding: 18px 25px;
            border-radius: 10px;
            margin-top: 30px;
            font-size: 1.25em;
            font-family: 'Courier New', monospace;
            color: #444;
        }
        .tool-options {
            padding: 20px;
            border-radius: 15px;
            background: #fdf6e3;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔧 Text Encryption Tools</h2>
    <form method="post">
        <div class="mb-4">
            <label for="text" class="form-label">Enter text:</label>
            <textarea class="form-control" id="text" name="text" rows="3" placeholder="Type your message or code here..." required><?= htmlspecialchars($text) ?></textarea>
            <small class="text-muted">🔑 For password verification, format: <code>plain_password|hashed_value</code></small>
        </div>
        <div class="tool-options">
            <label class="form-label">Choose a tool:</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="password_hash" id="hash" <?= $mode === 'password_hash' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="hash">🔑 Secure Password Hash</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="verify_hash" id="verify_hash" <?= $mode === 'verify_hash' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="verify_hash">✅ Verify Password Hash</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="encrypt_egypt" id="egypt_encrypt" <?= $mode === 'encrypt_egypt' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="egypt_encrypt">🪙 Encrypt as Ancient Egyptian</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="decrypt_egypt" id="egypt_decrypt" <?= $mode === 'decrypt_egypt' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="egypt_decrypt">🕵️‍♂️ Decrypt Ancient Egyptian</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="text_to_binary" id="text_binary" <?= $mode === 'text_to_binary' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="text_binary">🔢 Text ➜ Binary</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="binary_to_text" id="binary_text" <?= $mode === 'binary_to_text' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="binary_text">🔠 Binary ➜ Text</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" value="generate_key" id="key_gen" <?= $mode === 'generate_key' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="key_gen">🗝️ Generate Random Key</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-yellow">Generate</button>
            <a href="user_home.php" class="btn btn-yellow">Back to Home</a>
        </div>
    </form>
    <?php if ($result): ?>
        <div class="result-box">
            <strong>Result:</strong>
            <p><?= htmlspecialchars($result) ?></p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
