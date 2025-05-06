<?php
session_start();
require_once '../includes/db-conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from POST
    $year = $_POST['year'] ?? '';
    $student_id = $_POST['studentId'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $semester = $_POST['semestersubject'] ?? '';
    $practical_marks = $_POST['practicalMarks'] ?? '';
    $paper_marks = $_POST['paperMarks'] ?? '';
    $special_notes = $_POST['specialnotes'] ?? '';
    $subject_id = $_POST['subject_id'] ?? '';

    // Who is entering the marks
    $entered_by_id = null;
    $entered_by_role = null;
    if (isset($_SESSION['lecturer_id'])) {
        $entered_by_id = $_SESSION['lecturer_id'];
        $entered_by_role = 'lecturer';
    } elseif (isset($_SESSION['admin_id'])) {
        $entered_by_id = $_SESSION['admin_id'];
        $entered_by_role = 'admin';
    }

    // Validation
    if (
        empty($year) || empty($student_id) || empty($subject) ||
        empty($semester) || $practical_marks === '' || $paper_marks === ''
    ) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields are required.';
        header("Location: marks-entry.php?subject_id=" . urlencode($subject_id));
        exit();
    }

    if (
        !is_numeric($practical_marks) || !is_numeric($paper_marks) ||
        $practical_marks < 0 || $practical_marks > 100 ||
        $paper_marks < 0 || $paper_marks > 100
    ) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Marks must be between 0 and 100.';
        header("Location: marks-entry.php?subject_id=" . urlencode($subject_id));
        exit();
    }

    // Check for duplicate
    $check_sql = "SELECT id FROM marks WHERE student_id = ? AND subject = ? AND semester = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("sss", $student_id, $subject, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Marks for this student, subject, and semester already exist.';
        $stmt->close();
        header("Location: marks-entry.php?subject_id=" . urlencode($subject_id));
        exit();
    }
    $stmt->close();

    // Insert marks
    $insert_sql = "INSERT INTO marks 
        (student_id, year, subject, semester, practical_marks, paper_marks, special_notes, entered_by_id, entered_by_role) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param(
        "sssssssis",
        $student_id,
        $year,
        $subject,
        $semester,
        $practical_marks,
        $paper_marks,
        $special_notes,
        $entered_by_id,
        $entered_by_role
    );

    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Marks successfully entered.';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error saving marks. Please try again.';
    }

    $stmt->close();
    $conn->close();

    header("Location: marks-entry.php?subject_id=" . urlencode($subject_id));
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>
