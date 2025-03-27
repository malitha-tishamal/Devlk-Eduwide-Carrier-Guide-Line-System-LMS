<?php
session_start();
require_once 'includes/db-conn.php';

$user_id = $_SESSION['former_student_id'];

$school = $_POST['school'];
$degree = $_POST['degree'];
$field = $_POST['field'];
$start_month = $_POST['start_month'];
$start_year = $_POST['start_year'];
$end_month = $_POST['end_month'];
$end_year = $_POST['end_year'];
$grade = $_POST['grade'];
$activities = $_POST['activities'];
$description = $_POST['description'];

$sql = "INSERT INTO education (user_id, school, degree, field, start_month, start_year, end_month, end_year, grade, activities, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssssissss", $user_id, $school, $degree, $field, $start_month, $start_year, $end_month, $end_year, $grade, $activities, $description);
$stmt->execute();

header("Location: pages-your-path.php");
exit;
?>
