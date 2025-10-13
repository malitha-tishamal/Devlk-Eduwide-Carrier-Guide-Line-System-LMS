<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$experience_id = $_POST['id'] ?? '';

// Verify that the experience belongs to the user
$sql = "DELETE FROM students_experiences WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $experience_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Experience deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Experience not found or you don't have permission to delete it"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete experience"]);
}

$stmt->close();
$conn->close();
?>