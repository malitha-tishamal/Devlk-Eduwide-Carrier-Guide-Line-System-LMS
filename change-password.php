<?php
session_start();
require_once "includes/db-conn.php"; 

// Check if admin is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); 
    exit();
}

$admin_id = $_SESSION['student_id'];  

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if ($new_password !== $confirm_password) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'New password and confirmation do not match.';
        header("Location: user-profile.php");
        exit();
    }

    if (strlen($new_password) < 8) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Password must be at least 8 characters long.';
        header("Location: user-profile.php");
        exit();
    }

    $sql = "SELECT * FROM students WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (password_verify($current_password, $admin['password'])) {
            $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

            $update_sql = "UPDATE students SET password = ? WHERE id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                $update_stmt->bind_param("si", $hashed_new_password, $admin_id);
                if ($update_stmt->execute()) {

                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = 'Password changed successfully.';
                    header("Location: user-profile.php");
                    exit();
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to update password in the database.';
                }
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Current password is incorrect.';
        }
        $stmt->close();
    }

    $conn->close();

    header("Location: user-profile.php");
    exit();
}
?>
