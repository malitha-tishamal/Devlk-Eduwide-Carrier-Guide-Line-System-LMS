<?php
session_start();
require_once 'includes/db-conn.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];

// Get form data
$education_id = $_POST['education_id'] ?? '';
$school = $_POST['school'] ?? '';
$degree = $_POST['degree'] ?? '';
$field = $_POST['field'] ?? '';
$start_month = $_POST['start_month'] ?? '';
$start_year = $_POST['start_year'] ?? '';
$grade = $_POST['grade'] ?? '';
$activities = $_POST['activities'] ?? '';
$description = $_POST['description'] ?? '';
$current = isset($_POST['currently_studying']) ? 1 : 0;

// Validate required field
if (empty($school)) {
    die("School is required");
}

// Handle empty end month/year or currently studying
$end_month_sql = $current || empty($_POST['end_month']) ? "NULL" : "'" . $conn->real_escape_string($_POST['end_month']) . "'";
$end_year_sql  = $current || empty($_POST['end_year'])  ? "NULL" : intval($_POST['end_year']);

// Build SQL query
$sql = "UPDATE students_education SET 
        school = '".$conn->real_escape_string($school)."',
        degree = '".$conn->real_escape_string($degree)."',
        field_of_study = '".$conn->real_escape_string($field)."',
        start_month = '".$conn->real_escape_string($start_month)."',
        start_year = '".$conn->real_escape_string($start_year)."',
        end_month = $end_month_sql,
        end_year = $end_year_sql,
        grade = '".$conn->real_escape_string($grade)."',
        activities = '".$conn->real_escape_string($activities)."',
        description = '".$conn->real_escape_string($description)."',
        updated_at = CURRENT_TIMESTAMP
        WHERE id = ".intval($education_id)." AND user_id = ".intval($user_id);

if ($conn->query($sql)) {
    header("Location: pages-your-path.php?message=Education updated successfully");
    exit();
} else {
    die("Failed to update education: " . $conn->error);
}

$conn->close();
?>
