<?php
session_start();
include('db_connection.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Get the transaction ID from the URL
$transaction_id = $_GET['id'];

// Fetch the transaction details
$query = "SELECT * FROM transactions WHERE id = $transaction_id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $transaction = $result->fetch_assoc();
} else {
    echo "Transaction not found.";
    exit;
}

// Update transaction details when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $update_query = "UPDATE transactions SET amount = '$amount', status = '$status', date = '$date' WHERE id = $transaction_id";
    
    if ($conn->query($update_query) === TRUE) {
        echo "Transaction updated successfully.";
        header('Location: manage_transactions.php');
        exit;
    } else {
        echo "Error updating transaction: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaction</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Transaction</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" class="form-control" id="amount" name="amount" value="<?php echo $transaction['amount']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?php echo ($transaction['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="completed" <?php echo ($transaction['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="failed" <?php echo ($transaction['status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo $transaction['date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Transaction</button>
    </form>
</div>
</body>
</html>
