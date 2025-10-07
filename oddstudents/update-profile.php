<?php
require_once '../includes/db-conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $former_student_id = $_SESSION['former_student_id'];

    // Get input values and sanitize
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $nic        = trim($_POST['nic']);
    $mobile     = trim($_POST['mobile']);
    $nowstatus  = trim($_POST['nowstatus']);
    $course_id  = isset($_POST['course_id']) ? trim($_POST['course_id']) : null;
    $study_year = isset($_POST['study_year']) ? trim($_POST['study_year']) : null;

    // Optional: Basic validation
    if (empty($username) || empty($email)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Name and Email are required.';
        header("Location: user-profile.php");
        exit();
    }

    // Optional: Validate course_id and study_year if required
    if (empty($course_id) || empty($study_year)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Course and Study Year are required.';
        header("Location: user-profile.php");
        exit();
    }

    // Prepare the update statement
    $query = "UPDATE former_students 
              SET username = ?, email = ?, nic = ?, mobile = ?, nowstatus = ?, course_id = ?, study_year = ?
              WHERE id = ?"; 

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Prepare failed: ' . $conn->error;
        header("Location: user-profile.php");
        exit();
    }

    $stmt->bind_param(
        "ssssssii", 
        $username, 
        $email, 
        $nic, 
        $mobile, 
        $nowstatus, 
        $course_id, 
        $study_year, 
        $former_student_id
    );

    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Profile updated successfully.';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Failed to update profile. Try again.';
    }

    $stmt->close();
    $conn->close();

    // Redirect back to profile page
    header("Location: user-profile.php");
    exit();
} else {
    // Invalid access
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header("Location: user-profile.php");
    exit();
}
?>
