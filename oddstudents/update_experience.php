<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $experience_id = $_POST['id'];
    $title = $_POST['title'];
    $company = $_POST['company'];
    $start_month = $_POST['start_month'];
    $start_year = $_POST['start_year'];
    $description = $_POST['description'];
    $currently_working = isset($_POST['currently_working']) ? 1 : 0;
    $end_month = $_POST['end_month'];
    $end_year = $_POST['end_year'];

    // Update experience data in the database
    $query = "UPDATE experiences SET title = ?, company = ?, start_month = ?, start_year = ?, description = ?, currently_working = ?, end_month = ?, end_year = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssiisii', $title, $company, $start_month, $start_year, $description, $currently_working, $end_month, $end_year, $experience_id, $user_id);

    if ($stmt->execute()) {
        // Redirect to experience list page after updating
        header("Location: pages-your-path.php");
        exit();
    } else {
        echo "Error updating experience.";
    }
}
