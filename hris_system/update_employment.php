<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_POST['emp_id'])) {
    die("Invalid request.");
}

$emp_id = $_POST['emp_id'];
$employment_status = $_POST['employment_status'];
$employment_type = $_POST['employment_type'];

$stmt = $conn->prepare("
    UPDATE employees 
    SET employment_status = ?, employment_type = ? 
    WHERE emp_id = ?
");

$stmt->bind_param("ssi", $employment_status, $employment_type, $emp_id);

if ($stmt->execute()) {
    header("Location: employee_view.php?id=" . $emp_id . "&updated=1");
    exit();
} else {
    echo "Error updating information.";
}
