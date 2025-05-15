<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES);

// Fetch profile photo safely
$profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($username);
if ($stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?")) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($photo);
    if ($stmt->fetch() && $photo) {
        $profilePic = 'uploads/profile_photos/' . htmlspecialchars($photo);
    }
    $stmt->close();
}

// Time-based greeting
date_default_timezone_set('Asia/Kuala_Lumpur');
$hour = (int) date('H');
$greet = $hour < 12 ? "Good morning" : ($hour < 18 ? "Good afternoon" : "Good evening");

// Total chapters (safe fallback)
$totalChapters = 1;
if ($res = $conn->query("SELECT COUNT(*) AS total FROM chapters")) {
    $row = $res->fetch_assoc();
    $totalChapters = max((int) $row['total'], 1);
}

// Completed chapters
$completedChapters = 0;
if ($res = $conn->prepare("SELECT COUNT(*) FROM user_chapter_progress WHERE user_id = ? AND progress = 100")) {
    $res->bind_param('i', $userId);
    $res->execute();
    $res->bind_result($completedChapters);
    $res->fetch();
    $res->close();
}

$completion = round(($completedChapters / $totalChapters) * 100);

// Last completed chapter
$last_chapter = "Start Your First Chapter";
if ($stmt = $conn->prepare("
    SELECT chapters.title 
    FROM user_chapter_progress 
    JOIN chapters ON user_chapter_progress.chapter_id = chapters.id 
    WHERE user_chapter_progress.user_id = ? AND user_chapter_progress.progress = 100 
    ORDER BY chapters.id DESC 
    LIMIT 1
")) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($title);
    if ($stmt->fetch()) {
        $last_chapter = htmlspecialchars($title);
    }
    $stmt->close();
}

// Tip of the day
$crypto_tip = "RSA keys are stronger when they use at least 2048 bits.";

// Earned badges
$badges = [];
if ($res = $conn->prepare("SELECT game_name FROM user_game_status WHERE user_id = ? AND badge_earned = 1")) {
    $res->bind_param('i', $userId);
    $res->execute();
    $res->bind_result($game);
    while ($res->fetch()) {
        $name = ucfirst($game);
        $badgeIcon = match ($game) {
            'caesar' => '🔐',
            'base64' => '🧬',
            'reverse' => '🔁',
            default => '🏅'
        };
        $badges[] = "$badgeIcon " . htmlspecialchars($name) . " Master";
    }
    $res->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        background: #fffde7;
        font-family: 'Segoe UI', sans-serif;
    }
    header {
        background: linear-gradient(to right, #fbbf24, #fcd34d);
padding: 18px 40px;
border-bottom-left-radius: 20px;
border-bottom-right-radius: 20px;
display: flex;
justify-content: space-between;
align-items: center;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
font-family: 'Segoe UI', sans-serif;
    }
    .section-title {
        margin-top: 30px;
        font-weight: bold;
    }
    .card-grid {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .card {
        background: #ffffffcc;
        border-radius: 10px;
        padding: 20px;
        flex: 1;
        min-width: 260px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: scale(1.02);
    }
    .progress-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: conic-gradient(#ffb300 <?= $completion ?>%, #ddd <?= $completion ?>%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin: 0 auto;
    }
    .badge-list span {
        background: #fff3cd;
        border-radius: 20px;
        padding: 6px 12px;
        margin: 5px;
        display: inline-block;
        color: #795548;
        font-weight: 600;
    }
    .btn-primary {
        background-color: #ffa000;
        border: none;
    }
    .btn-primary:hover {
        background-color: #ff8f00;
    }
     .wave-emoji {
    display: inline-block;
    animation: wave 2s infinite;
    transform-origin: 70% 70%;
    }  
    /* Chat Bubbles */
.chat-bubble {
  max-width: 80%;
  margin-bottom: 10px;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.4;
  word-wrap: break-word;
}

.chat-user {
  background: #fbbf24;
  color: #fff;
  align-self: flex-end;
  border-bottom-right-radius: 0;
  text-align: right;
}

.chat-ai {
  background: #f0f0f0;
  color: #333;
  align-self: flex-start;
  border-bottom-left-radius: 0;
}

@keyframes wave {
    0% { transform: rotate(0deg); }
    10% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    30% { transform: rotate(14deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
    60% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}
</style>


</head>
<body>

<header>
  <!-- Left: Logo + Greeting -->
   <div style="display: flex; align-items: center; gap: 20px;">
  <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="height: 64px; width: auto;">
  <div>
    <div style="font-size: 24px; font-weight: 600; color: white;">
      <?= $greet ?>, <span style="text-transform: capitalize;"><?= htmlspecialchars($_SESSION['username']) ?></span>
      <span class="wave-emoji">👋🏻</span>
    </div>
    <div style="font-size: 14px; color: #fffbe8; margin-top: 4px;">
      Welcome back to your learning hub
    </div>
  </div>
</div>

 <div style="display: flex; align-items: center; gap: 16px;">
    <a href="profile.php" title="My Profile">
      <img src="<?= $profilePic ?>" alt="Profile" style="height: 42px; width: 42px; object-fit: cover; border-radius: 50%; border: 2px solid white;">
    </a>
    <a href="logout.php" style="
        background-color: white;
        color: #333;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease, transform 0.2s ease;
    " onmouseover="this.style.backgroundColor='#f1f1f1'; this.style.transform='scale(1.05)'" onmouseout="this.style.backgroundColor='white'; this.style.transform='scale(1)'">
        Logout
    </a>
  </div>


</header>

<div class="container mt-4">

<h4 class="section-title">🚀 Quick Access</h4>
<div class="card-grid mb-4">
      <a href="all_chapters.php" class="card text-decoration-none text-dark">
        <h5>📚 View Chapters</h5>
        <p>Browse all learning chapters.</p>
    </a>
    <a href="quiz_list.php" class="card text-decoration-none text-dark">
        <h5>🧪 Take a Quiz</h5>
        <p>Practice with interactive questions.</p>
    </a>
    <a href="videos.php" class="card text-decoration-none text-dark">
        <h5>📺 Watch Videos</h5>
        <p>Visual explanations for complex topics.</p>
    </a>
    <a href="leaderboard.php" class="card text-decoration-none text-dark">
        <h5>🏆 Leaderboard</h5>
        <p>See where you rank!</p>
    </a>
    <a href="games.php" class="card text-decoration-none text-dark">
        <h5>🎮 Crypto Games</h5>
        <p>Play and learn cryptography!</p>
    </a>
    <a href="game_egypt.php" class="card text-decoration-none text-dark">
        <h5>🪄 Decode the Tomb</h5>
        <p>Crack the ancient Egyptian message!</p>
    </a>
    <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
        <h5>🛠️ Encrypt Tools</h5>
        <p>Securely hash or hieroglyphify your text!</p>
    </a>
</div>

<h4 class="section-title">📍 Continue Learning</h4>
<div class="card mb-4 text-center">
    <?php if ($completion >= 100): ?>
        <h5>🎉 All Chapters Completed!</h5>
        <p>You've finished all learning chapters. Great job!</p>
        <div class="progress-circle mb-3" style="background: conic-gradient(#4caf50 100%, #ddd 0%);"><?= $completion ?>%</div>
        <a href="all_chapters.php" class="btn btn-success">Review Chapters</a>
    <?php else: ?>
        <h5><?= $last_chapter ?></h5>
        <p>You're <?= $completion ?>% done. Keep going!</p>
        <div class="progress-circle mb-3"><?= $completion ?>%</div>
        <a href="chapters.php?id=2" class="btn btn-primary">Continue</a>
    <?php endif; ?>
</div>


<h4 class="section-title">📰 Crypto Insight</h4>
<div class="card mb-4">
    <p><?= $crypto_tip ?></p>
</div>

<h4 class="section-title">🏅 Your Badges</h4>
<div class="card badge-list mb-4">
    <?php if (empty($badges)): ?>
        <p>You haven't earned any badges yet. Play the games to unlock them!</p>
    <?php else: ?>
        <?php foreach ($badges as $badge): ?>
            <span><?= $badge ?></span>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h4 class="section-title">📖 Digital Book</h4>
<div class="card-grid mb-4">
    <a href="book.php?page=1" class="card text-decoration-none text-dark">
        <h6>📄 Introduction to Cryptography</h6>
        <p>Explore the foundations and history of cryptography.</p>
    </a>
    <a href="book.php?page=2" class="card text-decoration-none text-dark">
        <h6>🔐 Symmetric Encryption</h6>
        <p>Understand encryption techniques using a single key.</p>
    </a>
    <a href="book.php?page=3" class="card text-decoration-none text-dark">
        <h6>🗝️ Public Key Cryptography</h6>
        <p>Dive into RSA, Diffie-Hellman, and more.</p>
    </a>
    <a href="book.php?page=4" class="card text-decoration-none text-dark">
        <h6>🏺 Ancient Egyptian Cryptography</h6>
        <p>Learn how scribes used symbols to hide sacred messages.</p>
    </a>
</div>

<h4 class="section-title">🧰 Quick Tools</h4>
<div class="card-grid mb-4">
    <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
        <h6>🔢 Base Converter</h6>
        <p>Convert between binary, hex, and more.</p>
    </a>
    <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
        <h6>🔐 Hash Generator</h6>
        <p>Generate SHA-256, MD5 hashes easily.</p>
    </a>
    <a href="encrypt_tools.php" class="card text-decoration-none text-dark">
        <h6>🧮 Key Generator</h6>
        <p>Create strong encryption keys.</p>
    </a>
</div>

<h4 class="section-title">🔓 Weekly Challenge</h4>
<div class="card mb-5">
    <p><strong>Hint:</strong> Caesar Cipher, Shift by 2</p>
    <p><strong>Encrypted:</strong> jgnnq</p>
    <form method="post">
        <input type="text" name="answer" placeholder="Enter decrypted word..." class="form-control mb-2" required>
        <button class="btn btn-success">Submit</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $attempt = strtolower(trim($_POST['answer']));
        echo $attempt === 'hello'
            ? '<div class="alert alert-success mt-2">✅ Correct! You cracked the cipher!</div>'
            : '<div class="alert alert-danger mt-2">❌ Not quite. Try again!</div>';
    }
    ?>
</div>


</div>

<footer style="background-color: #fff8d6; text-align: center; padding: 20px; font-size: 14px; color: #555; border-top: 1px solid #ccc; margin-top: 40px;">
    © 2025, <strong>CrypTeach</strong> — Learn, Encrypt, Empower.
</footer>
<?php if (isset($_SESSION['message'])): ?>

<script>
Swal.fire({
  title: "Welcome Back!",
  text: "<?= addslashes($_SESSION['message']) ?>",
  icon: "success",
  confirmButtonText: "OK",
  timer: 2500
});
</script>

<?php unset($_SESSION['message']); endif; ?>

<script>
function toggleDropdown() {
    const menu = document.getElementById('profileDropdown');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

// Optional: Close dropdown if clicked outside
window.onclick = function(event) {
    if (!event.target.closest('.dropdown')) {
        document.getElementById('profileDropdown').style.display = 'none';
    }
}
</script>

<style>
.dot-flashing {
  position: relative;
  width: 8px;
  height: 8px;
  border-radius: 5px;
  background-color: #fbbf24;
  color: #fbbf24;
  animation: dotFlashing 1s infinite linear alternate;
  animation-delay: 0.5s;
}

@keyframes dotFlashing {
  0% { background-color: #fbbf24; }
  50%, 100% { background-color: #fffbe8; }
}

#messages div {
    margin-bottom: 8px;
}

/* Chatbox Container */
#chatbox {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 340px;
  height: 460px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 1000;
  border: 1px solid #e0e0e0;
  font-family: 'Segoe UI', sans-serif;
}

/* Chat Header */
#chat-header {
  background: #fbbf24;
  color: #fff;
  padding: 14px 16px;
  font-weight: bold;
  text-align: center;
  font-size: 16px;
}

/* Chat Messages */
#chat-messages {
  flex: 1;
  padding: 12px;
  overflow-y: auto;
  font-size: 14px;
  background: #fefefe;
}

/* Chat Input Section */
#chat-input {
  display: flex;
  border-top: 1px solid #ddd;
  background: #fafafa;
}

#chat-input input {
  flex: 1;
  padding: 10px;
  border: none;
  outline: none;
  font-size: 14px;
  background: transparent;
}

#chat-input button {
  background: #fbbf24;
  border: none;
  color: white;
  padding: 0 16px;
  cursor: pointer;
  font-weight: bold;
}

