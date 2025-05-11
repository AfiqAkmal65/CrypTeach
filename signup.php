<?php
session_start();
include 'db.php';
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

// CAPTCHA logic
if (isset($_GET['refresh_captcha'])) {
    $operators = ['+', '-', '*'];
    $op = $operators[array_rand($operators)];
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);

    switch ($op) {
        case '+': $_SESSION['captcha_answer'] = $num1 + $num2; break;
        case '-': $_SESSION['captcha_answer'] = $num1 - $num2; break;
        case '*': $_SESSION['captcha_answer'] = $num1 * $num2; break;
    }

    $_SESSION['captcha_question'] = "$num1 $op $num2";
    header("Location: signup.php");
    exit();
}

if (!isset($_SESSION['captcha_question'])) {
    $operators = ['+', '-', '*'];
    $op = $operators[array_rand($operators)];
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);

    switch ($op) {
        case '+': $_SESSION['captcha_answer'] = $num1 + $num2; break;
        case '-': $_SESSION['captcha_answer'] = $num1 - $num2; break;
        case '*': $_SESSION['captcha_answer'] = $num1 * $num2; break;
    }

    $_SESSION['captcha_question'] = "$num1 $op $num2";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password_raw = $_POST['password'];
    $captcha_input = intval($_POST['captcha']);
    $role     = 'student';

    $strength = 0;
    if (strlen($password_raw) >= 6) $strength++;
    if (preg_match('/[A-Z]/', $password_raw)) $strength++;
    if (preg_match('/[0-9]/', $password_raw)) $strength++;
    if (preg_match('/[^A-Za-z0-9]/', $password_raw)) $strength++;

    if ($strength < 4) {
        $message = "Password not strong enough. Use uppercase, number, symbol, and at least 6 characters.";
    } elseif ($captcha_input !== ($_SESSION['captcha_answer'] ?? null)) {
        $message = "CAPTCHA failed. Please solve the math correctly.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'afiqcrypteach@gmail.com';       // change this
                $mail->Password   = 'ohyy ncyx hyca eoqf';      // change this
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('afiqcrypteach@gmail.com', 'CrypTeach');
                $mail->addAddress($email, $username);
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to CrypTeach!';
                $mail->Body = "
<h3>Hello $username!</h3>
<p>Welcome to <strong>CrypTeach</strong> — your gateway to mastering the world of cryptography. 🔐</p>
<p>We're excited to have you onboard! Here's what you can do next:</p>
<ul>
    <li>💡 Start with <strong>Chapter 1</strong> to learn the basics of cryptography.</li>
    <li>🎮 Try our <strong>interactive games</strong> to test your encryption and decryption skills.</li>
    <li>📚 Browse our <strong>digital cryptography book</strong> to deepen your knowledge.</li>
    <li>🧠 Chat with our <strong>AI assistant</strong> if you have any questions or need help!</li>
</ul>
<p>You can log in anytime at: <a href='http://localhost/CrypTeach/login.php'>CrypTeach Login Page</a></p>
<p>Let’s unlock the secrets of cryptography together!</p><br><p>— The CrypTeach Team</p>
";
                $mail->send();
                $_SESSION['message'] = 'Account created successfully! Please check your email.';
            } catch (Exception $e) {
                $_SESSION['message'] = 'Account created, but email could not be sent. Error: ' . $mail->ErrorInfo;
            }

            header("Location: login.php");
            exit();
        } else {
            $message = "Signup failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - CrypTeach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #0d0d0d, #1a1a1a, #2a2a2a);

        }
        .login-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            max-width: 960px;
            width: 100%;
        }
        .split-left, .split-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-image {
            max-width: 400px;
            width: 100%;
            height: auto;
        }
        .signup-container {
            background-color: #fffaf0;
            border: 2px solid #c9a53f;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(90, 42, 12, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .btn-custom {
            background-color: #c9a53f;
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-custom:hover {
            background-color: #b8902f;
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
        .strength-meter {
            height: 6px;
            background: #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease-in-out;
        }
        .strength-weak { background-color: red; width: 25%; }
        .strength-fair { background-color: orange; width: 50%; }
        .strength-good { background-color: yellowgreen; width: 75%; }
        .strength-strong { background-color: limegreen; width: 100%; }
    </style>
</head>
<body>
<div class="login-section">
    <div class="login-wrapper">
        <div class="split-left">
            <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo" class="logo-image">
        </div>
        <div class="split-right">
            <div class="signup-container">
                <h3 class="text-center mb-4">📝 Create Your Account</h3>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-warning text-center"><?= $message ?></div>
                <?php endif; ?>
                <form method="POST" id="signupForm" novalidate>
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                        <div class="invalid-feedback">Please enter a username.</div>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                        <span class="toggle-password" onclick="togglePassword()">👁️</span>
                        <div class="strength-meter" id="strengthMeter"><div class="strength-meter-fill"></div></div>
                        <div class="invalid-feedback">Please enter a strong password.</div>
                    </div>
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div style="flex: 1;">
                            <label for="captcha" class="form-label">
                                🤖 I'm not a robot: What is <?= htmlspecialchars($_SESSION['captcha_question'] ?? '...') ?>?
                            </label>
                            <input type="text" class="form-control" id="captcha" name="captcha" required>
                            <div class="invalid-feedback">Please solve the question to proceed.</div>
                        </div>
                        <a href="?refresh_captcha=1" class="btn btn-sm btn-warning ms-3">🔄</a>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Sign Up</button>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none text-dark">Already have an account? <strong>Login</strong></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passField = document.getElementById("password");
    passField.type = passField.type === "password" ? "text" : "password";
}
const password = document.getElementById("password");
const meter = document.getElementById("strengthMeter");
const meterFill = meter.querySelector(".strength-meter-fill");
password.addEventListener("input", function () {
    const val = password.value;
    let strength = 0;
    if (val.length >= 6) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    meterFill.className = "strength-meter-fill";
    if (strength === 1) meterFill.classList.add("strength-weak");
    if (strength === 2) meterFill.classList.add("strength-fair");
    if (strength === 3) meterFill.classList.add("strength-good");
    if (strength >= 4) meterFill.classList.add("strength-strong");
});
(() => {
    const form = document.getElementById("signupForm");
    const passwordInput = document.getElementById("password");
    form.addEventListener('submit', event => {
        const val = passwordInput.value;
        let strength = 0;
        if (val.length >= 6) strength++;
        if (/[A-Z]/.test(val)) strength++;
        if (/[0-9]/.test(val)) strength++;
        if (/[^A-Za-z0-9]/.test(val)) strength++;
        if (!form.checkValidity() || strength < 4) {
            event.preventDefault();
            event.stopPropagation();
            alert("Please enter a strong password: min 6 characters, with uppercase, number, and symbol.");
            return;
        }
        form.classList.add('was-validated');
    }, false);
})();
</script>
</body>
</html>
