<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'includes/db-conn.php'; // DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capture form data safely
    $reporterName     = $_POST['reporterName'] ?? '';
    $reportDate       = $_POST['reportDate'] ?? '';
    $reportTitle      = $_POST['reportTitle'] ?? '';
    $issueType        = $_POST['issueType'] ?? '';
    $issueDescription = $_POST['issueDescription'] ?? '';
    $location         = $_POST['location'] ?? '';
    $referenceLink    = $_POST['referenceLink'] ?? '';
    $priorityLevel    = $_POST['priorityLevel'] ?? '';

    // Validate required fields
    if (empty($reporterName) || empty($reportTitle) || empty($issueDescription)) {
        $_SESSION['error_message'] = "Please fill in all required fields!";
        header("Location: pages-report.php");
        exit();
    }

    // Insert report into 'reports' table
    $stmt = $conn->prepare("INSERT INTO reports 
        (reporter_name, report_date, report_title, issue_type, description, location, reference_link, priority) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssss",
        $reporterName,
        $reportDate,
        $reportTitle,
        $issueType,
        $issueDescription,
        $location,
        $referenceLink,
        $priorityLevel
    );

    if ($stmt->execute()) {
        $report_id = $conn->insert_id; // Get the last inserted report ID

        // Handle photo uploads
        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = 'uploads/reports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
                $fileName = time() . '_' . basename($_FILES['photos']['name'][$index]);
                if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                    // Insert each photo into 'report_photos' table
                    $photoStmt = $conn->prepare("INSERT INTO report_photos (report_id, photo_path) VALUES (?, ?)");
                    $photoStmt->bind_param("is", $report_id, $fileName);
                    $photoStmt->execute();
                    $photoStmt->close();
                }
            }
        }

        $_SESSION['success_message'] = "Report submitted successfully!";
    } else {
        $_SESSION['error_message'] = "Error submitting report. Please try again.";
    }

    $stmt->close();
    header("Location: pages-report.php");
    exit();
}
?>
