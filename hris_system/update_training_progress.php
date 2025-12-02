<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = intval($_POST['training_id']);
    $progress = intval($_POST['progress']);

    if ($training_id > 0 && $progress >= 0 && $progress <= 100) {
        if ($progress >= 100) {
            $progress = 100;
            $completion_date = date('Y-m-d');

            $stmt = $conn->prepare("
                UPDATE trainings 
                SET progress = ?, 
                    completion_date = ? 
                WHERE training_id = ?
            ");
            $stmt->bind_param("isi", $progress, $completion_date, $training_id);
        } else {
            $stmt = $conn->prepare("
                UPDATE trainings 
                SET progress = ?, 
                    completion_date = NULL 
                WHERE training_id = ?
            ");
            $stmt->bind_param("ii", $progress, $training_id);
        }

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
