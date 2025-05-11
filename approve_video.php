<?php
session_start();
include 'db.php';

// Ensure only admins can access this file
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle POST request to approve a video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'])) {
    $videoId = (int) $_POST['video_id'];

    // Securely update the approved status
    $stmt = $conn->prepare("UPDATE videos SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $videoId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Video approved successfully!";
    } else {
        $_SESSION['message'] = "❌ Failed to approve video.";
    }
}

// Redirect back to admin dashboard
header("Location: admin_home.php");
exit();
?>
