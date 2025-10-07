<?php
session_start();
include_once("../includes/db-conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $reg_id = mysqli_real_escape_string($conn, $_POST['reg_id']);
    $nic = strtoupper(mysqli_real_escape_string($conn, $_POST['nic']));
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $study_year = mysqli_real_escape_string($conn, $_POST['study_year']);
    $course_id = mysqli_real_escape_string($conn, $_POST['course']);
    $nowstatus = mysqli_real_escape_string($conn, $_POST['nowstatus']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_query = "SELECT id FROM former_students WHERE reg_id='$reg_id' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Email or Registration ID already exists!";
        header("Location: pages-signup.php");
        exit();
    }

    $insert_query = "INSERT INTO former_students (username, reg_id, nic, email, mobile, study_year, course_id, nowstatus, password, created_at)
                     VALUES ('$username', '$reg_id', '$nic', '$email', '$mobile', '$study_year', '$course_id', '$nowstatus', '$password', NOW())";

    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Account created successfully!";
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Database error: " . mysqli_error($conn);
    }

    header("Location: pages-signup.php");
    exit();
}
?>
