<?php
//logout.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

setcookie("type", "", time()-3600);

header("location:login.php");

?>
