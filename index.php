<?php
session_start();
include 'db.php';

// Insert visit
$user_ip = $_SERVER['REMOTE_ADDR'];
$conn->query("INSERT INTO site_visits (ip_address) VALUES ('$user_ip')");

// Count total and unique visits
$visitResult = $conn->query("SELECT COUNT(*) AS total FROM site_visits");
$totalVisits = $visitResult->fetch_assoc()['total'] ?? 0;

$uniqueResult = $conn->query("SELECT COUNT(DISTINCT ip_address) AS unique_visitors FROM site_visits");
$uniqueVisitors = $uniqueResult->fetch_assoc()['unique_visitors'] ?? 0;

// Get user comments
$commentsResult = $conn->query("SELECT name, comment, created_at FROM comments ORDER BY created_at DESC");
$commentsData = [];
while ($row = $commentsResult->fetch_assoc()) {
    $commentsData[] = [
        'name' => htmlspecialchars($row['name']),
        'comment' => nl2br(htmlspecialchars($row['comment'])),
        'date' => date('M d, Y H:i', strtotime($row['created_at']))
    ];
}

// Count users who have logged in
$countResult = $conn->query("SELECT COUNT(*) AS total_logged_in FROM users WHERE login_count > 0");
$totalLoggedIn = $countResult->fetch_assoc()['total_logged_in'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to CrypTeach</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #1a1a1a; /* updated from #121212 or #000000 */
  color: #f5f5f5;
}


  .site-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    background-color: #d6b347;
    color: #000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  }

  .site-header h1 {
    font-size: 28px;
    margin: 0;
    color: #000;
  }

  .header-right a {
    margin-left: 15px;
    color: #000;
    text-decoration: none;
    font-weight: 500;
  }

  .container, .about-section, .comment-section {
    max-width: 750px;
    margin: 40px auto;
  background-color: #232323;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.05);
    text-align: center;
  }

  .container h2 {
    color: #f5f5f5;
    font-weight: bold;
    font-size: 28px;
  }

  .container p {
    color: #ddd;
  }

  .cta-button {
    background-color: #d6b347;
    color: #000;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
    transition: background-color 0.3s;
    box-shadow: 0 0 10px rgba(214, 179, 71, 0.3);
  }

  .cta-button:hover {
    background-color: #bfa031;
  }

  .features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
  }

  .feature-box {
      background-color: #232323;
    border-radius: 10px;
    padding: 25px 20px;
    width: 260px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    color: #ddd;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .feature-box:hover {
    transform: scale(1.03);
    box-shadow: 0 0 20px rgba(214, 179, 71, 0.35);
  }

  .feature-box h4 {
    color: #d6b347;
    margin-bottom: 10px;
  }

  .info-rotate-box {
    margin-top: 30px;
    padding: 15px 25px;
    background-color: #2c2c2c;
    border-left: 4px solid #d6b347;
    border-radius: 6px;
    font-weight: bold;
    font-size: 16px;
    color: #f1f1f1;
    box-shadow: 0 0 8px rgba(214, 179, 71, 0.15);
  }

  .upcoming ul {
    list-style: none;
    padding: 0;
    font-size: 15px;
  }

  .upcoming ul li {
    background-color: #1e1e1e;
    color: #f5f5f5;
    border-radius: 6px;
    margin: 8px 0;
    padding: 12px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .upcoming ul li:hover {
    background-color: #2d2d2d;
  }

  .comment-section input,
  .comment-section textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #444;
    background: #1a1a1a;
    color: #f5f5f5;
  }

  .comment-section button {
    background-color: #d6b347;
    color: #000;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: bold;
    box-shadow: 0 0 10px rgba(214, 179, 71, 0.3);
    transition: background-color 0.3s;
  }

  .comment-section button:hover {
    background-color: #c2a236;
  }

  .comment-display {
    margin-top: 20px;
  }

  footer {
    text-align: center;
    margin: 40px 0 20px;
    color: #aaa;
  }

.header-right {
  display: flex;
  align-items: center;
}


.visitor-box {
  background-color: #ffffff !important;
  padding: 20px 30px;
  border-radius: 10px;
  box-shadow: 0 0 12px rgba(214, 179, 71, 0.2);
  text-align: center;
  transition: transform 0.3s;
}

.visitor-box i {
  font-size: 30px !important;
  color: #d6b347 !important;
}

.visitor-box h3 {
  margin-top: 10px;
  font-size: 24px !important;
  color: #222 !important;
  font-weight: bold !important;
}

.visitor-box p {
  margin: 0 !important;
  color: #444 !important;
  font-weight: 500 !important;
}


  /* Fix dark headers on black background */
h3 {
  color: #f5f5f5;
  font-weight: 600;
  margin-bottom: 20px;
}

.comment-display h3,
.comment-section h3 {
  color: #f5f5f5;
  font-weight: 700;
}
.upcoming h4 {
  color: #f5f5f5;
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 20px;
}
.header-right .header-btn {
  padding: 10px 20px;
  margin-left: 12px;
  background-color: #000;
  color: #d6b347;
  border-radius: 999px; /* fully rounded pill style */
  font-weight: 600;
  text-decoration: none;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  transition: all 0.3s ease-in-out;
  border: none;
  display: inline-block;
}

.header-right .header-btn.signup {
  background-color: #fff;
  color: #000;
  font-weight: bold;
}

.header-right .header-btn:hover {
  background-color: #1c1c1c;
  color: #ffd700;
  box-shadow: 0 0 10px #d6b347;
  transform: translateY(-2px);
}




