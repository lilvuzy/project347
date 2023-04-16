<?php
session_start();
require_once 'database_connection.php';

if (isset($_SESSION['user_id'])) {
    header("Location: welcome.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
        <label>Email:</label>
        <input type="email" name="user_email" required>
        <br>
        <label>Password:</label>
        <input type="password" name="user_password" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>