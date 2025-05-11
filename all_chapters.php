<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}


include 'db.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT c.*, COALESCE(p.progress, 0) as progress
          FROM chapters c
          LEFT JOIN user_chapter_progress p 
          ON c.id = p.chapter_id AND p.user_id = $user_id
          ORDER BY c.id ASC";

$result = $conn->query($query);
$chapters = [];
while ($row = $result->fetch_assoc()) {
    $chapters[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Chapters - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff9e6;
            font-family: 'Segoe UI', sans-serif;
        }
        .container { margin-top: 40px; max-width: 1000px; }
        h2 { color: #92400e; font-weight: bold; text-align: center; }
        .subtitle { text-align: center; color: #6b4c1e; margin-bottom: 30px; }
        .search-box { max-width: 400px; margin: 0 auto 30px auto; }
        .chapter-card {
            background: white; padding: 25px; border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px; transition: all 0.3s ease;
            display: flex; align-items: center; gap: 20px;
        }
        .chapter-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); }
        .chapter-icon { font-size: 32px; color: #f59e0b; }
        .chapter-details { flex-grow: 1; }
        .chapter-details h5 { color: #d97706; font-weight: bold; margin-bottom: 5px; }
        .chapter-details p { color: #4b5563; margin: 0 0 8px; }
        .progress { height: 10px; }
        .progress-bar { background-color: #f59e0b; }
        .btn-view {
            background-color: #f59e0b; color: white;
            border: none; padding: 8px 16px; font-weight: 500; border-radius: 8px;
        }
        .btn-view:hover { background-color: #d97706; }
        .custom-back-btn {
            background-color: #f59e0b; color: white; font-size: 16px;
            padding: 10px 24px; font-weight: 600; border-radius: 10px;
            border: none; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none; display: inline-block;
        }
        .custom-back-btn:hover { background-color: #d97706; transform: scale(1.05); }
    </style>
</head>
<body>
<div class="container">
    <h2>📚 All Chapters</h2>
    <p class="subtitle">Search and track your cryptography learning journey.</p>
    <div class="search-box">
        <input type="text" class="form-control" id="searchInput" placeholder="Search chapter by title...">
    </div>
    <div id="chapterList">
        <?php foreach ($chapters as $chapter): ?>
            <div class="chapter-card" data-title="<?= strtolower($chapter['title']) ?>">
                <div class="chapter-icon">📘</div>
                <div class="chapter-details">
                    <h5><?= htmlspecialchars($chapter['title']) ?></h5>
                    <p><?= htmlspecialchars($chapter['description']) ?></p>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: <?= $chapter['progress'] ?>%;" aria-valuenow="<?= $chapter['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small><?= $chapter['progress'] ?>% completed</small>
                </div>
                <a href="chapters.php?id=<?= $chapter['id'] ?>" class="btn btn-view">View</a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
        <a href="user_home.php" class="custom-back-btn">← Back to Home</a>
    </div>
</div>
<script>
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.chapter-card');
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            card.style.display = title.includes(query) ? 'flex' : 'none';
        });
    });
</script>
</body>
</html>


