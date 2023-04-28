<?php
// Start a session
session_start();
 // Include the config file
require_once "config.php";
 // Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
 // Function to calculate the forecast based on sales data
function createForecast($sales_data) {
    return array_sum($sales_data) / count($sales_data);
}
 // Function to get the previous 12 months
function getPreviousMonths($n) {
    $months = [];
    $currentMonth = new DateTime('first day of this month');
    for ($i = 0; $i < $n; $i++) {
        $currentMonth->modify('-1 month');
        $months[] = $currentMonth->format('F Y');
    }
    return array_reverse($months);
}
 // Get all products from the database
$sql = "SELECT id, product_name FROM products WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["id"]]);
$products = $stmt->fetchAll();
 // If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If the create forecast button is clicked
    if (isset($_POST["create_forecast"])) {
        // Get the product ID and sales data
        $product_id = intval($_POST["product_id"]);
        $sales_data = array_map('intval', $_POST["sales_data"]);
         // Calculate the forecast
        $forecast = createForecast($sales_data);
         // Update the forecast in the database
        $sql = "UPDATE products SET forecast = ? WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$forecast, $product_id, $_SESSION["id"]]);
         // Get the previous 12 months
        $previousMonths = getPreviousMonths(12);
         // Insert the sales data into the database
        $sql = "INSERT INTO sales_data (product_id, month, year, units_sold) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        for ($i = 0; $i < 12; $i++) {
            $monthYear = DateTime::createFromFormat('F Y', $previousMonths[$i]);
            $month = (int)$monthYear->format('m');
            $year = (int)$monthYear->format('Y');
            $stmt->execute([$product_id, $month, $year, $sales_data[$i]]);
        }
         // Redirect to the forecasts page
        header("location: forecasts.php");
    } elseif (isset($_POST["delete_forecast"])) { // If the delete forecast button is clicked
        // Get the product ID
        $product_id = intval($_POST["product_id"]);
         // Set the forecast to null in the database
        $sql = "UPDATE products SET forecast = NULL WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $_SESSION["id"]]);
         // Delete the sales data from the database
        $sql = "DELETE FROM sales_data WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
         // Redirect to the forecasts page
        header("location: forecasts.php");
    }
}
 // Get all products and forecasts from the database
$sql = "SELECT id, product_name, forecast FROM products WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["id"]]);
$products_forecasts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forecasts - Sales Forecaster</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "navbar.php"; ?>
    <div class="container mt-4">
        <h1>Forecasts</h1>
        <form action="forecasts.php" method="post">
            <div class="form-group">
                <label for="product_id">Select Product:</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product["id"] ?>"><?= htmlspecialchars($product["product_name"]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Enter Previous 12 Months of Sales Data (units sold per month):</label>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <input type="number" name="sales_data[]" class="form-control" required>
                <?php endfor; ?>
            </div>
            <button type="submit" name="create_forecast" class="btn btn-primary">Create Forecast</button>
        </form>
    </div>
    <div class="container mt-4">
        <h1>Your Forecasts</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Forecast</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products_forecasts as $product_forecast) : ?>
                    <?php if ($product_forecast["forecast"] !== null) : ?>
                        <tr>
                            <td><?= htmlspecialchars($product_forecast["product_name"]) ?></td>
                            <td><?= htmlspecialchars($product_forecast["forecast"]) ?></td>
                            <td>
                                <form action="forecasts.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= $product_forecast["id"] ?>">
                                    <button type="submit" name="delete_forecast" class="btn btn-danger">Delete Forecast</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include_once "footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
