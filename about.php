<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About - CrypTeach</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #121212;
      color: #f5f5f5;
    }
    header {
      background-color: #d6b347;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 {
      margin: 0;
      font-size: 26px;
      color: #000;
    }
    .content {
      max-width: 750px;
      margin: 60px auto;
      padding: 40px;
      background: #1e1e1e;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(255, 215, 0, 0.1);
    }
    h2 {
      color: #d6b347;
    }
    a.back {
      display: inline-block;
      margin-top: 30px;
      color: #d6b347;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
<header>
  <h1>About CrypTeach</h1>
</header>

<div class="content">
 <div style="display: flex; align-items: flex-start; gap: 20px;">
  <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="height: 300px; border-radius: 10px;">

  <div>
    <h2>Who We Are</h2>
    <p>
      CrypTeach is an interactive learning platform designed to make cryptography fun and accessible.
      Whether you're a student, hobbyist, or cybersecurity enthusiast, we guide you from basic to advanced concepts
      with videos, games, and quizzes.
        <li><strong>Email:</strong> support@crypteach.my</li>
    </p>
  </div>
</div>

  <h2>Our Mission</h2>
  <p>To empower learners to understand, apply, and appreciate the power of encryption in the digital age — through engaging tools, tutorials, and hands-on experiences.</p>



  <h2>Meet the Developer</h2>
  <img src="uploads/afiq.jpg" alt="Developer Photo" style="width: 120px; border-radius: 50%; margin-bottom: 15px;">
<p>
  Hi! 👋 I'm <strong>MUHAMMAD AFIQ AKMAL BIN OTHMAN</strong>, the developer behind CrypTeach. I'm passionate about cybersecurity, education, and making learning enjoyable through technology.
  This project was developed as part of my Final Year Project to bridge the gap between cryptography theory and practical application.
</p>
<ul>
  <li><strong>Email:</strong> afiqcrypteach@gmail.com</li>
  <li><strong>Institution:</strong> MANAGEMENT SCIENCE AND UNIVERSITY</li>
  <li><strong>Specialization:</strong> Cybersecurity / Software Engineering</li>

</ul>


<div style="
  margin-top: 40px;
  display: flex;
  justify-content: center;
">
  <a href="index.php" class="back" style="
    display: inline-block;
    background-color: #2c2c2c;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    color: #d6b347;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: background-color 0.3s ease;
  " onmouseover="this.style.backgroundColor='#3a3a3a'" onmouseout="this.style.backgroundColor='#2c2c2c'">
    <i class="fas fa-arrow-left"></i> Back to Home
  </a>
</div>

</div>
</body>
</html>
