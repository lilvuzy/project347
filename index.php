<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
echo "Welcome " . $_SESSION['user_fname'] . " " . $_SESSION['user_lname'];
?>
<a href="logout.php">Logout</a>
