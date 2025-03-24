<?php
require_once "../includes/db-conn.php"; // Correct path to your database connection

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Check if ID is provided
if (isset($data['id'])) {
    $experienceId = $data['id'];

    // Prepare SQL query to delete the work experience by ID
    $sql = "DELETE FROM work_experience WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Prepare failed
        die('MySQL prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("i", $experienceId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid ID";
}

$conn->close();
?>
