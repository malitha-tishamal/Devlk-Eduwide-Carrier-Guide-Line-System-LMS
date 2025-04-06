<?php
session_start();
require_once '../includes/db-conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $experience_id = $_POST['id'];

    $query = "DELETE FROM experiences WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $experience_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Experience deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete experience']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Experience ID is missing']);
}

$conn->close();
?>
