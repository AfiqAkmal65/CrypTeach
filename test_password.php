<?php
$passwordInput = 'admin123'; // the password you're typing
$hashFromDB = '$2y$10$D9LqU3B5D3S1bGE6c4skKOj0VYPZ0WMWl2kJo81N1i7nzkBP/qYiq';

if (password_verify($passwordInput, $hashFromDB)) {
    echo "✅ Password is correct!";
} else {
    echo "❌ Password is invalid!";
}
