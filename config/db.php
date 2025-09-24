<?php
// db.php

$host = "localhost";      // Database host (keep localhost for XAMPP/MAMP)
$user = "root";           // Default MySQL user for XAMPP/MAMP
$pass = "";               // Default password is empty in XAMPP
$dbname = "expense_tracker";  // Database name (create this in phpMyAdmin)

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
