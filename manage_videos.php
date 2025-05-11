<?php
include 'db.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $chapter_id = $_POST['chapter_id'];
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];

    $stmt = $conn->prepare("INSERT INTO videos (chapter_id, title, video_url) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $chapter_id, $title, $video_url);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_video'])) {
    $id = $_POST['video_id'];
    $title = $_POST['edit_title'];
    $video_url = $_POST['edit_video_url'];
    $stmt = $conn->prepare("UPDATE videos SET title=?, video_url=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $video_url, $id);
    $stmt->execute();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM videos WHERE id = $id");
    header("Location: manage_videos.php");
    exit();
}

$chapters = $conn->query("SELECT id, title FROM chapters");
$videos = $conn->query("SELECT videos.*, chapters.title as chapter_title FROM videos JOIN chapters ON videos.chapter_id = chapters.id ORDER BY videos.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Videos - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff8cc;
            color: #3e2b1f;
            padding: 40px 0;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background-color: #fffef3;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #a87700;
        }

        form .form-control {
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: #e4b700;
            color: black;
            font-weight: bold;
            border: none;
        }

        .btn-custom:hover {
            background-color: #d1a800;
        }

        .video-card {
            background-color: #fff9da;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .video-card h5 {
            margin: 0;
            font-size: 17px;
            color: #3e2b1f;
        }

        .video-card p {
            font-size: 14px;
            margin: 5px 0;
            color: #6c584c;
        }

        .video-card video {
            width: 100%;
            margin-top: 10px;
            border-radius: 8px;
        }

        .video-card .btn {
            margin-right: 8px;
        }

        #searchInput {
            margin-bottom: 20px;
        }

        .btn-back-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.btn-back {
    background-color: #f4c430;
    color: #4a3300;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: bold;
    border: 2px solid #e1b200;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.btn-back:hover {
    background-color: #ffdd55;
    text-decoration: none;
}


    </style>
</head>
<body>
<div class="container">
   <h2 class="text-center mb-4">
    <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" style="height: 60px; margin-bottom: 10px;"><br>
    🎬 Manage Videos
</h2>
    <form method="POST" class="mt-4">
        <input type="hidden" name="add_video" value="1">
        <select name="chapter_id" class="form-control" required>
            <option disabled selected>Select Chapter</option>
            <?php while ($chapter = $chapters->fetch_assoc()): ?>
                <option value="<?= $chapter['id'] ?>"><?= htmlspecialchars($chapter['title']) ?></option>
            <?php endwhile; ?>
        </select>

        <input type="text" name="title" class="form-control" placeholder="Video Title" required>
        <input type="text" name="video_url" class="form-control" placeholder="Video URL (.mp4 link)" required>
        <button type="submit" class="btn btn-custom w-100 mb-3">➕ Add Video</button>

    </form>

    <input type="text" id="searchInput" class="form-control" placeholder="Search videos...">

    <div class="video-list mt-4">
        <?php while ($video = $videos->fetch_assoc()): ?>
            <div class="video-card" data-title="<?= strtolower($video['title']) ?>">
                <h5><?= htmlspecialchars($video['title']) ?></h5>
                <p>📘 Chapter: <?= htmlspecialchars($video['chapter_title']) ?></p>
                <?php if (str_ends_with($video['video_url'], '.mp4')): ?>
                    <video controls>
                        <source src="<?= htmlspecialchars($video['video_url']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($video['video_url']) ?>" target="_blank">▶ View Video</a>
                <?php endif; ?>
                <div class="mt-2">
                    <button class="btn btn-sm btn-light text-dark" data-bs-toggle="modal" data-bs-target="#editModal<?= $video['id'] ?>">✏️ Edit</button>
                    <a href="?delete=<?= $video['id'] ?>" onclick="return confirm('Delete this video?')">
                        <button class="btn btn-sm btn-danger">🗑 Delete</button>
                    </a>
                </div>
            </div>

            <div class="modal fade" id="editModal<?= $video['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= $video['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" class="modal-content text-dark">
                        <input type="hidden" name="edit_video" value="1">
                        <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLabel<?= $video['id'] ?>">Edit Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="edit_title" class="form-control mb-2" value="<?= htmlspecialchars($video['title']) ?>" required>
                            <input type="text" name="edit_video_url" class="form-control" value="<?= htmlspecialchars($video['video_url']) ?>" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">💾 Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="btn-back-wrapper">
    <a class="btn-back" href="admin_home.php">← Back to Home</a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", function () {
        const search = this.value.toLowerCase();
        document.querySelectorAll(".video-card").forEach(card => {
            const title = card.getAttribute("data-title");
            card.style.display = title.includes(search) ? "" : "none";
        });
    });
</script>
</body>
</html>
