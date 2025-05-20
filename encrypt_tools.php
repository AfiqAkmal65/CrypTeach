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
    background: linear-gradient(to bottom right, #fffde7, #fff3e0);
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}

.container {
    max-width: 850px;
    margin: 50px auto;
    background: #ffffff;
    padding: 40px 50px;
    border-radius: 25px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    font-weight: 900;
    text-align: center;
    margin-bottom: 30px;
    color: #ff8f00;
    font-size: 2rem;
}

textarea.form-control {
    font-size: 1.1em;
    border-radius: 12px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}

.form-label {
    font-weight: 600;
    margin-bottom: 8px;
}

.form-check {
    margin-bottom: 12px;
    transition: transform 0.2s ease;
}

.form-check:hover {
    transform: scale(1.02);
}

.form-check-input:checked {
    background-color: #ffb300;
    border-color: #ffb300;
}

.btn-yellow {
    background: linear-gradient(to right, #ffb300, #ffa000);
    border: none;
    font-weight: bold;
    padding: 10px 24px;
    color: white;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-yellow:hover {
    background: #ff8f00;
    transform: scale(1.05);
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
    animation: popIn 0.4s ease;
}

@keyframes popIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.tool-options {
    padding: 20px 25px;
    border-radius: 15px;
    background: #fdf6e3;
    margin-top: 20px;
    border-left: 5px solid #ffcc80;
}

    </style>
</head>
<body>
<div class="container">
    <h2>🛠️ Cryptography & Encoding Playground</h2>

    <form method="post">
        <div class="mb-4">
            <label for="text" class="form-label">Enter text:</label>
            <textarea class="form-control" id="text" name="text" rows="3" placeholder="Type your message or code here..." required><?= htmlspecialchars($text) ?></textarea>
            <small class="text-muted">🔑 For password verification, format: <code>plain_password|hashed_value</code></small>
        </div>
       <div class="accordion" id="toolAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingHash">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHash" aria-expanded="true">
        🔐 Hashing & Keys
      </button>
    </h2>
    <div id="collapseHash" class="accordion-collapse collapse show" data-bs-parent="#toolAccordion">
      <div class="accordion-body">
        <label><input type="radio" name="mode" value="password_hash"> 🔑 Secure Password Hash</label><br>
        <label><input type="radio" name="mode" value="verify_hash"> ✅ Verify Password Hash</label><br>
        <label><input type="radio" name="mode" value="generate_key"> 🗝️ Generate Random Key</label>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <h2 class="accordion-header" id="headingEgypt">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEgypt">
        🏺 Ancient Egyptian Tools
      </button>
    </h2>
    <div id="collapseEgypt" class="accordion-collapse collapse" data-bs-parent="#toolAccordion">
      <div class="accordion-body">
        <label><input type="radio" name="mode" value="encrypt_egypt"> 🧬 Encrypt as Ancient Egyptian</label><br>
        <label><input type="radio" name="mode" value="decrypt_egypt"> 🕵️ Decrypt Ancient Egyptian</label>
      </div>
    </div>
  </div>

  <div class="accordion-item">
    <h2 class="accordion-header" id="headingBinary">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBinary">
        🧮 Binary Converter
      </button>
    </h2>
    <div id="collapseBinary" class="accordion-collapse collapse" data-bs-parent="#toolAccordion">
      <div class="accordion-body">
        <label><input type="radio" name="mode" value="text_to_binary"> 🔢 Text ➜ Binary</label><br>
        <label><input type="radio" name="mode" value="binary_to_text"> 🔠 Binary ➜ Text</label>
      </div>
    </div>
  </div>
</div>


<!-- RESULT BOX: Put here for better visibility -->
<?php if ($result): ?>
    <div class="alert shadow-sm mb-4" style="background-color: #fff8dc; border-left: 6px solid #ffb300; padding: 20px; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; font-weight: 700; color: #333;">🔐 Result:</h5>
            <button onclick="copyResult()" class="btn btn-sm btn-warning" style="font-weight: 600;">📋 Copy</button>
        </div>
        <pre style="margin-top: 10px; white-space: pre-wrap; font-size: 1.1em; font-family: 'Courier New', monospace; color: #333;" id="resultBox"><?= htmlspecialchars($result) ?></pre>
    </div>
<?php endif; ?>

        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-yellow">Generate</button>
            <a href="user_home.php" class="btn btn-yellow">Back to Home</a>
        </div>
    </form>

</div>
<script>
document.querySelectorAll('input[name="mode"]').forEach((radio) => {
  radio.addEventListener('change', () => {
    const textField = document.getElementById('text');
    if (radio.value === 'generate_key') {
      textField.disabled = true;
      textField.placeholder = "No input needed for key generation";
      textField.value = '';
    } else {
      textField.disabled = false;
      textField.placeholder = "Type your message or code here...";
    }
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyResult() {
  const resultText = document.getElementById('resultBox').innerText;
  navigator.clipboard.writeText(resultText).then(() => {
    alert("✅ Result copied to clipboard!");
  });
}
</script>


</body>
</html>
