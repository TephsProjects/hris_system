<?php
include('includes/db.php');

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_id = isset($_POST['leave_id']) ? intval($_POST['leave_id']) : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Validate input
    if ($leave_id <= 0 || empty($status)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input data.'
        ]);
        exit;
    }

    // Update leave status in database
    $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE leave_id = ?");
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Database prepare failed: ' . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("si", $status, $leave_id);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update leave status.'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
