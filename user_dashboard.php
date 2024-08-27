<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

include('../db_connection.php');

$user_id = $_SESSION['user_id'];

// Fetch user information
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Fetch user transactions
$transactions_query = "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY date DESC";
$transactions_result = $conn->query($transactions_query);

// Fetch user notifications
$notifications_query = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY date_sent DESC";
$notifications_result = $conn->query($notifications_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p>Current Balance: $<?php echo number_format($user['current_balance'], 2); ?></p>

        <div class="btn-group btn-group-toggle mt-4" data-toggle="buttons">
            <label class="btn btn-primary active" id="btn-transactions">
                <input type="radio" name="options" autocomplete="off" checked> View Transactions
            </label>
            <label class="btn btn-primary" id="btn-investments">
                <input type="radio" name="options" autocomplete="off"> View Investments
            </label>
            <label class="btn btn-primary" id="btn-notifications">
                <input type="radio" name="options" autocomplete="off"> View Notifications
            </label>
        </div>

        <div class="mt-4">
            <div id="transactions-section" class="section-content">
                <h3>Transactions</h3>
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

            <div id="investments-section" class="section-content" style="display: none;">
                <h3>Investments</h3>
                <!-- You can add specific logic for investments here, if needed -->
                <p>All investments are listed under transactions.</p>
            </div>

            <div id="notifications-section" class="section-content" style="display: none;">
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
        </div>

        <div class="mt-4">
            <a href="user_logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <script>
        // Script to handle tab switching
        document.getElementById('btn-transactions').addEventListener('click', function() {
            document.getElementById('transactions-section').style.display = 'block';
            document.getElementById('investments-section').style.display = 'none';
            document.getElementById('notifications-section').style.display = 'none';
        });

        document.getElementById('btn-investments').addEventListener('click', function() {
            document.getElementById('transactions-section').style.display = 'none';
            document.getElementById('investments-section').style.display = 'block';
            document.getElementById('notifications-section').style.display = 'none';
        });

        document.getElementById('btn-notifications').addEventListener('click', function() {
            document.getElementById('transactions-section').style.display = 'none';
            document.getElementById('investments-section').style.display = 'none';
            document.getElementById('notifications-section').style.display = 'block';
        });
    </script>
</body>
</html>
