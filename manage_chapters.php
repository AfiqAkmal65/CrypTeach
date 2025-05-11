<?php
include 'db.php';

session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_chapter'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $video = $_POST['video_url'];
    $file_path = null;

    if (isset($_FILES['chapter_file']) && $_FILES['chapter_file']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['chapter_file']['name']);
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $file_path = $target_dir . time() . "_" . $filename;
        move_uploaded_file($_FILES['chapter_file']['tmp_name'], $file_path);
    }

    $stmt = $conn->prepare("INSERT INTO chapters (title, description, content_file, video_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $desc, $file_path, $video);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_chapter'])) {
    $id = $_POST['chapter_id'];
    $title = $_POST['edit_title'];
    $desc = $_POST['edit_description'];
    $video = $_POST['edit_video_url'];
    $stmt = $conn->prepare("UPDATE chapters SET title=?, description=?, video_url=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $desc, $video, $id);
    $stmt->execute();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // First, delete related progress records
    $conn->query("DELETE FROM user_chapter_progress WHERE chapter_id = $id");

    // Then, delete the chapter
    $conn->query("DELETE FROM chapters WHERE id = $id");

    header("Location: manage_chapters.php");
    exit();
}


$chapters = $conn->query("SELECT * FROM chapters ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Chapters - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff8cc;
            color: #3e2b1f;
            padding: 50px 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fffef3;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .chapter-box {
            background-color: #fff9da;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .chapter-box h5 {
            font-weight: bold;
        }
        .chapter-box a {
            color: #e69500;
        }
        .btn-delete {
            background-color: #ff4d4d;
            border: none;
        }
        .btn-delete:hover {
            background-color: #cc0000;
        }
        .btn-submit {
            background-color: #e4b700;
            color: #000;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: #d1a800;
        }
        .form-control, textarea {
            border-radius: 8px;
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
    <img src="https://img.icons8.com/emoji/24/000000/open-book-emoji.png" alt="Book" style="margin-right:8px;">
    <span class="fw-bold text-dark">Manage Chapters</span>
</h2>


    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="add_chapter" value="1">
        <div class="mb-3">
            <input type="text" name="title" class="form-control" placeholder="Chapter Title" required>
        </div>
        <div class="mb-3">
            <textarea name="description" class="form-control" placeholder="Description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <input type="file" name="chapter_file" class="form-control" accept=".pdf,.ppt,.pptx">
        </div>
        <div class="mb-3">
            <input type="text" name="video_url" class="form-control" placeholder="Optional video URL">
        </div>
        <button type="submit" class="btn btn-submit w-100">➕ Add Chapter</button>
    </form>

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search chapters...">

    <?php while ($row = $chapters->fetch_assoc()): ?>
        <div class="chapter-box" data-title="<?= strtolower($row['title']) ?>">
            <h5><?= htmlspecialchars($row['title']) ?></h5>
            <p><?= htmlspecialchars($row['description']) ?></p>

            <?php if ($row['content_file']): ?>
                <?php if (str_ends_with($row['content_file'], '.pdf')): ?>
                    <iframe src="<?= htmlspecialchars($row['content_file']) ?>" width="100%" height="300px" class="my-2" style="border: none;"></iframe>
                <?php else: ?>
                    📄 <a href="<?= htmlspecialchars($row['content_file']) ?>" target="_blank">View PowerPoint</a><br>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($row['video_url']): ?>
                <?php
                preg_match('/(?:youtube\.com.*v=|youtu\.be\/)([^&\n]+)/', $row['video_url'], $matches);
                $yt_id = $matches[1] ?? null;
                ?>
                <?php if ($yt_id): ?>
                    <iframe width="100%" height="250" class="my-2" src="https://www.youtube.com/embed/<?= $yt_id ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    📺 <a href="<?= htmlspecialchars($row['video_url']) ?>" target="_blank">Watch Video</a><br>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-2">
                <button class="btn btn-sm btn-light text-dark" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">✏️ Edit</button>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this chapter?')">
                    <button class="btn btn-sm btn-delete">🗑 Delete</button>
                </a>
            </div>
        </div>

        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editLabel<?= $row['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" class="modal-content text-dark">
                    <input type="hidden" name="edit_chapter" value="1">
                    <input type="hidden" name="chapter_id" value="<?= $row['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLabel<?= $row['id'] ?>">Edit Chapter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="edit_title" class="form-control mb-2" value="<?= htmlspecialchars($row['title']) ?>" required>
                        <textarea name="edit_description" class="form-control mb-2" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>
                        <input type="text" name="edit_video_url" class="form-control" value="<?= htmlspecialchars($row['video_url']) ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">💾 Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endwhile; ?>

    <div class="btn-back-wrapper">
        <a class="btn-back" href="admin_home.php">← Back to Home</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", function () {
        const search = this.value.toLowerCase();
        document.querySelectorAll(".chapter-box").forEach(box => {
            const title = box.getAttribute("data-title");
            box.style.display = title.includes(search) ? "" : "none";
        });
    });
</script>
</body>
</html>
