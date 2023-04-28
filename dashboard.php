<?php
session_start();
require_once "config.php";

function fetchUserProductCount($pdo, $user_id) {
    $sql = "SELECT COUNT(id) as product_count FROM products WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

function fetchUserForecastCount($pdo, $user_id) {
    $sql = "SELECT COUNT(id) as forecast_count FROM products WHERE user_id = ? AND forecast IS NOT NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

$product_count = fetchUserProductCount($pdo, $_SESSION["id"]);
$forecast_count = fetchUserForecastCount($pdo, $_SESSION["id"]);

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sales Forecaster</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "navbar.php"; ?>
    <main class="content">
    <div class="container mt-4">
        <h1>Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Products Added</h5>
                        <p class="card-text"><?php echo $product_count; ?></p>
                        <a href="products.php" class="btn btn-primary">View Products</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Forecasts Created</h5>
                        <p class="card-text"><?php echo $forecast_count; ?></p>
                        <a href="forecasts.php" class="btn btn-primary">View Forecasts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </main>
    <?php include_once "footer.php"; ?>
</body>

</html>
