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

// Delete the transaction
$delete_query = "DELETE FROM transactions WHERE id = $transaction_id";

if ($conn->query($delete_query) === TRUE) {
    echo "Transaction deleted successfully.";
    header('Location: manage_transactions.php');
    exit;
} else {
    echo "Error deleting transaction: " . $conn->error;
}
?>
