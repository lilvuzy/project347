<?php
session_start();
require_once "config.php";
 // Check if the request method is POST and action is update_sales_data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update_sales_data") {
    // Get the product ID and year from the POST request
    $product_id = intval($_POST["product_id"]);
    $year = intval($_POST["year"]);
    $sales_data = $_POST["sales_data"];
     // Loop through the sales data
    foreach ($sales_data as $month => $units_sold) {
        // Check if the units sold is not empty
        if ($units_sold != "") {
            // Check if the sales data row exists
            if (salesDataRowExists($pdo, $product_id, $year, $month)) {
                // Update the sales data
                $sql = "UPDATE sales_data SET units_sold = ? WHERE product_id = ? AND year = ? AND month = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$units_sold, $product_id, $year, $month]);
            } else {
                // Insert the sales data
                $sql = "INSERT INTO sales_data (product_id, year, month, units_sold) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$product_id, $year, $month, $units_sold]);
            }
        }
    }
    echo "Sales data updated successfully!";
    exit;
}
 // Check if the action is fetch_sales_data
if (isset($_GET["action"]) && $_GET["action"] == "fetch_sales_data") {
    // Get the product ID and year from the GET request
    $product_id = intval($_GET["product_id"]);
    $year = intval($_GET["year"]);
    $sales_data = fetchSalesData($pdo, $product_id, $year);
    header("Content-Type: application/json");
    echo json_encode($sales_data);
    exit;
}
 // Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
 // Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];
    // Check if the action is add_product
    if (isset($_POST["add_product"])) {
        $product_name = trim($_POST["product_name"]);
        $sql = "INSERT INTO products (user_id, product_name) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $product_name]);
        header("location: products.php");
    // Check if the action is delete_product
    } elseif (isset($_POST["delete_product"])) {
        $product_id = intval($_POST["product_id"]);
        $sql = "DELETE FROM sales_data WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        $sql = "DELETE FROM products WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $user_id]);
        header("location: products.php");
    }
}
 // Check if the request method is GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if the action is fetch_sales_data
    if (isset($_GET["action"]) && $_GET["action"] == "fetch_sales_data") {
        $product_id = intval($_GET["product_id"]);
        $year = intval($_GET["year"]);
        $salesData = fetchSalesData($pdo, $product_id, $year);
        header("Content-Type: application/json");
        echo json_encode($salesData);
    }
}
 // Function to fetch user products
