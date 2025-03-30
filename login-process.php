<?php
session_start();
require_once 'includes/db-conn.php'; // DB connection

// Initialize login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Lockout after 3 failed attempts (5 minutes)
if ($_SESSION['login_attempts'] >= 3) {
    $timeout = 5 * 60; // 5 minutes
    $remaining = ($_SESSION['last_attempt_time'] + $timeout) - time();

    if ($remaining > 0) {
        $_SESSION['error_message'] = "Too many failed attempts. Try again in " . ceil($remaining / 60) . " minute(s).";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['login_attempts'] = 0; // Reset after timeout
    }
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tables = ['admins', 'lectures', 'students', 'former_students'];

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                // Reset attempts on success
                $_SESSION['login_attempts'] = 0;

                // Redirect based on user type
                if ($table == 'admins' && $user['status'] == 'approved') {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Admin!";
                    header("Location: admin/index.php");
                    exit();
                } elseif ($table == 'lectures' && $user['status'] == 'approved') {
                    $_SESSION['lecturer_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Lecturer!";
                    header("Location: lectures/index.php");
                    exit();
                } elseif ($table == 'students' && $user['status'] == 'approved') {
                    $_SESSION['student_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Student!";
                    header("Location: pages-home.php");
                    exit();
                } elseif ($table == 'former_students' && $user['status'] == 'approved') {
                    $_SESSION['former_student_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Former Student!";
                    header("Location: oddstudents/index.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Your $table account has not been approved yet.";
                    header("Location: index.php");
                    exit();
                }
            }
        }
    }

    $_SESSION['login_attempts'] += 1;
    $_SESSION['last_attempt_time'] = time();
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: index.php");
    exit();
}
?>
