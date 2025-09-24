<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php'; // check login

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $date = $_POST['expense_date'];

    if (!empty($amount) && !empty($category_id) && !empty($date)) {
        $sql = "INSERT INTO expenses (user_id, category_id, amount, description, expense_date) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $user_id, $category_id, $amount, $description, $date);
        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=ExpenseAdded");
            exit;
        } else {
            $message = "❌ Failed to add expense. Try again.";
        }
    } else {
        $message = "⚠️ Please fill all required fields.";
    }
}

// Fetch categories for dropdown
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense - Expense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Add New Expense</h2>
    
    <?php if (!empty($message)) echo "<p class='alert'>$message</p>"; ?>

    <form method="POST" action="">
        <label for="amount">Amount (₹):</label>
        <input type="number" step="0.01" name="amount" required>

        <label for="category">Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="description">Description:</label>
        <input type="text" name="description" placeholder="Optional">

        <label for="expense_date">Date:</label>
        <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>

        <button type="submit">➕ Add Expense</button>
    </form>

    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
