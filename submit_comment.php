<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $comment = trim($_POST['comment']);

    if (!empty($name) && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (name, comment) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $comment);
        $stmt->execute();

        $_SESSION['message'] = "✅ Thank you for your comment!";
    } else {
        $_SESSION['message'] = "❌ Name and comment cannot be empty.";
    }
}

header("Location: index.php");
exit();
?>
