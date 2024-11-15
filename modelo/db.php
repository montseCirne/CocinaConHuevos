<?php
$dbInfo = "mysql:host=localhost;dbname=cocina";
$dbUser = "root";
$dbPassword = "";
try {
    $db = new PDO($dbInfo, $dbUser, $dbPassword);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    exit();
}
?>
