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

// Fetch expense details
$sql = "SELECT * FROM expenses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Expense not found or unauthorized access.";
    exit();
}

$expense = $result->fetch_assoc();

// Fetch categories
$cat_sql = "SELECT * FROM categories";
$categories = $conn->query($cat_sql);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $expense_date = $_POST['expense_date'];

    $update_sql = "UPDATE expenses 
                   SET amount = ?, description = ?, category_id = ?, expense_date = ? 
                   WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("dsisii", $amount, $description, $category_id, $expense_date, $expense_id, $user_id);

    if ($stmt->execute()) {
        header("Location: view_expenses.php?msg=updated");
        exit();
    } else {
        $error = "Error updating expense. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Expense - Expense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>✏ Edit Expense</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label for="amount">Amount (₹):</label><br>
        <input type="number" step="0.01" name="amount" id="amount" value="<?php echo htmlspecialchars($expense['amount']); ?>" required><br><br>

        <label for="description">Description:</label><br>
        <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($expense['description']); ?>" required><br><br>

        <label for="category_id">Category:</label><br>
        <select name="category_id" id="category_id" required>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" 
                    <?php if ($cat['id'] == $expense['category_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="expense_date">Date:</label><br>
        <input type="date" name="expense_date" id="expense_date" value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required><br><br>

        <button type="submit">💾 Save Changes</button>
    </form>

    <p><a href="view_expenses.php">⬅ Back to Expenses</a></p>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
