<?php
session_start();
require_once "../includes/db-conn.php"; // Update path if needed

if (!isset($_SESSION['former_student_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['former_student_id'];

$sql = "SELECT id, skill_name, institution FROM skills WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$skills = [];

while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}

header('Content-Type: application/json');
echo json_encode($skills);

$stmt->close();
$conn->close();
?>
