<?php
session_start();
require 'config.php';

// Allow only admins
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle delete request (delete user + their game sessions)
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM game_sessions WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all users with games played count
$users = $conn->query("
  SELECT u.id, u.username, u.role, u.created_at, COUNT(gs.id) AS games_played
  FROM users u
  LEFT JOIN game_sessions gs ON u.id = gs.user_id
  GROUP BY u.id
  ORDER BY u.created_at DESC
");

// Fetch all game sessions with usernames
$sessions = $conn->query("SELECT gs.id, u.username, gs.start_time, gs.end_time, gs.generations FROM game_sessions gs JOIN users u ON gs.user_id = u.id ORDER BY gs.id DESC");

// Fetch top 5 users by games played
$topUsers = $conn->query("SELECT u.username, COUNT(gs.id) as games_played FROM game_sessions gs JOIN users u ON gs.user_id = u.id GROUP BY u.id ORDER BY games_played DESC LIMIT 5");

// Fetch total generation stats
$genStats = $conn->query("SELECT MIN(generations) as min_gen, MAX(generations) as max_gen, AVG(generations) as avg_gen FROM game_sessions");
$genData = $genStats->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 mb-4">
    <h2 class="text-center">🛠 Admin Dashboard</h2>
    <p>Welcome, <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong></p>
    <a href="dashboard.php" class="btn btn-secondary">← Back to User Dashboard</a>
  </div>

  <!-- 📊 Analytics -->
  <div class="card p-4 mb-5">
    <h4>📈 Analytics</h4>
    <div class="row">
      <div class="col-md-6">
        <canvas id="barChart"></canvas>
      </div>
      <div class="col-md-6">
        <h5>🧮 Generation Stats</h5>
        <ul class="list-group">
          <li class="list-group-item">Minimum Generations: <strong><?= $genData['min_gen'] ?? 0 ?></strong></li>
          <li class="list-group-item">Maximum Generations: <strong><?= $genData['max_gen'] ?? 0 ?></strong></li>
          <li class="list-group-item">Average Generations: <strong><?= round($genData['avg_gen'] ?? 0, 2) ?></strong></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- 👥 Users -->
  <div class="card p-4 mb-5">
    <h4>👥 All Users</h4>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Created At</th><th>Games Played</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['games_played'] ?></td>
            <td>
              <?php if ($row['id'] != $_SESSION['user_id']): ?>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user and sessions?');">Delete</a>
              <?php else: ?><span class="text-muted">N/A</span><?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- 🕹 Game Sessions -->
  <div class="card p-4">
    <h4>🕹 All Game Sessions</h4>
    <table class="table table-bordered table-striped mt-3">
      <thead class="table-dark">
        <tr><th>ID</th><th>Username</th><th>Start</th><th>End</th><th>Generations</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $sessions->fetch_assoc()): ?>
          <?php
            $start = new DateTime($row['start_time'], new DateTimeZone('UTC'));
            $end = new DateTime($row['end_time'], new DateTimeZone('UTC'));
            $start->setTimezone(new DateTimeZone('America/New_York'));
            $end->setTimezone(new DateTimeZone('America/New_York'));
          ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $start->format('Y-m-d H:i:s') ?></td>
            <td><?= $end->format('Y-m-d H:i:s') ?></td>
            <td><?= $row['generations'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Chart.js Bar Chart Script -->
<script>
  const ctx = document.getElementById('barChart').getContext('2d');
  const barChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [
        <?php mysqli_data_seek($topUsers, 0); while ($row = $topUsers->fetch_assoc()) echo "'{$row['username']}',"; ?>
      ],
      datasets: [{
        label: 'Games Played',
        data: [
          <?php mysqli_data_seek($topUsers, 0); while ($row = $topUsers->fetch_assoc()) echo "{$row['games_played']},"; ?>
        ],
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>
</body>
</html>
