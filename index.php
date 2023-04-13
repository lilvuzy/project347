<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Full Stack App</title>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">AppName</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Tab 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Tab 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Tab 3</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a>
            </li>
        </ul>
    </div>
</nav>
<!-- End Header -->

<!-- Main Content -->
<main role="main" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Graph Data</h1>
            <div id="graph-container" style="width:100%; height:400px;"></div>
        </div>
    </div>
</main>
<!-- End Main Content -->

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <span class="text-muted">&copy; 2023 AppName. All rights reserved.</span>
    </div>
</footer>
<!-- End Footer -->

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Custom script for fetching data and creating the chart -->
<script>
    $(document).ready(function() {
        $.getJSON('fetch_data.php', function(data) {
            let labels = [];
            let values = [];

            for (let i = 0; i < data.length; i++) {
                labels.push(data[i].date);
                values.push(data[i].value);
            }

            let ctx = document.getElementById('graph-container').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Data over the course of a year',
                        data: values,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    });
</script>

<!-- Font Awesome for Settings icon -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

</body>
</html>

