<?php

require_once '../includes/db-conn.php'; 

session_start();


if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php"); 
    exit();
}

// Fetch user data from session or database
$former_student_id = $_SESSION['former_student_id'];
$query = "SELECT * FROM former_students WHERE former_student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $former_student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $nic = htmlspecialchars($_POST['nic']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $nowstatus = $_POST['nowstatus'];
    

    $university = $_POST['university'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $country = $_POST['country'] ?? '';

    $company_name = $_POST['company_name'] ?? '';
    $position = $_POST['position'] ?? '';
    $job_type = $_POST['job_type'] ?? '';

    if ($nowstatus == 'edu') {
        $updateQuery = "UPDATE former_students SET username = ?, email = ?, nic = ?, mobile = ?, nowstatus = ?, university = ?, course_name = ?, country = ? WHERE former_student_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssssi", $username, $email, $nic, $mobile, $nowstatus, $university, $course_name, $country, $former_student_id);
    } else {
        $updateQuery = "UPDATE former_students SET username = ?, email = ?, nic = ?, mobile = ?, nowstatus = ?, company_name = ?, position = ?, job_type = ? WHERE former_student_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssssi", $username, $email, $nic, $mobile, $nowstatus, $company_name, $position, $job_type, $former_student_id);
    }

    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: user-profile.php");
        exit();
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Error updating profile: " . $stmt->error;
        header("Location: user-profile.php");
        exit();
    }
}


$conn->close();
?>
