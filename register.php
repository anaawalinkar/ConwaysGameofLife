<?php
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $message = "✅ Registration successful. <a href='login.php'>Login here</a>.";
    } else {
        $message = "❌ Username already taken.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card mx-auto p-4" style="max-width: 400px;">
    <h2 class="text-center mb-4">📝 Register</h2>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <input name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input name="password" type="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Register</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
  </div>
</div>
</body>
</html>
