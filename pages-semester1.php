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

$semester = 'Semester I';
$sql_marks = "
    SELECT marks.*, subjects.code AS subject_code, subjects.name AS subject_name
    FROM marks
    JOIN subjects ON marks.subject = subjects.name
    WHERE marks.student_id = ? AND marks.semester = ?";
$stmt_marks = $conn->prepare($sql_marks);
$stmt_marks->bind_param("ss", $reg_id, $semester); // Fixed here
$stmt_marks->execute();
$marks_result = $stmt_marks->get_result();
$stmt_marks->close();

// Resource array for suggestions based on subject code
$resources = [
    "HNDIT1012" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=Visual+Application+Programming+HNDIT"
    ],
    "HNDIT1022" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=Web+Design+HNDIT"
    ],
    "HNDIT1032" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=Computer+and+Network+Systems+HNDIT"
    ],
    "HNDIT1042" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=Information+Management+and+Information+Systems+HNDIT"
    ],
    "HNDIT1052" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=ICT+Project+Individual+HNDIT"
    ],
    "HNDIT1062" => [
        "notes" => "https://hnditmaterial.blogspot.com/2022/06/hndit-1st-year-1st-semester-lecture.html",
        "past_papers" => "https://hnditmaterial.blogspot.com/2021/11/hndit-past-paper-1st-year-1st-semester.html",
        "youtube" => "https://www.youtube.com/results?search_query=Communication+Skills+HNDIT"
    ],
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Semester I - EduWide</title>
    <?php include_once("includes/css-links-inc.php"); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

        .resource-links {
            padding: 5px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        .resource-links a {
            margin-right: 10px;
        }

        /* Styling for subjects with final marks less than 50 */
        .low-marks {
            background-color: #ffcccc;
            border: 2px solid red;
        }

        .icon {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        /* Natural icon colors */
        .youtube-icon {
            color: red;
        }

        .notes-icon {
            color: blue;
        }

        .paper-icon {
            color: green;
        }

        .icon:hover {
            opacity: 0.8;
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
                            <h3 class="text-primary">Marks Semester I</h3>
                            <h2 class="card-title mb-4 d-flex">Hello :&nbsp;&nbsp; <div class="text-success"><?php echo $student_name; ?> &nbsp;&nbsp;&nbsp; (Reg ID: <?php echo $reg_id; ?>)</div></h2>
                            <div class="marks-title">
                                <div class="marks-item title">Subject Name</div>
                                <div class="marks-item title">Practical Marks</div>
                                <div class="marks-item title">Paper Marks</div>
                                <div class="marks-item title">Final Marks</div>
                                <div class="marks-item title">Resources</div> <!-- Added Resources Column -->
                            </div>

                            <?php
                            if ($marks_result->num_rows > 0) {
                                while ($row = $marks_result->fetch_assoc()) {
                                    // Fetch subject details from the join
                                    $subject_name = $row['subject_name'];
                                    $subject_code = $row['subject_code'];

                                    // Fetch practical and paper marks
                                    $practical_marks = $row['practical_marks'];
                                    $paper_marks = $row['paper_marks'];

                                    // Final marks calculation: 60% paper marks + 40% practical marks
                                    $final_mark = ($paper_marks * 0.60) + ($practical_marks * 0.40);

                                    // Set font color based on practical marks
                                    $practical_text_color = ($practical_marks >= 90) ? 'green' :
                                                            (($practical_marks >= 75) ? 'darkblue' :
                                                            (($practical_marks >= 65) ? 'blue' :
                                                            (($practical_marks >= 35) ? 'orange' : 'red')));

                                    // Set font color based on paper marks
                                    $paper_text_color = ($paper_marks >= 90) ? 'green' :
                                                        (($paper_marks >= 75) ? 'darkblue' :
                                                        (($paper_marks >= 65) ? 'blue' :
                                                        (($paper_marks >= 35) ? 'orange' : 'red')));

                                    // Set color for final mark
                                    $final_text_color = ($final_mark > 40) ? 'green' : 'red';

                                    // Check if the final marks are below 50, and add the "low-marks" class for styling
                                    $low_marks_class = ($final_mark < 40) ? 'low-marks' : '';

                                    echo "<div class='marks-row $low_marks_class'>
                                        <div class='marks-item'>$subject_name</div>
                                        <div class='marks-item' style='color: $practical_text_color;'>$practical_marks</div>
                                        <div class='marks-item' style='color: $paper_text_color;'>$paper_marks</div>
                                        <div class='marks-item' style='color: $final_text_color;'>$final_mark</div>
                                        <div class='marks-item'>
                                            <div class='resource-links'>";
                                    
                                    if ($final_mark < 40 && isset($resources[$subject_code])) {
                                        echo "
                                         <a href='" . $resources[$subject_code]['past_papers'] . "' target='_blank' class='paper-icon'>
                                                <i class='fas fa-file icon'></i> Past Papers</a><br/>
                                        <a href='" . $resources[$subject_code]['youtube'] . "' target='_blank' class='youtube-icon'>
                                                <i class='fab fa-youtube icon'></i> YouTube</a><br/>
                                                <a href='" . $resources[$subject_code]['notes'] . "' target='_blank' class='notes-icon'>
                                                <i class='fas fa-book icon'></i> Notes</a>
                                                ";
                                    }
                                    
                                    echo "</div></div></div>";
                                }
                            } else {
                                echo "<div class='marks-row'>No marks found</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="card p-2">
                        <b>Paper & Practicle Marks</b>
                        <div class="d-flex m-4">
                            <div>
                                <div class="bg-success p-2  text-white">Status Verry Good</div>
                            </div>
                            &nbsp;&nbsp;
                            <div>
                                <div class="bg-primary p-2 text-white">Status Good</div>
                            </div>
                            &nbsp;&nbsp;
                            <div>
                                <div class="bg-info p-2 text-white">Status Normal</div>
                            </div>
                            &nbsp;&nbsp;
                            <div>
                                <div class="bg-warning p-2 text-white">Status Week</div>
                            </div>
                            &nbsp;&nbsp;
                            <div>
                                <div class="bg-danger p-2 text-white">Status Fail</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card p-2">
                        <b>Final Marks</b>
                        <div class="d-flex m-4">
                            <div>
                                <div class="bg-success p-2  text-white">Good Pass</div>
                            </div>
                            &nbsp;&nbsp;
                            <div>
                                <div class="bg-danger p-2 text-white">Status Fail (Please Refer Resourses !)</div>
                            </div>
                        </div>
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
