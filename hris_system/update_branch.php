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
$branch_id = !empty($_POST['branch_id']) ? $_POST['branch_id'] : NULL;

$stmt = $conn->prepare("UPDATE employees SET branch_id = ? WHERE emp_id = ?");
$stmt->bind_param("ii", $branch_id, $emp_id);

if ($stmt->execute()) {
    header("Location: employee_view.php?id=" . $emp_id . "&updated=1");
    exit();
} else {
    echo "Error updating branch.";
}
