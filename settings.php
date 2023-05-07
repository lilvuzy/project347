<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an admin
$isAdmin = false;
$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["id"]]);
$is_admin = $stmt->fetchColumn();

if ($is_admin == 1) {
    $isAdmin = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_account"])) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["id"]]);

        // Remove user's products
        $sql = "DELETE FROM products WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["id"]]);

        header("location: logout.php");
    } elseif (isset($_POST["delete_user"]) && $isAdmin) {
        $user_id = $_POST["user_id"];
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

        // Remove the user's products
        $sql = "DELETE FROM products WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    }
}

// Fetch all users if the current user is an admin
$allUsers = [];
if ($isAdmin) {
    $sql = "SELECT id, username, email FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $allUsers = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Sales Forecaster</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .navbar {
            background-color: #1d3557;
        }
        .nav-link {
            color: #f1faee;
        }
        .nav-link:hover {
            color: #e63946;
        }
        .user-tab {
            color: #f1faee;
            cursor: pointer;
        }
        .user-tab:hover {
            color: #e63946;
        }
    </style>
</head>
<body>
    <?php include_once "navbar.php"; ?>
    <div class="container mt-4">
        <h1>Settings</h1>
        <form action="settings.php" method="post">
            <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
        </form>
        <?php if ($isAdmin): ?>
        <hr>
        <h2>All Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user["id"]); ?></td>
                    <td><?php echo htmlspecialchars($user["username"]); ?></td>
                    <td><?php echo htmlspecialchars($user["email"]); ?></td>
                    <td>
                        <form action="settings.php" method="post">
                            <input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php include_once "footer.php"; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>