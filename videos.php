<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
include 'db.php';

// ✅ FIXED QUERY WITH ALIAS
$videos = $conn->query("SELECT videos.*, chapters.title AS chapter_title FROM videos JOIN chapters ON videos.chapter_id = chapters.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Videos - CrypTeach</title>
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        margin: 0;
        background-color: #fffde7; /* soft yellow */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .navbar {
        background-color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .navbar h1 {
        margin: 0;
        font-size: 22px;
        color: #f57c00;
    }

    .navbar .user-info {
        font-size: 14px;
    }

    .navbar .user-info a {
        color: #f57c00;
        margin-left: 8px;
        text-decoration: none;
    }

    .navbar .user-info a:hover {
        text-decoration: underline;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        background: #fffdf3;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .video-title {
        font-size: 20px;
        margin-bottom: 25px;
        text-align: left;
        color: #e65100;
        font-weight: 600;
    }

    .video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .video-card {
        background-color: #fff8c5; /* pale yellow */
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 5px 12px rgba(0,0,0,0.06);
    }

    .video-card video {
        width: 100%;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .video-card p {
        margin: 0;
        font-weight: bold;
        color: #444;
    }

    .video-card small {
        color: #8d6e63;
    }

    .btn-back {
    display: inline-block;
    padding: 10px 25px;
    background-color: #f57c00;
    color: white;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}
.btn-back:hover {
    background-color: #e65100;
}


</style>

</head>
<body>



<div class="container">
    <h2 class="video-title">📺 All Videos</h2>
    <div class="video-grid">
        <?php while ($video = $videos->fetch_assoc()): ?>
            <div class="video-card">
                <video controls>
                    <source src="<?= htmlspecialchars($video['video_url']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <p><?= htmlspecialchars($video['title']) ?> <br><small>Chapter: <?= htmlspecialchars($video['chapter_title']) ?></small></p>
            </div>
        <?php endwhile; ?>
    </div>
    <div style="text-align: center; margin-top: 40px;">
    <a href="user_home.php" class="btn-back">← Back to Home</a>
</div>



</body>
</html>
