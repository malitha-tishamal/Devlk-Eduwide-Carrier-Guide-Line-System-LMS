<?php
session_start();
require_once "../includes/db-conn.php"; // Ensure this path is correct

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Fetch work experience data from the database for the logged-in user
$user_id = $_SESSION['former_student_id'];
$sql = "SELECT * FROM work_experience WHERE user_id = '$user_id' ORDER BY start_date DESC";
$result = $conn->query($sql);

// Check if there are any work experiences and return them
$experiences = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $experiences[] = $row;
    }
}

// Return the fetched data as a JSON response
echo json_encode($experiences);

$conn->close();
?>
