<?php
session_start();
require_once 'includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$reg_id = $user['reg_id']; // Get student's registration ID
$student_name = $user['username']; // Get student's name

// Fetch student marks for all semesters
$semesters = ['Semester I', 'Semester II', 'Semester III', 'Semester IV'];
$marks_results = [];

foreach ($semesters as $semester) {
    $sql_marks = "SELECT * FROM marks WHERE student_id = ? AND semester = ?";
    $stmt_marks = $conn->prepare($sql_marks);
    $stmt_marks->bind_param("is", $reg_id, $semester);
    $stmt_marks->execute();
    $marks_results[$semester] = $stmt_marks->get_result();
    $stmt_marks->close();
}

// Function to determine the color and status based on final marks
function getFinalMarksStatus($final_marks) {
    if ($final_marks > 90) {
        return ['color' => 'gold', 'status' => 'Destination (Gold)', 'range' => 'Marks Range 90+ '];
    } elseif ($final_marks >= 75) {
        return ['color' => 'green', 'status' => 'Very Good', 'range' => 'Marks Range 75-90'];
    } elseif ($final_marks >= 65) {
        return ['color' => 'darkblue', 'status' => 'Good', 'range' => 'Marks Range 65-75'];
    } elseif ($final_marks >= 55) {
        return ['color' => 'blue', 'status' => 'Normal', 'range' => 'Marks Range 55-65'];
    } elseif ($final_marks >= 35) {
        return ['color' => 'orange', 'status' => 'Need Improvement', 'range' => 'Marks Range 35-55 '];
    } else {
        return ['color' => 'red', 'status' => 'Warning (Repeat) ', 'range' => 'Below 35 = Warning'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Marks Overview - EduWide</title>
    <?php include_once("includes/css-links-inc.php"); ?>
    <style type="text/css">
        .marks-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .marks-title, .marks-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .marks-item {
            flex: 1;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .title {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }

        .marks-row:nth-child(even) {
            background-color: #f1f1f1;
        }

        .marks-item {
            text-align: center;
        }

        .status {
            padding: 5px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .status-bar {
            height: 10px;
            width: 100%;
            border-radius: 5px;
        }

        .status-description {
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <?php include_once("includes/header.php") ?>
    <?php include_once("includes/students-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle"></div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="marks-container">
                            <h3 class="text-primary">Marks Overview</h3>
                            <h2 class="card-title mb-4 d-flex">Hello :&nbsp;&nbsp; <div class="text-success"><?php echo $student_name; ?> &nbsp;&nbsp;&nbsp; (Reg ID: <?php echo $reg_id; ?>)</div></h2>
                            
                            <?php

                            foreach ($semesters as $semester) {
                                echo "<br><h4 class='text-info'>Marks for $semester</h4>";
                                echo "<div class='marks-title'>
                                        <div class='marks-item title'>Subject Name</div>
                                        <div class='marks-item title'>Practical Marks</div>
                                        <div class='marks-item title'>Paper Marks</div>
                                        <div class='marks-item title'>Final Marks</div>
                                                                                <div class='marks-item title'>Special Notes</div>
                                        <div class='marks-item title'>Status</div>
                                      </div>";

                                $marks_result =$marks_results[$semester];
                                if ($marks_result->num_rows > 0) {
                                    while ($row = $marks_result->fetch_assoc()) {
                                        $practical_marks = $row['practical_marks'];
                                        $paper_marks = $row['paper_marks'];


                                        $final_marks = ($practical_marks * 0.4) + ($paper_marks * 0.6);

                                        $status_info = getFinalMarksStatus($final_marks);
                                        $final_marks_color = $status_info['color'];
                                        $status = $status_info['status'];
                                        $status_range = $status_info['range'];

                                        $practical_text_color = ($practical_marks >= 90) ? 'green' :
                                                                (($practical_marks >= 75) ? 'darkblue' :
                                                                (($practical_marks >= 65) ? 'blue' :
                                                                (($practical_marks >= 35) ? 'orange' : 'red')));

                                        $paper_text_color = ($paper_marks >= 90) ? 'green' :
                                                            (($paper_marks >= 75) ? 'darkblue' :
                                                            (($paper_marks >= 65) ? 'blue' :
                                                            (($paper_marks >= 35) ? 'orange' : 'red')));

                                        echo "<div class='marks-row'>
                                                <div class='marks-item'>{$row['subject']}</div>  
                                                <div class='marks-item' style='color: $practical_text_color;'>{$practical_marks}</div>  
                                                <div class='marks-item' style='color: $paper_text_color;'>{$paper_marks}</div>  
                                                <div class='marks-item' style='color: $final_marks_color;'><b>{$final_marks}</b></div> 
                                                <div class='marks-item'>{$row['special_notes']}</div>  
                                                <div class='marks-item'>
                                                    <div class='status' style='background-color: $final_marks_color;'>$status</div>
                                                    <div class='status-description'>$status_range</div>
                                                </div>
                                              </div>";
                                    }
                                } else {
                                    echo "<div class='marks-row'>
                                            <div class='marks-item' colspan='6'>No marks available for $semester.</div>
                                          </div>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include_once("includes/footer4.php") ?>
    <?php include_once("includes/js-links-inc.php") ?>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
