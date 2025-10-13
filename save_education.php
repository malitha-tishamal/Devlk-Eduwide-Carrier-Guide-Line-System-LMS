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
$grade = $_POST['grade'] ?? '';
$activities = $_POST['activities'] ?? '';
$description = $_POST['description'] ?? '';
$current = isset($_POST['currently_studying']) ? 1 : 0;

// Convert empty end_month/year to NULL if currently studying or empty
$end_month = $current || empty($_POST['end_month']) ? "NULL" : "'" . $conn->real_escape_string($_POST['end_month']) . "'";
$end_year  = $current || empty($_POST['end_year'])  ? "NULL" : intval($_POST['end_year']);

// Prepare SQL query
$sql = "INSERT INTO students_education
        (user_id, school, degree, field_of_study, start_month, start_year, end_month, end_year, grade, activities, description)
        VALUES ($user_id, '".$conn->real_escape_string($school)."', '".$conn->real_escape_string($degree)."', '".$conn->real_escape_string($field)."', '$start_month', '$start_year', $end_month, $end_year, '".$conn->real_escape_string($grade)."', '".$conn->real_escape_string($activities)."', '".$conn->real_escape_string($description)."')";

// Execute query
if ($conn->query($sql)) {
    header("Location: pages-your-path.php?msg=success");
    exit();
} else {
    header("Location: pages-your-path.php?msg=error");
    exit();
}

$conn->close();
?>
