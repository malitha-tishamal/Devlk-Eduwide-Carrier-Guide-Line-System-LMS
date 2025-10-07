<?php
session_start();
require_once 'includes/db-conn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['student_id'])) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Unauthorized access!';
        header("Location: user-profile.php");
        exit();
    }

    $user_id = $_SESSION['student_id'];
    $username = trim($_POST['username']);
    $reg_id = trim($_POST['reg_id']);
    $study_year = trim($_POST['study_year']);
    $email = trim($_POST['email']);
    $nic = trim($_POST['nic']);
    $mobile = trim($_POST['mobile']);
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid email format!';
        header("Location: user-profile.php");
        exit();
    }

    // Update user details in the database including HND course
    $sql = "UPDATE students SET username = ?, reg_id = ?, study_year = ?, email = ?, nic = ?, mobile = ?, course_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssssii", $username, $reg_id, $study_year, $email, $nic, $mobile, $course_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Profile updated successfully!';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Failed to update profile!';
        }
        $stmt->close();
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database error!';
    }

    // Redirect back to profile page
    header("Location: user-profile.php");
    exit();
}
?>
