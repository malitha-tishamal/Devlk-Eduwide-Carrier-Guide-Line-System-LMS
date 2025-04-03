<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['former_student_id'];
    $summary = trim($_POST['summary']); 

    $sql = "SELECT id FROM summaries WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $update_sql = "UPDATE summaries SET summary = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $summary, $user_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Summary updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed."]);
        }
    } else {
        $stmt->close();
        $insert_sql = "INSERT INTO summaries (user_id, summary) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("is", $user_id, $summary);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Summary added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database insert failed."]);
        }
    }
    $stmt->close();
}
$conn->close();
?>
