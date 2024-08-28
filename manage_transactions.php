<?php
// Include the database connection file
include('db_connection.php');

// Initialize variables
$isEdit = false;
$transaction_id = '';
$user_id = '';
$type = '';
$amount = '';
$date = '';
$status = '';

// Check if the form was submitted for editing
if (isset($_POST['update_transaction'])) {
    $transaction_id = $_POST['transaction_id'];
    $user_id = $_POST['user_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    
    // Prepare the SQL query for updating
    $sql = "UPDATE transactions 
            SET user_id = ?, type = ?, amount = ?, date = ?, status = ? 
            WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isdsis", $user_id, $type, $amount, $date, $status, $transaction_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Transaction updated successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating transaction: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing query: " . $conn->error . "</div>";
    }
}

// Check if the form was submitted for adding a new transaction
if (isset($_POST['add_transaction'])) {
    $user_id = $_POST['user_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Prepare the SQL query for inserting
    $sql = "INSERT INTO transactions (user_id, type, amount, date, status) 
            VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isdss", $user_id, $type, $amount, $date, $status);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Transaction added successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding transaction: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing query: " . $conn->error . "</div>";
    }
}

// Check if transaction ID is provided for editing
if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];
    $isEdit = true;

    // Fetch the transaction details for editing
    $sql = "SELECT * FROM transactions WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $transaction = $result->fetch_assoc();
            $user_id = $transaction['user_id'];
            $type = $transaction['type'];
            $amount = $transaction['amount'];
            $date = $transaction['date'];
            $status = $transaction['status'];
        } else {
            echo "<div class='alert alert-warning'>Transaction not found.</div>";
            exit();
        }
        
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing query: " . $conn->error . "</div>";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Transaction</title>
    <link rel="stylesheet" href="../bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">b
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3><?php echo $isEdit ? 'Edit' : 'Add'; ?> Transaction</h3>
        </div>
        <div class="card-body">
            <form method="post" action="manage_transactions.php">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="transaction_id" value="<?php echo $transaction_id; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="number" class="form-control" name="user_id" value="<?php echo $user_id; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Transaction Type</label>
                    <select class="form-select" name="type" required>
                        <option value="deposit" <?php if ($type == 'deposit') echo 'selected'; ?>>Deposit</option>
                        <option value="withdrawal" <?php if ($type == 'withdrawal') echo 'selected'; ?>>Withdrawal</option>
                        <option value="investment" <?php if ($type == 'investment') echo 'selected'; ?>>Purchase</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $amount; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" value="<?php echo $date; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="pending" <?php if ($status == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="completed" <?php if ($status == 'completed') echo 'selected'; ?>>Completed</option>
                        <option value="failed" <?php if ($status == 'failed') echo 'selected'; ?>>Failed</option>
                    </select>
                </div>

                <button type="submit" name="<?php echo $isEdit ? 'update_transaction' : 'add_transaction'; ?>" class="btn btn-primary">
                    <?php echo $isEdit ? 'Update' : 'Add'; ?> Transaction
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
