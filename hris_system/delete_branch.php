<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid branch ID.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM branches WHERE branch_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: branch_list.php");
exit();
