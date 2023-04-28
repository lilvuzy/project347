<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];

    if (isset($_POST["add_product"])) {
        $product_name = trim($_POST["product_name"]);

        $sql = "INSERT INTO products (user_id, product_name) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $product_name]);
        header("location: products.php");
    } elseif (isset($_POST["delete_product"])) {
        $product_id = intval($_POST["product_id"]);

        // Delete sales data from sales_data table
        $sql = "DELETE FROM sales_data WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);

        // Delete product from products table
        $sql = "DELETE FROM products WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $user_id]);
        header("location: products.php");
    }
}

function fetchUserProducts($pdo, $user_id) {
    $sql = "SELECT id, product_name FROM products WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

$products = fetchUserProducts($pdo, $_SESSION["id"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Sales Forecaster</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "navbar.php"; ?>
    <div class="container mt-4">
        <h1>Products</h1>
        <form action="products.php" method="post">
            <div class="form-group">
                <label for="product_name">Add Product:</label>
                <input type="text" name="product_name" id="product_name" class="form-control" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>
        <h2 class="mt-4">Your Products:</h2>
        <ul class="list-group">
    <?php foreach ($products as $product): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($product["product_name"]) ?>
            <form action="products.php" method="post" class="mb-0">
                <input type="hidden" name="product_id" value="<?= $product["id"] ?>">
                <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
    </div>
    <?php include_once "footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
