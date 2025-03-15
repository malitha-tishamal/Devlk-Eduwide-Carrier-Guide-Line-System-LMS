<?php
session_start();
// Include database connection
include_once("../includes/db-conn.php");
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $reg_id = mysqli_real_escape_string($conn, $_POST['reg_id']);
    $nic = strtoupper(mysqli_real_escape_string($conn, $_POST['nic']));
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $study_year = mysqli_real_escape_string($conn, $_POST['study_year']);
    $nowstatus = mysqli_real_escape_string($conn, $_POST['nowstatus']);
    $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT); // Hash password for security

    // Optional fields
    $university = isset($_POST['university']) ? mysqli_real_escape_string($conn, $_POST['university']) : '';
    $course_name = isset($_POST['course_name']) ? mysqli_real_escape_string($conn, $_POST['course_name']) : '';
    $country = isset($_POST['country']) ? mysqli_real_escape_string($conn, $_POST['country']) : '';
    $company_name = isset($_POST['company_name']) ? mysqli_real_escape_string($conn, $_POST['company_name']) : '';
    $position = isset($_POST['position']) ? mysqli_real_escape_string($conn, $_POST['position']) : '';
    $job_type = isset($_POST['job_type']) ? mysqli_real_escape_string($conn, $_POST['job_type']) : '';

    // Check if the email or registration ID already exists
    $check_email_query = "SELECT * FROM former_students WHERE email = '$email' OR reg_id = '$reg_id'";
    $result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Email or Registration ID already exists!";
        header("Location: pages-signup.php");
        exit();
    }

    // Insert new user data into the database
    $insert_query = "INSERT INTO former_students (username, reg_id, nic, email, mobile, study_year, nowstatus, university, course_name, country, company_name, position, job_type, password)
                     VALUES ('$username', '$reg_id', '$nic', '$email', '$mobile', '$study_year', '$nowstatus', '$university', '$course_name', '$country', '$company_name', '$position', '$job_type', '$password')";

    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Account created successfully!";
        header("Location: pages-signup.php");
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Error: " . mysqli_error($conn);
        header("Location: pages-signup.php");
    }
}
?>
