<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if the education_id is passed
if (isset($_POST['education_id'])) {
    $education_id = $_POST['education_id'];

    // Prepare and execute the delete query
    $query = "DELETE FROM education WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $education_id, $user_id);

    if ($stmt->execute()) {
        // Redirect to the same page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // If query execution fails
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete education record']);
    }

    $stmt->close();
} else {
    // If the education_id is not set
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
