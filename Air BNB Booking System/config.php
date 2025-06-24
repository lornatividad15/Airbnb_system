<?php
//FOR CONFIG
$host = 'localhost';
$db   = 'airbnb_system';
$user = 'root';
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
