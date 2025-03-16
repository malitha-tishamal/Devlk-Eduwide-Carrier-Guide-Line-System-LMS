<?php
session_start();
require_once '../includes/db-conn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecting data from the form
    $year = $_POST['year'] ?? '';
    $student_id = $_POST['studentId'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $semester = $_POST['semestersubject'] ?? '';
    $student_id = $_POST['studentId'] ?? '';
    $practical_marks = $_POST['practicalMarks'] ?? '';
    $paper_marks = $_POST['paperMarks'] ?? '';
    $special_notes = $_POST['specialnotes'] ?? '';

    // Validate input: Ensure no required fields are empty
    if (empty($year) || empty($student_id) || empty($subject) || empty($semester) || $practical_marks === '' || $paper_marks === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields are required.';
        header("Location: marks-entry.php?subject_id=" . urlencode($_POST['subject_id']));
        exit();
    }

    // Validate marks: Ensure marks are between 0 and 100
    if ($practical_marks < 0 || $practical_marks > 100 || $paper_marks < 0 || $paper_marks > 100) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Marks should be between 0 and 100.';
        header("Location: marks-entry.php?subject_id=" . urlencode($_POST['subject_id']));
        exit();
    }

    // Check if the student already has marks for this subject and semester
    $check_sql = "SELECT id FROM marks WHERE student_id = ? AND subject = ? AND semester = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("sss", $student_id, $subject, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Marks already exist for this student in this subject and semester
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Marks for this student in this subject and semester already exist.';
        header("Location: marks-entry.php?subject_id=" . urlencode($_POST['subject_id']));
        exit();
    }

    $stmt->close();

    // Insert marks into the database
    $insert_sql = "INSERT INTO marks (student_id, year, subject, semester, practical_marks, paper_marks, special_notes) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sssssss", $student_id, $year, $subject, $semester, $practical_marks, $paper_marks, $special_notes);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Success: Marks have been entered
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Marks successfully entered.';
    } else {
        // Failure: There was an error while entering the marks
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error entering marks. Please try again.';
    }

    // Close the statement
    $stmt->close();

    // Redirect back to the marks-entry page with the subject_id
    header("Location: marks-entry.php?subject_id=" . urlencode($_POST['subject_id']));
    exit();
} else {
    // If the request is not POST, redirect to the index page
    header("Location: ../index.php");
    exit();
}
?>
