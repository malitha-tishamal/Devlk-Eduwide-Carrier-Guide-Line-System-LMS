<?php
session_start();
require_once 'includes/db-conn.php';

// Set timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');
$conn->query("SET time_zone = '+05:30'");

// Initialize login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_stage'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Lockout durations based on failed attempts
$lockout_durations = [5 * 60, 10 * 60, 20 * 60, 60 * 60];

// Check if locked out
if ($_SESSION['login_attempts'] >= 3) {
    $stage = $_SESSION['lockout_stage'];
    $timeout = $lockout_durations[$stage] ?? end($lockout_durations);
    $remaining = ($_SESSION['last_attempt_time'] + $timeout) - time();

    if ($remaining > 0) {
        $_SESSION['error_message'] = "Too many failed attempts. Try again in " . ceil($remaining / 60) . " minute(s).";
        $_SESSION['lockout_remaining'] = $remaining;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_stage'] += 1;
    }
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Order changed: former_students comes before students
    $tables = ['admins', 'lectures', 'former_students', 'students', 'companies'];

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                // Reset login attempts
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lockout_stage'] = 0;

                $current_time = date("Y-m-d H:i:s");

                // Role-specific login
                if ($table == 'admins' && $user['status'] == 'approved') {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Admin!";
                    $update = $conn->prepare("UPDATE admins SET last_login = ? WHERE id = ?");
                    $update->bind_param("si", $current_time, $user['id']);
                    $update->execute();
                    header("Location: admin/index.php");
                    exit();

                } elseif ($table == 'lectures' && $user['status'] == 'approved') {
                    $_SESSION['lecturer_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Lecturer!";
                    $update = $conn->prepare("UPDATE lectures SET last_login = ? WHERE id = ?");
                    $update->bind_param("si", $current_time, $user['id']);
                    $update->execute();
                    header("Location: lectures/index.php");
                    exit();

                } elseif ($table == 'former_students' && $user['status'] == 'approved') {
                    $_SESSION['former_student_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Former Student!";
                    $update = $conn->prepare("UPDATE former_students SET last_login = ? WHERE id = ?");
                    $update->bind_param("si", $current_time, $user['id']);
                    $update->execute();
                    header("Location: oddstudents/index.php");
                    exit();

                } elseif ($table == 'students' && $user['status'] == 'approved') {
                    $_SESSION['student_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Student!";
                    $update = $conn->prepare("UPDATE students SET last_login = ? WHERE id = ?");
                    $update->bind_param("si", $current_time, $user['id']);
                    $update->execute();
                    header("Location: user-profile.php");
                    exit();

                } elseif ($table == 'companies' && $user['status'] == 'approved') {
                    $_SESSION['company_id'] = $user['id'];
                    $_SESSION['success_message'] = "Welcome Company!";
                    $update = $conn->prepare("UPDATE companies SET last_login = ? WHERE id = ?");
                    $update->bind_param("si", $current_time, $user['id']);
                    $update->execute();
                    header("Location: companies/index.php");
                    exit();

                } elseif ($table == 'companies') {
                    $_SESSION['error_message'] = "Your company account has not been approved yet.";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Your $table account has not been approved yet.";
                    header("Location: index.php");
                    exit();
                }
            }
        }
    }

    // Failed login
    $_SESSION['login_attempts'] += 1;
    $_SESSION['last_attempt_time'] = time();
    $_SESSION['error_message'] = "Invalid email or password.";

    // Increase lockout stage every 3 attempts
    if ($_SESSION['login_attempts'] % 3 == 0) {
        $_SESSION['lockout_stage'] += 1;
    }

    header("Location: index.php");
    exit();
}
?>
