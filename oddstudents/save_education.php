<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and assign form data to variables
    $school = $_POST['school'];
    $degree = $_POST['degree'];
    $field = $_POST['field'];
    $start_month = $_POST['start_month'];
    $start_year = $_POST['start_year'];
    $end_month = empty($_POST['end_month']) ? NULL : $_POST['end_month'];
    $end_year = empty($_POST['end_year']) ? NULL : $_POST['end_year'];
    $grade = $_POST['grade'];
    $activities = $_POST['activities'];
    $description = $_POST['description'];
    $education_id = isset($_POST['education_id']) ? $_POST['education_id'] : null;

    if ($education_id) {
        // UPDATE existing record
        $query = "UPDATE education SET 
                    school = ?, degree = ?, field_of_study = ?, 
                    start_month = ?, start_year = ?, 
                    end_month = ?, end_year = ?, 
                    grade = ?, activities = ?, description = ?
                  WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'ssssssssssii',
            $school, $degree, $field,
            $start_month, $start_year,
            $end_month, $end_year,
            $grade, $activities, $description,
            $education_id, $user_id
        );
    } else {
        // INSERT new record
        $query = "INSERT INTO education 
                  (user_id, school, degree, field_of_study, start_month, start_year, end_month, end_year, grade, activities, description) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'issssssssss',
            $user_id, $school, $degree, $field,
            $start_month, $start_year,
            $end_month, $end_year,
            $grade, $activities, $description
        );
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: pages-your-path.php?success=1");
        exit();
    } else {
        $stmt->close();
        header("Location: pages-your-path.php?error=1");
        exit();
    }
} else {
    header("Location: pages-your-path.php?error=invalid_request");
    exit();
}
?>
