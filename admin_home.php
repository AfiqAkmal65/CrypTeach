<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$countResult = $conn->query("SELECT COUNT(*) AS total_logged_in FROM users WHERE login_count > 0");
$totalLoggedIn = $countResult->fetch_assoc()['total_logged_in'] ?? 0;
$adminResult = $conn->query("SELECT id, username, role FROM users WHERE role = 'admin'");
$pendingVideos = $conn->query("SELECT id, title, created_at FROM videos WHERE approved = 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CrypTeach Admin</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff8cc;
      color: #333;
    }
    .site-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #fbe287;
      padding: 20px 40px;
      border-bottom: 1px solid #d4b44c;
    }
    .site-header h1 {
      margin: 0;
      font-size: 26px;
      color: #3e2b1f;
      display: flex;
      align-items: center;
    }
    .site-header img {
      height: 40px;
      margin-right: 12px;
    }
    .header-right span {
      margin-right: 12px;
      color: #5c441c;
    }
    .header-right a {
      color: #5c441c;
      text-decoration: none;
      font-weight: bold;
      margin-left: 10px;
    }
    .dashboard-card {
      max-width: 600px;
      margin: 60px auto;
      background: #fffdf3;
      border-radius: 16px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }
    .admin-buttons {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
    }
    .admin-buttons a {
      background-color: #d4a017;
      color: #fff;
      padding: 12px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      display: inline-block;
      min-width: 160px;
      transition: background 0.3s ease;
    }
    .admin-buttons a:hover {
      background-color: #b88c11;
    }
    .section {
      max-width: 800px;
      margin: 30px auto;
      background-color: #fffdf3;
      border: 1px solid #e0c97a;
      border-radius: 12px;
      padding: 20px;
    }
    .section h3 {
      margin-bottom: 15px;
      color: #3e2b1f;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border: 1px solid #e8d892;
      text-align: left;
    }
    th {
      background-color: #f5e6a3;
    }
    .approve-btn {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 5px 12px;
      border-radius: 5px;
      cursor: pointer;
    }
    .reject-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 5px 12px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
<header class="site-header">
 <h1>
  <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="height: 60px; width: auto; margin-right: 16px;">
  CrypTeach Admin
</h1>

  <div class="header-right">
    <span>👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="dashboard-card fade-in">
  <h2>Welcome, Admin!</h2>
  <div class="admin-buttons">
    <a href="manage_chapters.php">📘 Manage Chapters</a>
    <a href="manage_videos.php">📺 Manage Videos</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="leaderboard.php">🏆 View Leaderboard</a>
  </div>
</div>

<div class="section">
  <h3>🛡️ Admin Role Management</h3>
  <table>
    <thead><tr><th>Username</th><th>Role</th></tr></thead>
    <tbody>
      <?php while ($admin = $adminResult->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($admin['username']) ?></td>
        <td><?= htmlspecialchars($admin['role']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<div class="section">
  <h3>🕒 Pending Video Approvals</h3>
  <table>
    <thead>
      <tr><th>Title</th><th>Uploaded At</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php while ($video = $pendingVideos->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($video['title']) ?></td>
          <td><?= isset($video['created_at']) ? date('M d, Y H:i', strtotime($video['created_at'])) : '—' ?></td>
          <td>
            <form method="POST" action="process_video_action.php" style="display:inline;">
              <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
              <input type="hidden" name="action" value="approve">
              <button class="approve-btn" type="submit">✅ Approve</button>
            </form>
            <form method="POST" action="process_video_action.php" style="display:inline; margin-left:8px;">
              <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
              <input type="hidden" name="action" value="reject">
              <button class="reject-btn" type="submit">❌ Reject</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer style="text-align: center; padding: 20px; color: #5c441c; border-top: 1px solid #e6d173;">
  © <?= date('Y') ?> CrypTeach. Empowering Secure Learning.
</footer>

<?php if (isset($_SESSION['message'])): ?>
<script>
Swal.fire({
  title: "Notification",
  text: "<?= addslashes($_SESSION['message']) ?>",
  icon: "success",
  confirmButtonText: "OK",
  timer: 2500
});
</script>
<?php unset($_SESSION['message']); endif; ?>

</body>
</html>