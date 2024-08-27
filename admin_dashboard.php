<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <div class="list-group">
            <a href="manage_investments.php" class="list-group-item">Manage Investments</a>
            <a href="manage_notifications.php" class="list-group-item">Manage Notifications</a>
            <a href="admin_manage_transactions.php" class="list-group-item">Manage Transactions</a>
            <a href="admin_logout.php" class="list-group-item text-danger">Logout</a>
        </div>
    </div>
</body>
</html>
