<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Welcome to Game of Life</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; text-align: center; padding-top: 80px; }
    .btn { margin: 10px; }
  </style>
</head>
<body>

  <div class="container">
    <h1 class="mb-4">🧬 Conway's Game of Life</h1>

    <?php if (isset($_SESSION["user_id"])): ?>
      <p class="lead">Welcome back, <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong>!</p>
      <div>
        <a href="dashboard.php" class="btn btn-primary">📂 Dashboard</a>
        <a href="game.html" class="btn btn-success">🎮 Play Game</a>
        <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
      </div>
    <?php else: ?>
      <p class="lead">Please login or register to begin.</p>
      <div>
        <a href="login.php" class="btn btn-primary">🔑 Login</a>
        <a href="register.php" class="btn btn-secondary">📝 Register</a>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
