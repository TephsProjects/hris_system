<?php
session_start();
include('includes/db.php');

// Only allow if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

header("Location: accounts.php");
exit();
?>
