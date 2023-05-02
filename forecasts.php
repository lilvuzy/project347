<?php
// Start a session
session_start();
 // Include the config file
require_once "config.php";
function getSalesData($pdo, $product_id) {
    // Build the SQL query
    $query = "SELECT year, month, units_sold FROM sales_data WHERE product_id = :product_id AND units_sold IS NOT NULL ORDER BY year, month";

    // Prepare the query
    $stmt = $pdo->prepare($query);

    // Bind the product ID parameter
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Build an array of sales data
    $sales_data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sales_data[] = $row;
    }
    return $sales_data;
}

 // Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
 // Get all products from the database
$sql = "SELECT id, product_name FROM products WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["id"]]);
$products = $stmt->fetchAll();
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
    </div>
    <div class="container mt-4">
        <div class="list-group">
            <?php foreach ($products as $product) : ?>
                <div class="list-group-item">
                    <div class="row">
                        <div class="col">
                            <?= htmlspecialchars($product["product_name"]) ?>
                        </div>
                        <div class="col">
                            <select name="forecast_model" id="forecast_model_<?= $product["id"] ?>" class="form-control forecast-model" onchange="updateForecast(<?= $product["id"] ?>, this.value)">
                                <option value="average">Moving Average</option>
                                <option value="exponential_smoothing">Exponential Smoothing</option>
                                <option value="seasonal_index">Seasonal Index</option>
                                <option value="linear_regression">Linear Regression</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col forecast-result" id="forecast_result_<?= $product["id"] ?>">
                            -
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include_once "footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>

    const productSalesData = <?= json_encode(array_map(function($product) use ($pdo) {
        return [
            'id' => $product['id'],
            'data' => getSalesData($pdo, $product['id'])
        ];
    }, $products)) ?>;

    function updateForecast(productId, model) {
        const salesData = productSalesData.find(product => product.id === productId)?.data || [];
        let forecast;
        const now = new Date();
        let currentYear = now.getFullYear();
        let currentMonth = now.getMonth()+1;

        const nextMonth = getNextMonth(currentYear, currentMonth);

        switch (model) {
            case 'average':
                forecast = calculateMovingAverage(salesData);
                break;
            case 'exponential_smoothing':
                forecast = calculateExponentialSmoothing(salesData);
                break;
            case 'seasonal_index':
                // Assuming season length is 3 months
                forecast = calculateSeasonalIndex(salesData, 3);
                break;
            case 'linear_regression':
                forecast = calculateLinearRegression(salesData);
                break;
            default:
                forecast = 'Unknown model';
        }

    let forecastText = '';
    if (model == 'seasonal_index') {
        forecastText = `${getMonthName(currentMonth)} ${getMonthName(currentMonth + 1)} ${getMonthName(currentMonth + 2)} ${currentYear}: ${forecast}`;
    } else {
        forecastText = `${getMonthName(currentMonth)} ${currentYear}: ${forecast}`;
    }
    
    document.getElementById(`forecast_result_${productId}`).innerText = forecastText;
}



    function getNextMonth(year, month) {
        if (month === 12) {
            return { year: year + 1, month: 1 };
        }
        return { year: year, month: month + 1 };
    }

    function getMonthName(month) {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return monthNames[month - 1];
    }

    function calculateMovingAverage(salesData) {
        const totalMonths = salesData.length;
        const totalSales = salesData.reduce((sum, data) => sum + data.units_sold, 0);
        return (totalSales / totalMonths).toFixed(2);
    }

    function calculateExponentialSmoothing(salesData, alpha = 0.5) {
        let smoothedValue = salesData[0]?.units_sold || 0;
        for (let i = 1; i < salesData.length; i++) {
            smoothedValue = alpha * salesData[i].units_sold + (1 - alpha) * smoothedValue;
        }
        return smoothedValue.toFixed(2);
    }

    function calculateSeasonalIndex(salesData, seasonLength) {
        let averageSales = Array(seasonLength).fill(0);
        let seasonCount = Array(seasonLength).fill(0);
        for (let i = 0; i < salesData.length; i++) {
            averageSales[i % seasonLength] += salesData[i].units_sold;
            seasonCount[i % seasonLength]++;
        }
        const seasonalIndex = averageSales.map((total, index) => (total / seasonCount[index]).toFixed(2));
        return seasonalIndex;
    }

    function calculateLinearRegression(salesData) {
        const n = salesData.length;
        const x = salesData.map((_, i) => i);
        const sumX = x.reduce((a, b) => a + b, 0);
        const sumY = salesData.reduce((sum, data) => sum + data.units_sold, 0);
        const sumXY = salesData.reduce((sum, data, i) => sum + x[i] * data.units_sold, 0);
        const sumXSquare = x.reduce((a, b) => a + b * b, 0);
        const slope = (n * sumXY - sumX * sumY) / (n * sumXSquare - sumX * sumX);
        const intercept = (sumY - slope * sumX) / n;
        const forecast = intercept + slope * n;
        return forecast.toFixed(2);
    }
</script>

</body>
</html>
