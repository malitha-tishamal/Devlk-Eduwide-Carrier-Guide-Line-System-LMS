<?php
session_start();
require_once "../includes/db-conn.php"; // Make sure this path is correct

if (!isset($_SESSION['former_student_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Get data from POST
$user_id = $_SESSION['former_student_id'];
$jobTitle = $_POST['job-title'];
$companyName = $_POST['company-name'];
$startDate = $_POST['start-date'];
$endDate = $_POST['end-date'];
$jobDescription = $_POST['job-description'];

$sql = "INSERT INTO work_experience (user_id, job_title, company_name, start_date, end_date, job_description)
        VALUES ('$user_id', '$jobTitle', '$companyName', '$startDate', '$endDate', '$jobDescription')";

if ($conn->query($sql) === TRUE) {
    echo "New work experience added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
