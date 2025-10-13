<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];

// Get form data
$experience_id = $_POST['id'] ?? '';
$title = $_POST['title'] ?? '';
$employment_type = $_POST['employment_type'] ?? '';
$company = $_POST['company'] ?? '';
$currently_working = isset($_POST['currently_working']) ? 1 : 0;
$start_month = $_POST['start_month'] ?? '';
$start_year = $_POST['start_year'] ?? '';
$end_month = $currently_working ? null : ($_POST['end_month'] ?? '');
$end_year = $currently_working ? null : ($_POST['end_year'] ?? '');
$description = $_POST['description'] ?? '';

// Validate required fields
if (empty($title) || empty($company) || empty($start_month) || empty($start_year)) {
    die("Please fill all required fields");
}

// ✅ Fixed SQL query
$sql = "UPDATE students_experiences 
        SET title = ?, employment_type = ?, company = ?, currently_working = ?, 
            start_month = ?, start_year = ?, end_month = ?, end_year = ?, 
            description = ?, updated_at = CURRENT_TIMESTAMP 
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);

// ✅ Corrected bind types (11 total parameters)
$stmt->bind_param(
    "sssisssssii",
    $title,
    $employment_type,
    $company,
    $currently_working,
    $start_month,
    $start_year,
    $end_month,
    $end_year,
    $description,
    $experience_id,
    $user_id
);

if ($stmt->execute()) {
    header("Location: pages-your-path.php?message=Experience updated successfully");
    exit();
} else {
    die("Failed to update experience: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
