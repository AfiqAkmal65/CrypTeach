<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CrypTeach</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="header-left">
        <h1>CrypTeach</h1>
    </div>
    <div class="header-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span style="margin-right: 10px;">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="logout">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

<main>
<?php if ($_SESSION['role'] === 'admin'): ?>
<section class="admin-panel" style="margin-top: 50px;">
    <h2 style="color: #c0392b;">Admin Panel</h2>
    <div class="menu-grid">
        <a href="manage_chapters.php" class="menu-item">📚 Manage Chapters</a>
        <a href="manage_users.php" class="menu-item">👥 Manage Users</a>
        <a href="manage_videos.php" class="menu-item">🎞️ Manage Videos</a>
        <a href="admin_dashboard.php" class="menu-item">📊 Admin Dashboard</a>
    </div>
</section>
<?php endif; ?>

        <!-- Menu Section -->
        <section class="menu">
    <h2>Menu</h2>
    <div class="menu-grid">
        <a href="chapter.php" class="menu-item">📖 Chapters</a>
        <a href="quiz_list.php" class="menu-item">🧪 Quizzes</a>
        <a href="videos.php" class="menu-item">📺 Videos</a>
        <a href="leaderboard.php" class="menu-item">🏆 Leaderboard</a>
    </div>
</section>

</main>

<!-- Show popup welcome message -->
<?php if (isset($_SESSION['message'])): ?>
    <script>alert("<?= addslashes($_SESSION['message']) ?>");</script>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<footer style="background-color: #f0f4f8; text-align: center; padding: 20px; font-size: 14px; color: #555; border-top: 1px solid #ddd; margin-top: 40px;">
    © 2025, <strong>CrypTeach</strong> — Learn, Encrypt, Empower.
</footer>

</body>
</html>
