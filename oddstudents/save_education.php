<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and assign form data to variables
    $school = $_POST['school'];
    $degree = $_POST['degree'];
    $field = $_POST['field'];
    $start_month = $_POST['start_month'];
    $start_year = $_POST['start_year'];
    $end_month = empty($_POST['end_month']) ? NULL : $_POST['end_month'];  // If end_month is empty, set it as NULL
    $end_year = empty($_POST['end_year']) ? NULL : $_POST['end_year'];    // If end_year is empty, set it as NULL
    $grade = $_POST['grade'];
    $activities = $_POST['activities'];
    $description = $_POST['description'];

    // Prepare and insert into the database
    $query = "INSERT INTO education 
              (user_id, school, degree, field_of_study, start_month, start_year, end_month, end_year, grade, activities, description) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare the statement and bind parameters
    $stmt = $conn->prepare($query);
    if ($end_month === NULL && $end_year === NULL) {
        $stmt->bind_param('issssssssss', $user_id, $school, $degree, $field, $start_month, $start_year, $end_month, $end_year, $grade, $activities, $description);
    } else {
        $stmt->bind_param('issssssssss', $user_id, $school, $degree, $field, $start_month, $start_year, $end_month, $end_year, $grade, $activities, $description);
    }

    // Execute the query
if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'Education added successfully',
        
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to add education',
        'redirect' => 'pages-your-path.php' // specify the page you want to redirect to
    ]);
}

$stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

?>
