<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $achievement_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT image_path FROM former_students_achievements WHERE id = ?");
    $stmt->bind_param("i", $achievement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $achievement = $result->fetch_assoc();
    $image_path = $achievement['image_path'];
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM former_students_achievements WHERE id = ?");
    $stmt->bind_param("i", $achievement_id);
    $stmt->execute();
    $stmt->close();

    if (file_exists($image_path)) {
        unlink($image_path);
    }

    header("Location: pages-achievements.php");
    exit();
}
?>
