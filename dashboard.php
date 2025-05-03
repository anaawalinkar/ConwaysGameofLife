<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require_once('config.php');

// Connect to the database
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Fetch sessions only for the logged-in user
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT id, start_time, end_time, generations FROM game_sessions WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sessions = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card mx-auto p-4 text-center" style="max-width: 600px;">
    <h2 class="mb-3">🎮 Welcome to the Game Dashboard</h2>
    <p class="lead">Hello, <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong></p>
    <p>Role: <span class="badge bg-secondary"><?= htmlspecialchars($_SESSION["role"]) ?></span></p>

    <div class="d-grid gap-3 mt-4">
      <a href="game.html" class="btn btn-success btn-lg">Play Game of Life</a>
  
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin_dashboard.php" class="btn btn-warning btn-lg">👑 Admin Dashboard</a>
      <?php endif; ?>
  
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

  </div>

  <!-- 🕹 Game Session Logs -->
  <div class="mt-5">
    <h3>📊 Session History</h3>
    
    <table class="table table-bordered table-striped mt-3">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Generations</th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($row = $sessions->fetch_assoc()):
          $start = new DateTime($row['start_time'], new DateTimeZone('UTC'));
          $start->setTimezone(new DateTimeZone('America/New_York'));
          $end = new DateTime($row['end_time'], new DateTimeZone('UTC'));
          $end->setTimezone(new DateTimeZone('America/New_York'));
        ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $start->format('Y-m-d H:i:s') ?></td>
            <td><?= $end->format('Y-m-d H:i:s') ?></td>
            <td><?= $row['generations'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
