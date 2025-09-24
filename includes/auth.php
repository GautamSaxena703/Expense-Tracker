<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect user to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
