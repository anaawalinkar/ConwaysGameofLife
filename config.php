<?php
$DB_HOST = "localhost";
$DB_USER = "aawalinkar1";
$DB_PASS = "";   
$DB_NAME = "game_of_life";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
