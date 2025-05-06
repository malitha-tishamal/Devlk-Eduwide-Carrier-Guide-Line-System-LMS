<?php
session_start();
require_once 'includes/db-conn.php';

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

$reg_id = $user['reg_id'];
$student_name = $user['username'];

$semesters = ['Semester I', 'Semester II', 'Semester III', 'Semester IV'];

function getFinalMarksStatus($final_marks) {
    if ($final_marks >= 90) {
        return ['color' => 'gold', 'status' => 'Destination "A+"', 'range' => 'Marks Range 90+ '];
    } elseif ($final_marks >= 75) {
        return ['color' => 'green', 'status' => 'Very Good "A"', 'range' => 'Marks Range 75-90'];
    } elseif ($final_marks >= 65) {
        return ['color' => 'darkblue', 'status' => 'Status Good "B"', 'range' => 'Marks Range 65-75'];
    } elseif ($final_marks >= 55) {
        return ['color' => 'blue', 'status' => 'Status Normal "C"', 'range' => 'Marks Range 55-65'];
    } elseif ($final_marks >= 35) {
        return ['color' => 'orange', 'status' => 'Need Improvement "S"', 'range' => 'Marks Range 35-55 '];
    } else {
        return ['color' => 'red', 'status' => 'Warning (Repeat) "F"', 'range' => 'Below 35 = Warning'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Home - EduWide</title>
    <?php include_once("includes/css-links-inc.php"); ?>
    <style>
        .marks-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            display: inline-block;
        }

        .status-description {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
        }

        .table thead th {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .status-description {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

<?php include_once("includes/header.php") ?>
<?php include_once("includes/students-sidebar.php") ?>

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="marks-container">
                        <h3 class="text-primary">Your Education Journey</h3>
                        <h2 class="card-title d-flex">
                            Hello :&nbsp;&nbsp; 
                            <div class="text-success"><?php echo $student_name; ?> &nbsp;&nbsp;&nbsp; (Reg ID: <?php echo $reg_id; ?>)</div>
                        </h2>

                        <?php foreach ($semesters as $semester): ?>
                            <br>
                            <h4 class="text-primary"><b>Marks for <?php echo $semester; ?></b></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Subject Name</th>
                                            <th>Practical Marks</th>
                                            <th>Paper Marks</th>
                                            <th>Final Marks</th>
                                            <th>Special Notes</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql_marks = "SELECT * FROM marks WHERE student_id = ? AND semester = ?";
                                    $stmt_marks = $conn->prepare($sql_marks);
                                    $stmt_marks->bind_param("ss", $reg_id, $semester);
                                    $stmt_marks->execute();
                                    $marks_result = $stmt_marks->get_result();
                                    $stmt_marks->close();

                                    if ($marks_result->num_rows > 0):
                                        while ($row = $marks_result->fetch_assoc()):
                                            $practical = $row['practical_marks'];
                                            $paper = $row['paper_marks'];
                                            $final = ($practical * 0.4) + ($paper * 0.6);

                                            $statusInfo = getFinalMarksStatus($final);
                                            $statusColor = $statusInfo['color'];
                                            $statusText = $statusInfo['status'];
                                            $range = $statusInfo['range'];

                                            $practicalColor = ($practical >= 90) ? 'green' :
                                                              (($practical >= 75) ? 'darkblue' :
                                                              (($practical >= 65) ? 'blue' :
                                                              (($practical >= 35) ? 'orange' : 'red')));

                                            $paperColor = ($paper >= 90) ? 'green' :
                                                         (($paper >= 75) ? 'darkblue' :
                                                         (($paper >= 65) ? 'blue' :
                                                         (($paper >= 35) ? 'orange' : 'red'))); 
                                    ?>
                                        <tr>
                                            <td><?php echo $row['subject']; ?></td>
                                            <td style="color: <?php echo $practicalColor; ?>;"><?php echo $practical; ?></td>
                                            <td style="color: <?php echo $paperColor; ?>;"><?php echo $paper; ?></td>
                                            <td style="color: <?php echo $statusColor; ?>;"><b><?php echo round($final, 2); ?></b></td>
                                            <td><?php echo $row['special_notes']; ?></td>
                                            <td>
                                                <div class="status" style="background-color: <?php echo $statusColor; ?>;"><?php echo $statusText; ?></div>
                                                <div class="status-description"><?php echo $range; ?></div>
                                            </td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="6">No marks available for <?php echo $semester; ?>.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
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

<?php $conn->close(); ?>
