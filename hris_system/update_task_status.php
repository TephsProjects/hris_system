<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $status = trim($_POST['status']);

    if ($task_id > 0 && in_array($status, ['Pending', 'Completed'])) {
        $completion_date = ($status === 'Completed') ? date('Y-m-d') : null;

        $stmt = $conn->prepare("
            UPDATE onboarding_tasks 
            SET status = ?, 
                due_date = due_date, 
                -- Keep due_date unchanged
                completed_at = ?
            WHERE task_id = ?
        ");
        $stmt->bind_param("ssi", $status, $completion_date, $task_id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
    } else {
        echo 'invalid';
    }
}
?>
