<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];

// Get form data
$school = $_POST['school'] ?? '';
$degree = $_POST['degree'] ?? '';
$field = $_POST['field'] ?? '';
$start_month = $_POST['start_month'] ?? '';
$start_year = $_POST['start_year'] ?? '';
$end_month = $_POST['end_month'] ?? '';
$end_year = $_POST['end_year'] ?? '';
$grade = $_POST['grade'] ?? '';
$activities = $_POST['activities'] ?? '';
$description = $_POST['description'] ?? '';

// Insert education
$sql = "INSERT INTO students_education (user_id, school, degree, field_of_study, start_month, start_year, end_month, end_year, grade, activities, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssiissss", $user_id, $school, $degree, $field, $start_month, $start_year, $end_month, $end_year, $grade, $activities, $description);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>