<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}
include('db_connection.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id='$user_id'";
$user_result = $conn->query($query);
$user = $user_result->fetch_assoc();

// Fetch Transactions and Notifications
$transactions_query = "SELECT * FROM transactions WHERE user_id='$user_id'";
$transactions_result = $conn->query($transactions_query);

$notifications_query = "SELECT * FROM notifications WHERE user_id='$user_id'";
$notifications_result = $conn->query($notifications_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <div class="card">
            <div class="card-header">
                Account Overview
            </div>
            <div class="card-body">
                <h5 class="card-title">Current Balance: $<?php echo number_format($user['current_balance'], 2); ?></h5>
            </div>
        </div>

        <div class="mt-4">
            <h3>Transaction History</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaction = $transactions_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo ucfirst($transaction['type']); ?></td>
                        <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo $transaction['date']; ?></td>
                        <td><?php echo ucfirst($transaction['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Notifications</h3>
            <ul class="list-group">
                <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($notification['message']); ?>
                    <span class="badge badge-primary float-right"><?php echo $notification['date_sent']; ?></span>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="mt-4">
            <a href="user_logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
