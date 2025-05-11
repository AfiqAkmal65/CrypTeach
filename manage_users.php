<?php
include 'db.php';

session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = intval($_POST['user_id']);
    $newRole = $_POST['new_role'];
    $conn->query("UPDATE users SET role = '$newRole' WHERE id = $userId");
    header("Location: manage_users.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: manage_users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");
$adminCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='admin'")->fetch_assoc()['total'];
$studentCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
    background-color: #fff9e6;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
    max-width: 1100px;
    margin: 60px auto;
    background-color: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.logo {
    display: block;
    margin: 0 auto 20px;
    height: 85px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #4a3300;
    font-weight: bold;
}

.summary {
    text-align: center;
    margin-bottom: 20px;
    color: #5a4300;
}

.summary span {
    margin: 0 10px;
    font-weight: 600;
}

.search-filter {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    gap: 10px;
}

.search-filter input, .search-filter select {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e2c36c;
    background-color: #fffbe6;
}

.table thead {
    background: linear-gradient(to right, #ffdd80, #ffe699);
    color: #4a3300;
}

.table td, .table th {
    vertical-align: middle;
    text-align: center;
}

.btn-delete {
    background-color: #e06666;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
}

.btn-delete:hover {
    background-color: #c0392b;
}

.btn-save {
    background-color: #f5b041;
    color: white;
    padding: 6px 10px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
}

.btn-save:hover {
    background-color: #d49a34;
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
    <img src="uploads/crypteach_logo.png" alt="Logo" class="logo">
    <h2>👤 Manage Users</h2>
    
    <div class="summary">
        <span>Total Users: <?= $totalUsers ?></span>
        <span>Admins: <?= $adminCount ?></span>
        <span>Students: <?= $studentCount ?></span>
    </div>

    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Search by username or email...">
        <select id="roleFilter">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="student">Student</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="userTable">
            <thead class="table-primary text-center">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="POST" class="d-flex justify-content-center gap-2">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="new_role" class="form-select form-select-sm" style="width: auto;">
                                <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>student</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>
                            <button type="submit" name="update_role" class="btn-save">Save</button>
                        </form>
                    </td>
                    <td><?= $user['created_at'] ?></td>
                    <td>
                        <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">
                            <button class="btn-delete">🗑 Delete</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="btn-back-wrapper">
    <a class="btn-back" href="admin_home.php">← Back to Home</a>
</div>

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const table = document.getElementById('userTable').getElementsByTagName('tbody')[0];

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        const role = roleFilter.value;

        Array.from(table.rows).forEach(row => {
            const username = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const userRole = row.cells[3].textContent.toLowerCase();

            const matchSearch = username.includes(search) || email.includes(search);
            const matchRole = role === "" || userRole === role;

            row.style.display = matchSearch && matchRole ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterTable);
    roleFilter.addEventListener("change", filterTable);
</script>
</body>
</html>
