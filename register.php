<?php
// Start session to store success/error messages
session_start();

// Include database connection
include_once("includes/db-conn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs and sanitize them
    $username    = $_POST['username'];
    $reg_id      = $_POST['reg_id'];
    $nic         = $_POST['nic'];
    $study_year  = $_POST['study_year'];
    $email       = $_POST['email'];
    $mobile      = $_POST['mobile'];
    $password    = password_hash($_POST['password'], PASSWORD_BCRYPT); // Password hashing
    $course_id   = $_POST['course_id']; // NEW: HND course ID

    // Check for duplicate email or NIC
    $checkQuery = "SELECT * FROM students WHERE email = ? OR nic = ?";
    if ($stmt = $conn->prepare($checkQuery)) {
        $stmt->bind_param("ss", $email, $nic);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Duplicate found
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Email or NIC already exists. Please try again with different details.';
            header("Location: pages-signup.php");
            $stmt->close();
            exit();
        }
        $stmt->close();
    }

    // Prepare SQL query to insert student data into the database, now including course_id
    $query = "INSERT INTO students (username, reg_id, nic, study_year, email, mobile, password, course_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssssss", $username, $reg_id, $nic, $study_year, $email, $mobile, $password, $course_id);

        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Student account successfully created!';
            header("Location: pages-signup.php");
            exit();
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Failed to create account. Please try again.';
            header("Location: pages-signup.php");
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database error. Please try again.';
        header("Location: pages-signup.php");
        exit();
    }
}
?>
