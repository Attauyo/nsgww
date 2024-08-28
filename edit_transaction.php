<?php
// Include the database connection file
include('db_connection.php');

// Check if the form was submitted
if (isset($_POST['update_transaction'])) {
    $transaction_id = $_POST['transaction_id'];
    $user_id = $_POST['user_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    
    // Prepare the SQL query
    $sql = "UPDATE transactions 
            SET user_id = ?, type = ?, amount = ?, date = ?, status = ? 
            WHERE id = ?";
    
    // Initialize a prepared statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("isdsis", $user_id, $type, $amount, $date, $status, $transaction_id);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            echo "Transaction updated successfully.";
        } else {
            echo "Error updating transaction: " . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
}

// Check if transaction ID is provided
if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];
    
    // Fetch the transaction details
    $sql = "SELECT * FROM transactions WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $transaction = $result->fetch_assoc();
        } else {
            echo "Transaction not found.";
            exit();
        }
        
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "No transaction ID provided.";
    exit();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction</title>
    <link rel="stylesheet" href="bootstrap.min.css"> <!-- Ensure Bootstrap is linked -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Transaction</h2>
    <form method="post" action="edit_transaction.php">
        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
        
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID</label>
            <input type="number" class="form-control" name="user_id" value="<?php echo $transaction['user_id']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Transaction Type</label>
            <select class="form-control" name="type" required>
                <option value="deposit" <?php if ($transaction['type'] == 'deposit') echo 'selected'; ?>>Deposit</option>
                <option value="withdrawal" <?php if ($transaction['type'] == 'withdrawal') echo 'selected'; ?>>Withdrawal</option>
                <option value="purchase" <?php if ($transaction['type'] == 'investment') echo 'selected'; ?>>investment</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" class="form-control" name="amount" value="<?php echo $transaction['amount']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="<?php echo $transaction['date']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="pending" <?php if ($transaction['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="completed" <?php if ($transaction['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                <option value="failed" <?php if ($transaction['status'] == 'failed') echo 'selected'; ?>>Failed</option>
            </select>
        </div>
        
        <button type="submit" name="update_transaction" class="btn btn-primary">Update Transaction</button>
    </form>
</div>

</body>
</html>
