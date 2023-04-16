<?php
$servername = "database347-do-user-13902082-0.b.db.ondigitalocean.com";
$port = 25060;
$username = "doadmin";
$password = "AVNS_dGJG8cfh3XVCPJhdxjG";
$dbname = "defaultdb";

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $connect = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