function fetchUserProducts($pdo, $user_id) {
    $sql = "SELECT id, product_name FROM products WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
 // Function to check if the sales data row exists
function salesDataRowExists($pdo, $product_id, $year, $month) {
    $sql = "SELECT COUNT(*) FROM sales_data WHERE product_id = ? AND year = ? AND month = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id, $year, $month]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}
 // Function to fetch sales data
function fetchSalesData($pdo, $product_id, $year) {
    $sql = "SELECT month, units_sold FROM sales_data WHERE product_id = ? AND year = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id, $year]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $salesData = [];
    foreach ($rows as $row) {
        $salesData[intval($row["month"])] = intval($row["units_sold"]);
    }
    return $salesData;
}
 // Get the user products and current year and month
$products = fetchUserProducts($pdo, $_SESSION["id"]);
$currentYear = (int) date("Y");
$currentMonth = (int) date("n");
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
                <li class="list-group-item">
                    <h4><?= htmlspecialchars($product["product_name"]) ?></h4>
                    
                    <form action="products.php" method="post" class="mb-0 float-right">
                        <input type="hidden" name="product_id" value="<?= $product["id"] ?>">
                        <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <div class="mt-2">
                        <label for="year_<?= $product["id"] ?>">Year:</label>
                        <select name="year" id="year_<?= $product["id"] ?>" class="form-control" onchange="updateMonthInputs(<?= $product["id"] ?>)" required>
                            <?php for ($i = $currentYear; $i >= $currentYear - 5; $i--): ?>
                                <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label>Monthly Sales:</label>
                        <div class="row">
                            <?php
                            $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                            for ($i = 0; $i < 6; $i++): ?>
                                <div class="col">
                                <input type="number" name="sales_data[]" class="form-control d-inline-block w-100 sales_month_<?= $product["id"] ?>" onchange="submitSalesData(<?= $product["id"] ?>)" placeholder="<?= $monthNames[$i] ?>" <?= $currentMonth > $i + 1 ? '' : 'disabled' ?> value="<?= isset($salesData[$i + 1]) ? $salesData[$i + 1] : '' ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div class="row mt-2">
                            <?php for ($i = 6; $i < 12; $i++): ?>
                                <div class="col">
                                <input type="number" name="sales_data[]" class="form-control d-inline-block w-100 sales_month_<?= $product["id"] ?>" onchange="submitSalesData(<?= $product["id"] ?>)" placeholder="<?= $monthNames[$i] ?>" <?= $currentMonth > $i + 1 ? '' : 'disabled' ?> value="<?= isset($salesData[$i + 1]) ? $salesData[$i + 1] : '' ?>">

                                </div>
                            <?php endfor; ?>
                        </div>
                        
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php include_once "footer.php"; ?>
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Add Bootstrap CSS and JS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        



        
    
        document.querySelectorAll('[id^="year_"]').forEach(yearSelect => {
            // Add an event listener to each year select element
            yearSelect.addEventListener('change', event => {
                // Get the product id from the element's id
                const productId = event.target.id.split('_').pop();
                // Update the month inputs for the selected product
                updateMonthInputs(productId);
            });
        });
        // Submit sales data for a given product
        async function submitSalesData(productId) {
            // Get the year select element for the product
            const yearSelect = document.getElementById(`year_${productId}`);
            // Get the selected year value
            const year = yearSelect.options[yearSelect.selectedIndex].value;
            // Create a new form data object
            let formData = new FormData();
            // Append the action, product id and year to the form data
            formData.append("action", "update_sales_data");
            formData.append("product_id", productId);
            formData.append("year", year);
            // Get the month inputs for the product
            const monthInputs = document.querySelectorAll(`.sales_month_${productId}`);
            // Append the sales data for each month to the form data
            monthInputs.forEach((input, index) => {
                formData.append("sales_data[" + (index + 1) + "]", input.value);
            });
            // Send the form data to the server
            const response = await fetch("products.php", {
                method: "POST",
                body: formData,
            });
            // Get the response from the server
            const result = await response.text();
            // Log the result of the sales data update
            console.log("Sales data update result:", result);
        }
        // Update the month inputs for a given product
        async function updateMonthInputs(productId) {
            // Get the year select element for the product
            let yearSelect = document.getElementById("year_" + productId);
            // Get the month inputs for the product
            let salesMonthInputs = document.getElementsByClassName("sales_month_" + productId);
            // Get the selected year value
            let selectedYear = parseInt(yearSelect.value);
            // Get the current year
            let currentYear = new Date().getFullYear();
            // Get the current month
            let currentMonth = new Date().getMonth() + 1;
            // Loop through the month inputs
            for (let i = 0; i < salesMonthInputs.length; i++) {
                // If the selected year is less than the current year, enable the input
                if (selectedYear < currentYear) {
                    salesMonthInputs[i].removeAttribute("disabled");
                // If the selected year is equal to the current year and the month is less than the current month, enable the input
                } else if (selectedYear === currentYear && i + 1 < currentMonth) {
                    salesMonthInputs[i].removeAttribute("disabled");
                // Otherwise, disable the input
                } else {
                    salesMonthInputs[i].setAttribute("disabled", true);
                }
            }
            // Get the sales data for the product from the server
            const response = await fetch(`products.php?action=fetch_sales_data&product_id=${productId}&year=${selectedYear}`);
            // Get the sales data as JSON
            const salesData = await response.json();
            // Loop through the month inputs
            for (let i = 0; i < salesMonthInputs.length; i++) {
                // Set the value of the input to the sales data for the month, or an empty string if there is no data
                salesMonthInputs[i].value = salesData[i + 1] || '';
            }
        }
        // Add an event listener to each year select element
        document.querySelectorAll('[id^="year_"]').forEach(yearSelect => {
            // Get the product id from the element's id
            const productId = yearSelect.id.split('_').pop();
            // Update the month inputs for the selected product
            updateMonthInputs(productId);
        });
    </script>
</body>
</html>
