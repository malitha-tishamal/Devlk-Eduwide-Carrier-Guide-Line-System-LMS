<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Fetch experience data from the database
$query = "SELECT * FROM experiences WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Experience</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="section-header">Work Experience</h2>

    <!-- Experience List -->
    <div id="experience-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $currently_working = $row['currently_working'] ? 'Currently Working' : 'Ended';
                $end_date = $row['currently_working'] ? 'Present' : $row['end_month'] . ' ' . $row['end_year'];

                echo '
                <div class="experience-card mb-4">
                    <div class="experience-header">
                        <h4 class="experience-title">' . $row['title'] . '</h4>
                        <span class="experience-company">' . $row['company'] . '</span>
                    </div>
                    <div class="experience-details">
                        <span class="experience-location">' . $row['location'] . '</span>
                        <span class="experience-dates">' . $row['start_month'] . ' ' . $row['start_year'] . ' - ' . $end_date . '</span>
                    </div>
                    <div class="experience-description">
                        <p>' . nl2br($row['description']) . '</p>
                    </div>
                    <div class="experience-footer">
                        <span class="experience-type">' . $row['employment_type'] . '</span>
                        <span class="experience-source">Source: ' . $row['job_source'] . '</span>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No work experience added yet.</p>';
        }

        $stmt->close();
        ?>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
