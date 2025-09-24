<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php'; // checks login

$user_id = $_SESSION['user_id'];

// Fetch total expenses
$sql = "SELECT SUM(amount) AS total FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalResult = $stmt->get_result()->fetch_assoc();
$totalExpenses = $totalResult['total'] ?? 0;

// Fetch expenses by category
$sql = "SELECT c.name AS category, SUM(e.amount) AS total 
        FROM expenses e 
        JOIN categories c ON e.category_id = c.id 
        WHERE e.user_id = ? 
        GROUP BY c.name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categoryResult = $stmt->get_result();

// Fetch recent 5 expenses
$sql = "SELECT e.amount, e.description, e.expense_date, c.name AS category 
        FROM expenses e 
        JOIN categories c ON e.category_id = c.id 
        WHERE e.user_id = ? 
        ORDER BY e.expense_date DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recentExpenses = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Expense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h2>
    <h3>Total Expenses: ₹<?php echo number_format($totalExpenses, 2); ?></h3>

    <div class="summary">
        <h3>Expenses by Category</h3>
        <ul>
            <?php while ($row = $categoryResult->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($row['category']); ?>: ₹<?php echo number_format($row['total'], 2); ?></li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="recent">
        <h3>Recent Expenses</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount (₹)</th>
            </tr>
            <?php while ($row = $recentExpenses->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['expense_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo number_format($row['amount'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="actions">
        <a href="add_expense.php">➕ Add Expense</a> | 
        <a href="view_expenses.php">📊 View All</a> | 
        <a href="../public/logout.php">🚪 Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
