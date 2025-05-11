<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<h2>Admin Dashboard</h2>
<ul>
    <li><a href="manage_chapters.php">Manage Chapters</a></li>
    <li><a href="manage_videos.php">Manage Videos</a></li>
    <li><a href="manage_users.php">Manage Users</a></li>
    <li><a href="manage_quizzes.php">Manage Quiz</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>