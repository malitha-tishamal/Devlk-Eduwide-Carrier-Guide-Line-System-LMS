<?php
session_start();
require_once 'includes/db-conn.php'; // Ensure database connection

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // List of tables to check for users
    $tables = ['admins', 'lectures', 'students', 'former_students'];

    // Loop through each table to check if the email exists and the password is correct
    foreach ($tables as $table) {
        // Prepare the SQL query to check user
        $sql = "SELECT * FROM $table WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Check if a user exists and verify the password
            if ($user && password_verify($password, $user['password'])) {
                // Check account status for different user types
                if ($table == 'admins') {
                    // For admins, ensure the account is approved
                    if ($user['status'] == 'approved') {
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['success_message'] = "Welcome Admin!";
                        header("Location: admin/index.php");
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Your admin account has not been approved yet.";
                        header("Location: index.php");
                        exit();
                    }
                } elseif ($table == 'lectures') {
                    // For lecturers, ensure the account is approved
                    if ($user['status'] == 'approved') {
                        $_SESSION['lecturer_id'] = $user['id'];
                        $_SESSION['success_message'] = "Welcome Lecture!";
                        header("Location: lectures/index.php");
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Your lecture account has not been approved yet.";
                        header("Location: index.php");
                        exit();
                    }
                } elseif ($table == 'students') {
                    // For students, ensure the account is approved
                    if ($user['status'] == 'approved') {
                        $_SESSION['student_id'] = $user['id'];
                        $_SESSION['success_message'] = "Welcome Student!";
                        header("Location: pages-home.php");
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Your student account has not been approved yet.";
                        header("Location: index.php");
                        exit();
                    }
                } elseif ($table == 'former_students') {
                    // For former students, ensure the account is approved
                    if ($user['status'] == 'approved') {
                        $_SESSION['former_student_id'] = $user['id'];
                        $_SESSION['success_message'] = "Welcome Former Student!";
                        header("Location: oddstudents/index.php");
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Your former student account has not been approved yet.";
                        header("Location: index.php");
                        exit();
                    }
                }
            }
        }
    }

    // If no user is found or password is incorrect
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: index.php");
    exit();
}
?>
