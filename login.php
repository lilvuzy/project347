<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once('database_connection.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT * FROM user_details WHERE user_email='$email'";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_fname'] = $user['user_fname'];
            $_SESSION['user_lname'] = $user['user_lname'];
            header('Location: index.php');
            exit();
        }
    }
    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form method="POST" action="">
        <label>Email</label>
        <input type="text" name="email">
        <br>
        <label>Password</label>
        <input type="password" name="password">
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
