<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$about_text = $_POST['about'] ?? '';

// Check if about record exists
$check_sql = "SELECT id FROM students_about WHERE user_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $sql = "UPDATE students_about SET about_text = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $about_text, $user_id);
} else {
    // Insert new record
    $sql = "INSERT INTO students_about (user_id, about_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $about_text);
}

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>