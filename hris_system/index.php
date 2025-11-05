<?php
session_start();
include('includes/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Optional: Redirect based on role
            if ($user['role'] === 'Admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard.php"); // HR staff also go here for now
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HRIS Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>HRIS Login</h2>

        <form method="POST" action="">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
