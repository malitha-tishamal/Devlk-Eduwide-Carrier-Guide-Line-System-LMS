<?php
session_start();
include '../includes/db-conn.php';

// Get the form data
$assignment_id = $_POST['assignment_id'];
$lecturer_id = $_POST['lecturer_id'];
$subject_id = $_POST['subject_id'];

// Check if all fields are provided
if (empty($assignment_id) || empty($lecturer_id) || empty($subject_id)) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Please select a lecturer and a subject.';
    header("Location: pages-assign-subject-manage.php");
    exit();
}

// Check if this lecturer already has this subject assigned (avoid duplicates)
$check_query = "SELECT * FROM lectures_assignment WHERE lecturer_id = ? AND subject_id = ? AND id != ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("iii", $lecturer_id, $subject_id, $assignment_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'This subject is already assigned to the selected lecturer.';
    $check_stmt->close();
    $conn->close();
    header("Location: pages-assign-subject-manage.php");
    exit();
}
$check_stmt->close();

// Update the assignment
$update_query = "UPDATE lectures_assignment SET lecturer_id = ?, subject_id = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("iii", $lecturer_id, $subject_id, $assignment_id);

if ($stmt->execute()) {
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Assignment updated successfully.';
} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Error updating assignment.';
}

$stmt->close();
$conn->close();

// Redirect back to the manage assignments page
header("Location: pages-assign-subject-manage.php");
exit();
?>
