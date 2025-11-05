<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if performance ID is provided
if (!isset($_GET['id'])) {
    header("Location: performance_list.php");
    exit();
}

$perf_id = $_GET['id'];

// Delete the performance evaluation
$stmt = $conn->prepare("DELETE FROM performance WHERE perf_id = ?");
$stmt->bind_param("i", $perf_id);

if ($stmt->execute()) {
    // Optionally, you could set a session message to show a success alert
    $_SESSION['message'] = "✅ Performance evaluation deleted successfully!";
} else {
    $_SESSION['message'] = "❌ Failed to delete evaluation: " . $conn->error;
}

// Redirect back to the performance list
header("Location: performance_list.php");
exit();
