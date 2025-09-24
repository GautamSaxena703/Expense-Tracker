<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php'; // check login

$user_id = $_SESSION['user_id'];

// Fetch all expenses of logged-in user
$sql = "SELECT e.id, e.amount, e.description, e.expense_date, c.name AS category 
        FROM expenses e
        JOIN categories c ON e.category_id = c.id
        WHERE e.user_id = ?
        ORDER BY e.expense_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total expenses
$total_sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
$stmt2 = $conn->prepare($total_sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$total_result = $stmt2->get_result()->fetch_assoc();
$total_spent = $total_result['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expenses - Expense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>📜 Your Expenses</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0" class="expense-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['expense_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td>
                            <a href="edit_expense.php?id=<?php echo $row['id']; ?>">✏ Edit</a> | 
                            <a href="delete_expense.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this expense?');">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Total Spent: ₹<?php echo number_format($total_spent, 2); ?></h3>

    <?php else: ?>
        <p>No expenses recorded yet. <a href="add_expense.php">Add one</a>!</p>
    <?php endif; ?>

    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
