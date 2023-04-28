<?php
$host = "database347-do-user-13902082-0.b.db.ondigitalocean.com";
$port = 25060;
$dbname = "defaultdb";
$user = "doadmin";
$pass = "AVNS_dGJG8cfh3XVCPJhdxjG";
$charset = "utf8mb4";
$sslmode = "REQUIRED";

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset;sslmode=$sslmode";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
