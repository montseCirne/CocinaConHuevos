<?php
// db.php
$dbInfo = "mysql:host=localhost;dbname=cocina";
$dbUser = "root";
$dbPassword = "";

try {
    $db = new PDO($dbInfo, $dbUser, $dbPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    exit();
}
?>
