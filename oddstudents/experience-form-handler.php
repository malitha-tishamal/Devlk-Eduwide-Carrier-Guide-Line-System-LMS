<?php
// Database connection
require_once "../includes/db-conn.php";

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data sent from the front-end (modal form)
    $title = $_POST['title'] ?? '';
    $employment_type = $_POST['employment_type'] ?? '';
    $company = $_POST['company'] ?? '';
    $currently_working = isset($_POST['currently_working']) ? 1 : 0;
    $start_month = $_POST['start_month'] ?? '';
    $start_year = $_POST['start_year'] ?? '';
    $end_month = $_POST['end_month'] ?? '';
    $end_year = $_POST['end_year'] ?? '';
    $location = $_POST['location'] ?? '';
    $location_type = $_POST['location_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $job_source = $_POST['job_source'] ?? '';
    
    // Handle the skills (multiple skills from the form)
    $skills = $_POST['skills'] ?? [];

    // Validation
    if (empty($title) || empty($company) || empty($start_month) || empty($start_year)) {
        echo json_encode(['success' => false, 'message' => 'Title, company, start month, and start year are required.']);
        exit();
    }

    // Convert start and end dates to proper format
    $start_date = $start_year . '-' . date('m', strtotime($start_month)) . '-01'; // Use first day of the month
    $end_date = ($end_year && $end_month) ? $end_year . '-' . date('m', strtotime($end_month)) . '-01' : null;

    // Insert data into the database
    try {
        // Begin a transaction for safe inserts
        $conn->beginTransaction();

        // Insert the work experience
        $stmt = $conn->prepare("INSERT INTO work_experiences (user_id, title, employment_type, company, currently_working, start_date, end_date, location, location_type, description, job_source) VALUES (:user_id, :title, :employment_type, :company, :currently_working, :start_date, :end_date, :location, :location_type, :description, :job_source)");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'], // Assuming you have user_id in session
            ':title' => $title,
            ':employment_type' => $employment_type,
            ':company' => $company,
            ':currently_working' => $currently_working,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':location' => $location,
            ':location_type' => $location_type,
            ':description' => $description,
            ':job_source' => $job_source
        ]);

        // Get the last inserted work experience ID
        $experience_id = $conn->lastInsertId();

        // Insert skills if provided
        if (!empty($skills)) {
            $stmt_skills = $conn->prepare("INSERT INTO experience_skills (experience_id, skill_name) VALUES (:experience_id, :skill_name)");
            foreach ($skills as $skill) {
                $stmt_skills->execute([
                    ':experience_id' => $experience_id,
                    ':skill_name' => $skill
                ]);
            }
        }

        // Commit transaction
        $conn->commit();

        // Respond back with success message
        echo json_encode(['success' => true, 'message' => 'Work experience added successfully!']);
    } catch (PDOException $e) {
        // Rollback if any error occurs
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