</style>

</head>
<body>
<header class="site-header">
  <div class="header-left" style="display: flex; align-items: center;">
    <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="height: 60px; margin-right: 15px;">
    <h1 style="margin: 0; font-size: 26px;">CrypTeach</h1>
  </div>
  <div class="header-right">
  <a href="about.php" class="header-btn">About</a>
  <a href="contact.php" class="header-btn">Contact</a>
  <a href="login.php" class="header-btn">Login</a>
  <a href="signup.php" class="header-btn signup">Sign Up</a>
</div>

</header>

<section class="about-section">
    <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="width: 500px; margin-bottom: 10px;">
    <h3>🔍 About CrypTeach</h3>
    <p><strong>CrypTeach</strong> is your guide to mastering cryptography. From encryption basics to hands-on quizzes, this platform makes complex topics easy and fun to understand.</p>
  </section>
  
<main class="container">


  <h2>Master Cryptography the Smart Way</h2>
  <p>Learn through interactive chapters, videos, and quizzes. Start your journey now!</p>
  <a href="signup.php" class="cta-button">Get Started</a>
  <div class="info-rotate-box" id="rotatingMessage">💡 Cryptography is the foundation of secure communication.</div>

  <!-- Visitors Display -->
<div style="display: flex; justify-content: center; gap: 40px; margin-top: 40px;">
  <div style="background-color: #ffffff; padding: 20px 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; color: #222;">
    <i class="fas fa-users" style="font-size: 30px; color: #b78c2a;"></i>
    <h3 style="margin-top: 10px; font-size: 24px; font-weight: bold; color: #222;"><?= $totalVisits ?></h3>
    <p style="margin: 0; color: #444; font-weight: 500;">Total Visits</p>
  </div>
  <div style="background-color: #ffffff; padding: 20px 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; color: #222;">
    <i class="fas fa-user" style="font-size: 30px; color: #b78c2a;"></i>
    <h3 style="margin-top: 10px; font-size: 24px; font-weight: bold; color: #222;"><?= $uniqueVisitors ?></h3>
    <p style="margin: 0; color: #444; font-weight: 500;">Unique Visitors</p>
  </div>
</div>

</main>

 <section class="features">
    <div class="feature-box"><h4>🧠 Interactive Learning</h4><p>Understand cryptography through videos and diagrams.</p></div>
    <div class="feature-box"><h4>🎮 Gamified Experience</h4><p>Earn points, level up and see your name on the leaderboard.</p></div>
    <div class="feature-box"><h4>📈 Track Progress</h4><p>Monitor your growth as you learn each chapter.</p></div>
  </section>


  <section class="container upcoming">
    <h4>🚀 Coming Soon</h4>
    <ul>
      <li>🔐 Live Workshops with Cybersecurity Experts</li>
      <li>🛡️ Advanced Encryption & RSA Visualization</li>
      <li>📱 Mobile App for Android & iOS</li>
      <li>🧹 Real-life Cryptography Case Studies</li>
      <li>🗂️ Printable Cheat Sheets & Flashcards</li>
      <li>🕵️ Capture-the-Flag Style Hacking Games</li>
      <li>🌐 Blockchain & Digital Signature Modules</li>
    </ul>
  </section>

  <section class="comment-section">
    <h3>💬 Leave Us a Comment</h3>
    <form method="post" action="submit_comment.php">
      <input type="text" name="name" placeholder="Your Name" required>
      <textarea name="comment" placeholder="Your Comment..." required></textarea>
      <button type="submit">Submit Comment</button>
    </form>
  </section>

  <section class="comment-display container">
    <h3>🖊 What Our Users Say</h3>
    <div id="commentRotator" style="min-height: 100px; color: #333;"></div>
  </section>

  <footer>
    © <?= date('Y') ?> CrypTeach. All rights reserved.
  </footer>

<script>
  const messages = [
    "💡 Cryptography is the foundation of secure communication.",
    "🔐 Learn how encryption protects your data online.",
    "📚 CrypTeach lets you explore chapters, videos, and quizzes!",
    "🏆 Compete on the leaderboard and track your progress.",
    "🎓 Perfect for students learning cybersecurity basics."
  ];
  let index = 0;
  const messageBox = document.getElementById('rotatingMessage');
  setInterval(() => {
    index = (index + 1) % messages.length;
    messageBox.style.opacity = 0;
    setTimeout(() => {
      messageBox.textContent = messages[index];
      messageBox.style.opacity = 1;
    }, 500);
  }, 3000);

  const comments = <?= json_encode($commentsData) ?>;
  let cIndex = 0;
  const commentBox = document.getElementById('commentRotator');
  function showNextComment() {
    if (comments.length === 0) {
      commentBox.innerHTML = "<p style='color:#777'>No comments yet.</p>";
      return;
    }
    const c = comments[cIndex];
    commentBox.style.opacity = 0;
    setTimeout(() => {
      commentBox.innerHTML = `
        <div style="background: rgba(255,255,255,0.6); padding: 12px; border-radius: 8px;">
          <strong>${c.name}</strong><br>
          <p>${c.comment}</p>
          <small style="color:#888;">🕒 ${c.date}</small>
        </div>`;
      commentBox.style.opacity = 1;
    }, 400);
    cIndex = (cIndex + 1) % comments.length;
  }
  showNextComment();
  setInterval(showNextComment, 4000);
</script>
</body>
</html>
