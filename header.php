<?php
// header.php - Shared site header for all CrypTeach pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header style="
    background: linear-gradient(to right, #fbbf24, #fcd34d);
    padding: 18px 40px;
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    font-family: 'Segoe UI', sans-serif;
">
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="user_home.php">
            <img src="uploads/crypteach_logo.png" alt="CrypTeach Logo"
                 style="height: 60px; width: auto; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
        </a>
        <div>
            <div style="font-size: 22px; font-weight: 600; color: white;">
                CrypTeach Learning Portal
            </div>
            <div style="font-size: 14px; color: #fffbe8;">Empowering Secure Learning</div>
        </div>
    </div>
    <div>
        <?php if (isset($_SESSION['username'])): ?>
            <span style="margin-right: 15px; color: #5c441c; font-weight: 500;">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php endif; ?>
        <a href="logout.php" style="
            background-color: white;
            color: #333;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
        " onmouseover="this.style.backgroundColor='#f1f1f1'; this.style.transform='scale(1.05)'"
           onmouseout="this.style.backgroundColor='white'; this.style.transform='scale(1)'">
            Logout
        </a>
    </div>
</header>
