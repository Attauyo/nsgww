<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Track Ticket</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Make it responsive -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .track-form {
      max-width: 500px;
      margin: auto;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2 class="text-center mb-4">🎟️ Track Your Ticket</h2>
  <div class="track-form">
    <form action="view-ticket.php" method="GET" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label for="code" class="form-label">Enter Ticket Code:</label>
        <input type="text" class="form-control" id="code" name="code" placeholder="e.g. LEE123" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">View Ticket</button>
    </form>
  </div>
</div>
</body>
</html>
