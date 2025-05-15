<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info
$result = $conn->query("SELECT username, email, profile_photo FROM users WHERE id = $userId");
$user = $result->fetch_assoc();
$photoPath = !empty($user['profile_photo']) ? "uploads/profile_photos/" . $user['profile_photo'] : "https://ui-avatars.com/api/?name=" . urlencode($user['username']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $conn->real_escape_string($_POST['username']);
    $newEmail = $conn->real_escape_string($_POST['email']);
    $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $photoFileName = $user['profile_photo']; // Keep current photo by default
    $uploadOk = true;

    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/profile_photos/";
        $fileName = basename($_FILES["profile_photo"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif"];
        $newFileName = uniqid() . "." . $fileExt;

        if (in_array($fileExt, $allowed)) {
            move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetDir . $newFileName);
            $photoFileName = $newFileName;
        } else {
            $uploadOk = false;
            $error = "Only JPG, PNG, or GIF files are allowed.";
        }
    }

    if ($uploadOk) {
        $query = "UPDATE users SET username='$newUsername', email='$newEmail', profile_photo='$photoFileName'";
        if ($newPassword) {
            $query .= ", password='$newPassword'";
        }
        $query .= " WHERE id = $userId";

        if ($conn->query($query)) {
            $_SESSION['username'] = $newUsername;
            $_SESSION['message'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            $error = "Update failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body style="background: #fffde7; font-family: 'Segoe UI', sans-serif;">
<div class="container mt-5" style="max-width: 600px;">
  <div style="background: #fff; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px;">
    <h3 style="font-weight: bold; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
      <i class="fas fa-user-circle" style="color: #d4a017;"></i> My Profile
    </h3>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="text-center mb-3">
        <img src="<?= $photoPath ?>" alt="Profile Photo" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #fbbf24;">
        <input type="file" name="profile_photo" accept="image/*" class="form-control mt-2">
      </div>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">New Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn" style="background-color: #fbbf24; color: #fff; font-weight: 600;">Save Changes</button>
        <a href="user_home.php" class="btn btn-secondary">Back</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
