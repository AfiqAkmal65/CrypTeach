<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'], $_POST['action'])) {
    $videoId = (int) $_POST['video_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE videos SET approved = 1 WHERE id = ?");
        $stmt->bind_param("i", $videoId);
        $stmt->execute();
        $_SESSION['message'] = "✅ Video approved successfully!";
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->bind_param("i", $videoId);
        $stmt->execute();
        $_SESSION['message'] = "❌ Video rejected and removed.";
    }
}

header("Location: admin_home.php");
exit();
?>
