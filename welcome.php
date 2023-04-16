<?php
session_start();
require_once 'database_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$query = "SELECT * FROM user_details WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h2>Welcome, <?php echo $user['user_fname'] . ' ' . $user['user_lname']; ?>!</h2>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
