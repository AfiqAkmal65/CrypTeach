<?php
session_start();
include 'db.php';

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}

$loginError = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['message'] = 'Welcome back, ' . $user['username'] . '!';
        header("Location: " . ($user['role'] === 'admin' ? 'admin_home.php' : 'user_home.php'));
        exit();
    } else {
        $loginError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to bottom right, #0d0d0d, #1a1a1a, #2a2a2a);
    color:rgb(15, 14, 14);
}


        .login-section {
            min-height: 100vh;
        }

        .login-container {
            background-color: #fff9e6;
            border: 2px solid #c9a53f;
            border-radius: 16px;
           box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);

            padding: 40px;
            transition: all 0.3s ease;
            max-width: 400px;
            width: 100%;
        }
        .login-container:hover {
    transform: scale(1.01);
}

        .shake {
            animation: shake 0.4s;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            50% { transform: translateX(8px); }
            75% { transform: translateX(-4px); }
            100% { transform: translateX(0); }
        }

        .logo-image {
            max-width: 400px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8c6e4e;
        }

        .position-relative {
            position: relative;
        }

        .btn-primary {
            background-color: #c9a53f;
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #b8902f;
        }

        .dark-mode {
            background-color: #1f1f1f !important;
            color: #eaeaea !important;
        }

        .dark-mode input,
        .dark-mode .login-container {
            background-color: #2d2d2d !important;
            color: #fff !important;
            border-color: #444;
        }

        .dark-mode .btn-primary {
            background-color: #00c6ff;
        }
    </style>
</head>
<body>
<div class="container-fluid login-section d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center align-items-center" style="max-width: 1000px;">
        <!-- Logo Column -->
        <div class="col-md-6 d-flex align-items-center justify-content-center mb-4 mb-md-0">
            <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" class="logo-image">
        </div>
        <!-- Login Form Column -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="login-container <?= $loginError ? 'shake' : '' ?>">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Login</h3>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleDarkMode()">🌓</button>
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <span class="toggle-password" onclick="togglePassword()">👁️</span>
                    </div>
                    <?php if ($loginError): ?>
                        <div class="alert alert-danger py-1 text-center">Invalid username or password.</div>
                    <?php endif; ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    <a href="signup.php" class="text-decoration-none text-dark">Don't have an account? <strong>Sign Up</strong></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}
</script>
</body>
</html>
