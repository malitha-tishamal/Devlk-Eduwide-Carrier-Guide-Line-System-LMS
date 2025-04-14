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

// Fetch student marks for Semester III
$semester = 'Semester II';
$sql_marks = "SELECT * FROM marks WHERE student_id = ? AND semester = ?";
$stmt_marks = $conn->prepare($sql_marks);
$stmt_marks->bind_param("is", $reg_id, $semester); // Binding reg_id and semester
$stmt_marks->execute();
$marks_result = $stmt_marks->get_result();
$stmt_marks->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Semester II - EduWide</title>
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
    </style>
</head>
<body>

    <?php include_once("includes/header.php") ?>
    <?php include_once("includes/students-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="marks-container">
                            <h3 class="text-primary">Marks Semester II</h3>
                            <!-- Displaying the student's name and reg_id -->
                            <h2 class="card-title mb-4 d-flex">Hello :&nbsp;&nbsp; <div class="text-success"><?php echo $student_name; ?> &nbsp;&nbsp;&nbsp; (Reg ID: <?php echo $reg_id; ?>)</div></h2>
                            <div class="marks-title">
                                <div class="marks-item title">Subject Name</div>
                                <div class="marks-item title">Practical Marks</div>
                                <div class="marks-item title">Paper Marks</div>
                                <div class="marks-item title">Special Notes</div>
                            </div>

                            <?php
                            if ($marks_result->num_rows > 0) {
                                while ($row = $marks_result->fetch_assoc()) {
                                    // Set font color based on practical marks
                                    $practical_marks = $row['practical_marks'];
                                    $paper_marks = $row['paper_marks'];

                                    // Practical Marks font color logic
                                    if ($practical_marks >= 90) {
                                        $practical_text_color = 'green';
                                    } elseif ($practical_marks >= 75) {
                                        $practical_text_color = 'darkblue';
                                    } elseif ($practical_marks >= 65) {
                                        $practical_text_color = 'blue';
                                    } elseif ($practical_marks >= 35) {
                                        $practical_text_color = 'orange';
                                    } else {
                                        $practical_text_color = 'red';
                                    }

                                    // Paper Marks font color logic
                                    if ($paper_marks >= 90) {
                                        $paper_text_color = 'green';
                                    } elseif ($paper_marks >= 75) {
                                        $paper_text_color = 'darkblue';
                                    } elseif ($paper_marks >= 65) {
                                        $paper_text_color = 'blue';
                                    } elseif ($paper_marks >= 35) {
                                        $paper_text_color = 'orange';
                                    } else {
                                        $paper_text_color = 'red';
                                    }

                                    echo "<div class='marks-row'>
                                            <div class='marks-item'>{$row['subject']}</div>  
                                            <div class='marks-item' style='color: $practical_text_color;'>{$practical_marks}</div>  
                                            <div class='marks-item' style='color: $paper_text_color;'>{$paper_marks}</div>  
                                            <div class='marks-item'>{$row['special_notes']}</div>  
                                          </div>";
                                }
                            } else {
                                echo "<div class='marks-row'>
                                        <div class='marks-item' colspan='4'>No marks available for Semester III.</div>
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="row">
                <div class="col-lg-12">
                    <div class="card">
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
        </div>
    </main>

    <?php include_once("includes/footer4.php") ?>
    <?php include_once("includes/js-links-inc.php") ?>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
