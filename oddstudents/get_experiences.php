<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

if (isset($_GET['id'])) {
    $experience_id = $_GET['id'];

    $query = "SELECT * FROM experiences WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $experience_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $experience = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'experience' => $experience]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Experience not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid experience ID']);
}
