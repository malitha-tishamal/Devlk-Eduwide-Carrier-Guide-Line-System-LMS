<?php
session_start();
require_once '../includes/db-conn.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_account'])) {

    // Sanitize input
    $username = trim($_POST['username']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $category = trim($_POST['category']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($address) || empty($email) || empty($mobile) || empty($category) || empty($password)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Please fill in all fields.';
        header('Location: pages-add-company.php');
        exit();
    }

    // Check for duplicate email
    $check_sql = "SELECT id FROM companies WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'This email is already registered.';
        $check_stmt->close();
        header('Location: pages-add-company.php');
        exit();
    }
    $check_stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default profile picture (you can change this to a real upload logic later)
    $default_profile = 'default.png'; // Make sure this file exists in the /companies folder

    // Insert company
    $sql = "INSERT INTO companies (username, address, email, mobile, category, password, profile_picture, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $address, $email, $mobile, $category, $hashed_password, $default_profile);

    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Company account created successfully!';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database error: Could not create account.';
    }

    $stmt->close();
    $conn->close();

    header('Location: pages-add-company.php');
    exit();
} else {
    // If form not submitted correctly
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request.';
    header('Location: pages-add-company.php');
    exit();
}
