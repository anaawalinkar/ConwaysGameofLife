<?php
$DB_HOST = //put your db info
$DB_USER = //put your db info
$DB_PASS = //put your db info    
$DB_NAME = //put your db info

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
