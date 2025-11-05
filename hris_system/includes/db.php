<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default password for XAMPP
$dbname = 'hris_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
