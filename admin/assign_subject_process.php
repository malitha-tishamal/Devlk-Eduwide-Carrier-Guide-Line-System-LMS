<?php
session_start();
require_once '../includes/db-conn.php';

// Check required fields
if (!isset($_POST['lecturer_id']) || empty($_POST['lecturer_id'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Please select a lecturer.';
    header("Location: pages-assign-subject.php");
    exit();
}

if (!isset($_POST['subject_ids']) || empty($_POST['subject_ids'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Please select at least one subject.';
    header("Location: pages-assign-subject.php");
    exit();
}

$lecturer_id = intval($_POST['lecturer_id']);
$subject_ids = $_POST['subject_ids'];

$insert_query = "INSERT INTO lectures_assignment (lecturer_id, subject_id) VALUES (?, ?)";
$check_query  = "SELECT id FROM lectures_assignment WHERE lecturer_id = ? AND subject_id = ?";

$insert_stmt = $conn->prepare($insert_query);
$check_stmt  = $conn->prepare($check_query);

$assigned = 0;
$skipped = 0;
$errors = 0;

foreach ($subject_ids as $subject_id) {
    $subject_id = intval($subject_id);

    // Check if already assigned
    $check_stmt->bind_param("ii", $lecturer_id, $subject_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $skipped++;
        continue; // Skip duplicate
    }

    // Insert new assignment
    $insert_stmt->bind_param("ii", $lecturer_id, $subject_id);
    if ($insert_stmt->execute()) {
        $assigned++;
    } else {
        $errors++;
    }
}

// Build final message
if ($assigned > 0) {
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = "$assigned subject(s) assigned successfully.";
}

if ($skipped > 0) {
    $_SESSION['status'] = 'warning';
    $_SESSION['message'] .= " $skipped subject(s) were already assigned and skipped.";
}

if ($errors > 0) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] .= " $errors subject(s) failed to assign due to a database error.";
}

// Close statements and connection
$insert_stmt->close();
$check_stmt->close();
$conn->close();

// Redirect back
header("Location: pages-assign-subject.php");
exit();
?>
