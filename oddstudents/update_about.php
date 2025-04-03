<?php
session_start();
require_once "../includes/db-conn.php"; 

if (!isset($_SESSION['former_student_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Get data from POST
$user_id = $_SESSION['former_student_id'];
$about_text = $_POST['about'] ?? '';

if (empty($about_text)) {
    http_response_code(400);
    echo "About section cannot be empty";
    exit;
}


$sql = "INSERT INTO about (user_id, about_text)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE about_text = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Database prepare failed";
    exit;
}

$stmt->bind_param("iss", $user_id, $about_text, $about_text);

if ($stmt->execute()) {
} else {
    http_response_code(500);
    echo "Database error";
}

$stmt->close();
$conn->close();
?>
