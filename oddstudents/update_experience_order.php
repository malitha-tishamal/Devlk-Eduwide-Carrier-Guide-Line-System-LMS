<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if orderedIds is set
if (isset($_POST['orderedIds'])) {
    $orderedIds = $_POST['orderedIds'];

    // Update the order in the database
    $order = 1; // Start with position 1
    foreach ($orderedIds as $id) {
        $query = "UPDATE experiences SET position = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iii', $order, $id, $user_id);
        $stmt->execute();
        $order++;
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No order data received']);
}

$stmt->close();
?>
