<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$summary = $_POST['summary'] ?? '';

// Check if summary record exists
$check_sql = "SELECT id FROM students_summaries WHERE user_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $sql = "UPDATE students_summaries SET summary = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
} else {
    // Insert new record
    $sql = "INSERT INTO students_summaries (user_id, summary) VALUES (?, ?)";
}

$stmt = $conn->prepare($sql);
if ($result->num_rows > 0) {
    $stmt->bind_param("si", $summary, $user_id);
} else {
    $stmt->bind_param("is", $user_id, $summary);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Summary saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save summary"]);
}

$stmt->close();
$conn->close();
?>