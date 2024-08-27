<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('db_connection.php');

// Fetch all investments
$query = "SELECT * FROM transactions WHERE type='investment'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Investments</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Investments</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($investment = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $investment['user_id']; ?></td>
                    <td>$<?php echo number_format($investment['amount'], 2); ?></td>
                    <td><?php echo $investment['date']; ?></td>
                    <td><?php echo ucfirst($investment['status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
