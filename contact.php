<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact - CrypTeach</title>
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
    label {
      display: block;
      margin: 15px 0 5px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      background: #2a2a2a;
      border: 1px solid #555;
      border-radius: 6px;
      color: #fff;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background: #d6b347;
      border: none;
      color: #000;
      border-radius: 6px;
      font-weight: bold;
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
  <h1>Contact Us</h1>
</header>

<div class="content">
  <h2>We’d Love to Hear From You!</h2>
  <form method="post" action="#">
    <label>Your Name</label>
    <input type="text" name="name" required>

    <label>Your Email</label>
    <input type="email" name="email" required>

    <label>Your Message</label>
    <textarea name="message" rows="5" required></textarea>

    <button type="submit">Send Message</button>
  </form>

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
