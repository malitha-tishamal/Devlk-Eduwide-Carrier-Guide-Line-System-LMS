<?php
require_once "../includes/db-conn.php"; // Correct path to your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request
    $id = $_POST['id'];
    $jobTitle = $_POST['job-title'];
    $companyName = $_POST['company-name'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];
    $jobDescription = $_POST['job-description'];

    // Prepare SQL query to update the work experience by ID
    $sql = "UPDATE work_experience SET job_title = ?, company_name = ?, start_date = ?, end_date = ?, job_description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('MySQL prepare failed: ' . $conn->error);
    }

    // Bind parameters and execute the update
    $stmt->bind_param("sssssi", $jobTitle, $companyName, $startDate, $endDate, $jobDescription, $id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
