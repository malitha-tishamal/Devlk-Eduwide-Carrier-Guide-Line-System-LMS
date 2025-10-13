<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];

// Get form data
$title = $_POST['title'] ?? '';
$employment_type = $_POST['employment_type'] ?? '';
$company = $_POST['company'] ?? '';
$location = $_POST['location'] ?? '';
$currently_working = isset($_POST['currently_working']) ? 1 : 0;
$start_month = $_POST['start_month'] ?? '';
$start_year = $_POST['start_year'] ?? '';
$end_month = $currently_working ? NULL : ($_POST['end_month'] ?? '');
$end_year = $currently_working ? NULL : ($_POST['end_year'] ?? '');
$description = $_POST['description'] ?? '';
$job_source = $_POST['job_source'] ?? '';

// Insert experience
$sql = "INSERT INTO students_experiences (user_id, title, employment_type, company, location, currently_working, start_month, start_year, end_month, end_year, description, job_source) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssisssiss", $user_id, $title, $employment_type, $company, $location, $currently_working, $start_month, $start_year, $end_month, $end_year, $description, $job_source);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Experience added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add experience: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>