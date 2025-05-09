<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['student_id'];

if (isset($_GET['id'])) {
    $certification_id = $_GET['id'];

    // Fetch certification to delete image file
    $stmt = $conn->prepare("SELECT image_path FROM students_certifications WHERE id = ? AND student_id = ?");
    $stmt->bind_param("ii", $certification_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cert = $result->fetch_assoc();
    $stmt->close();

    if ($cert) {
        // Delete image file if exists
        if (!empty($cert['image_path']) && file_exists($cert['image_path'])) {
            unlink($cert['image_path']);
        }

        // Delete the record from DB
        $stmt = $conn->prepare("DELETE FROM students_certifications WHERE id = ? AND student_id = ?");
        $stmt->bind_param("ii", $certification_id, $current_user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: pages-Certification.php");
exit();
