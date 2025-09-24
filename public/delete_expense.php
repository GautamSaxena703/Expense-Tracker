<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php'; // check login

$user_id = $_SESSION['user_id'];

// Check if expense ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_expenses.php");
    exit();
}

$expense_id = intval($_GET['id']);

// Delete only if this expense belongs to the logged-in user
$sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expense_id, $user_id);

if ($stmt->execute()) {
    header("Location: view_expenses.php?msg=deleted");
    exit();
} else {
    echo "Error deleting expense. Please try again.";
}
?>