/* Toggle Button */
#chat-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #fbbf24;
  color: #fff;
  font-weight: bold;
  padding: 12px 20px;
  border: none;
  border-radius: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  cursor: pointer;
}

</style>

<!-- Chatbox -->
<div id="chatbox">
  <div id="chat-header">CrypTeach AI</div>
  <div id="chat-messages"></div>
  <div id="chat-input">
    <input type="text" id="userMessage" placeholder="Ask CrypTeach AI...">
    <button onclick="sendMessage()">Send</button>
  </div>
</div>

<!-- Toggle Button -->
<button id="chat-toggle" onclick="toggleChatbox()">💬 Ask CrypTeach AI</button>


<script>
    function toggleChatbox() {
  const chat = document.getElementById('chatbox');
  chat.style.display = (chat.style.display === 'flex') ? 'none' : 'flex';
}

function sendMessage() {
  const input = document.getElementById('userMessage');
  const message = input.value.trim();
  if (!message) return;

  const messagesDiv = document.getElementById('chat-messages');

  const userMsg = document.createElement('div');
  userMsg.style.marginBottom = '8px';
  userMsg.style.textAlign = 'right';
  userMsg.className = 'chat-bubble chat-user';
  userMsg.textContent = message;
  messagesDiv.appendChild(userMsg);

  const typingMsg = document.createElement('div');
  typingMsg.id = 'typing';
  typingMsg.innerHTML = `<em>Typing...</em>`;
  messagesDiv.appendChild(typingMsg);

  input.value = '';
  messagesDiv.scrollTop = messagesDiv.scrollHeight;

  fetch('gemini_chat.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'message=' + encodeURIComponent(message)
  })
  .then(response => response.text())
  .then(data => {
    document.getElementById('typing').remove();

    const aiMsg = document.createElement('div');
    aiMsg.style.marginBottom = '8px';
    aiMsg.className = 'chat-bubble chat-ai';
    aiMsg.innerHTML = markdownToHtml(data);
    messagesDiv.appendChild(aiMsg);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
  })
  .catch(error => {
    document.getElementById('typing').remove();
    const errMsg = document.createElement('div');
    errMsg.style.color = 'red';
    errMsg.innerText = 'Error: ' + error;
    messagesDiv.appendChild(errMsg);
  });
}

function markdownToHtml(md) {
  return md
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code>$1</code>')
    .replace(/\n/g, '<br>');
}

// 👇 Enable Enter key to submit message
document.getElementById('userMessage').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    sendMessage();
  }
});
</script>
</body>
</html>  