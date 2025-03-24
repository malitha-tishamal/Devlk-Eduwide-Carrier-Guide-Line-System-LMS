<?php
session_start();
require_once "../includes/db-conn.php"; // Update the path if needed

if (!isset($_SESSION['former_student_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['former_student_id'];

// Get data from the POST request
$skill_name = $_POST['skill'];
$institution = $_POST['institution'];

// Check if both skill and institution are not empty
if (empty($skill_name) || empty($institution)) {
    echo json_encode(["error" => "Skill and institution are required"]);
    exit;
}

try {
    // Insert skill into the database
    $sql = "INSERT INTO skills (user_id, skill_name, institution) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $skill_name, $institution);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Skill added successfully"]);
    } else {
        throw new Exception("Failed to add skill: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}
?>
