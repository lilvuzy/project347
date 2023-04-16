<?php
$servername = "database347-do-user-13902082-0.b.db.ondigitalocean.com";
$port = 25060;
$username = "doadmin";
$password = "AVNS_dGJG8cfh3XVCPJhdxjG";
$dbname = "defaultdb";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable SSL connection
$conn->ssl_set(
    NULL, // key
    NULL, // cert
    NULL, // ca
    NULL, // capath
    'REQUIRED' // cipher
);
?>