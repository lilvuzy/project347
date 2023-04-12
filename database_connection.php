<?php
// database_connection.php

// Connection parameters
$servername = "database347-do-user-13902082-0.b.db.ondigitalocean.com";
$username = "doadmin";
$password = "************************";
$port = 25060;
$database = "defaultdb";
$sslmode = "REQUIRED";

// Create connection
$conn = mysqli_init();

// Require SSL for connection
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, $sslmode);

// Connect to the database
if (!mysqli_real_connect($conn, $servername, $username, $password, $database, $port)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

// Close the connection
mysqli_close($conn);

?>
