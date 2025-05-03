<?php
session_start();
require 'config.php';

// If already logged in, redirect based on role
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "❌ Username not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card mx-auto p-4" style="max-width: 400px;">
    <h2 class="text-center mb-4">🔑 Login</h2>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <input name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input name="password" type="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</div>
</body>
</html>
