<?php
session_start();
require_once '../includes/db-conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $company_name = trim($_POST['name']);
    $address = trim($_POST['adress']);
    $email = trim($_POST['email']);
    $category = $_POST['category'];
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($company_name) || empty($address) || empty($email) || empty($category) || empty($mobile) || empty($password)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields are required.';
        header("Location: pages-signup.php");
        exit();
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid email format.';
        header("Location: pages-signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM companies WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Email already exists.';
        $stmt->close();
        header("Location: pages-signup.php");
        exit();
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $insert = $conn->prepare("INSERT INTO companies (name, address, email, category, mobile, password) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("ssssss", $company_name, $address, $email, $category, $mobile, $hashed_password);

    if ($insert->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Company account created successfully.';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error creating account. Please try again.';
    }

    $insert->close();
    $conn->close();

    header("Location: pages-signup.php");
    exit();
} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request.';
    header("Location: pages-signup.php");
    exit();
}
