<?php
session_start();
require_once '../includes/db-conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

try {
    $title = $_POST['title'] ?? '';
    $employment_type = $_POST['employment_type'] ?? '';
    $company = $_POST['company'] ?? '';
    $currently_working = isset($_POST['currently_working']) ? 1 : 0;
    $start_month = $_POST['start_month'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_month = $_POST['end_month'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $location = $_POST['location'] ?? '';
    $location_type = $_POST['location_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $job_source = $_POST['job_source'] ?? '';

    if (empty($title) || empty($company) || empty($start_month) || empty($start_year)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO experiences 
        (user_id, title, employment_type, company, currently_working, start_month, start_year, end_month, end_year, location, location_type, description, job_source)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissssssss", 
        $user_id,
        $title, 
        $employment_type, 
        $company, 
        $currently_working, 
        $start_month, 
        $start_year, 
        $end_month, 
        $end_year, 
        $location, 
        $location_type, 
        $description, 
        $job_source
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database insert failed.']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
