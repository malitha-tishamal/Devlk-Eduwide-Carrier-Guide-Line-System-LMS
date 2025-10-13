<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$education_id = $_POST['education_id'] ?? '';

// Verify that the education belongs to the user
$sql = "DELETE FROM students_education WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $education_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Education record deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Education record not found or you don't have permission to delete it"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete education record"]);
}

$stmt->close();
$conn->close();
?>