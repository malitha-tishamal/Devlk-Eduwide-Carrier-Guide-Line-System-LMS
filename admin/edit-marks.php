<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic,mobile,profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch the record based on ID from the query string
if (isset($_GET['id'])) {
    $mark_id = $_GET['id'];

    // Fetch the existing marks for this ID
    $sql = "SELECT * FROM marks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mark_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Record not found.';
        header("Location: marks-details.php");
        exit();
    }

    $stmt->close();
} else {
    // If ID is not present in the URL, redirect to the marks details page
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request.';
    header("Location: pages-marks-details.php");
    exit();
}

// Handle form submission to update marks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'] ?? '';
    $student_id = $_POST['studentId'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $practical_marks = $_POST['practicalMarks'] ?? '';
    $paper_marks = $_POST['paperMarks'] ?? '';
    $special_notes = $_POST['specialnotes'] ?? '';

    // Validate input
    if (empty($year) || empty($student_id) || empty($subject) || empty($semester) || $practical_marks === '' || $paper_marks === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'All fields are required.';
        header("Location: edit-marks.php?id=" . $mark_id);
        exit();
    }

    // Validate marks
    if ($practical_marks < 0 || $practical_marks > 100 || $paper_marks < 0 || $paper_marks > 100) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Marks should be between 0 and 100.';
        header("Location: edit-marks.php?id=" . $mark_id);
        exit();
    }

    // Update the marks in the database
    $update_sql = "UPDATE marks SET student_id = ?, year = ?, subject = ?, semester = ?, practical_marks = ?, paper_marks = ?, special_notes = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssi", $student_id, $year, $subject, $semester, $practical_marks, $paper_marks, $special_notes, $mark_id);

    // Execute the update query
    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Marks updated successfully.';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error updating marks. Please try again.';
    }

    $stmt->close();

    // Redirect to the marks details page
    header("Location: pages-marks-details.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edit Marks - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>
    <?php include_once("../includes/header.php"); ?>
    <?php include_once("../includes/sadmin-sidebar.php"); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Edit Marks</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="marks-details.php">Marks</a></li>
                    <li class="breadcrumb-item active">Edit Marks</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="edit-marks.php?id=<?= $mark_id ?>" method="POST">
                                <div class="mb-3 mt-3">
                                    <label for="studentId" class="form-label">Student ID</label>
                                    <input type="text" class="form-control w-50" id="studentId" name="studentId" value="<?= htmlspecialchars($row['student_id']) ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="year" class="form-label">Academic Year</label>
                                    <input type="text" class="form-control w-50" id="year" name="year" value="<?= htmlspecialchars($row['year']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control w-50" id="subject" name="subject" value="<?= htmlspecialchars($row['subject']) ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <input type="text" class="form-control w-50" id="semester" name="semester" value="<?= htmlspecialchars($row['semester']) ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="practicalMarks" class="form-label">Practical Marks</label>
                                    <input type="number" class="form-control w-50" id="practicalMarks" name="practicalMarks" value="<?= htmlspecialchars($row['practical_marks']) ?>" min="0" max="100" required>
                                    <div id="practical-error-message" style="color: red; display: none;">Please enter a valid number between 0 and 100.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="paperMarks" class="form-label">Paper Marks</label>
                                    <input type="number" class="form-control w-50" id="paperMarks" name="paperMarks" value="<?= htmlspecialchars($row['paper_marks']) ?>" min="0" max="100" required>
                                    <div id="paper-error-message" style="color: red; display: none;">Please enter a valid number between 0 and 100.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="specialnotes" class="form-label">Special Notes</label>
                                    <textarea class="form-control w-50" id="specialnotes" name="specialnotes"><?= htmlspecialchars($row['special_notes']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="pages-marks-details.php" class="btn btn-danger">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const practicalMarksInput = document.getElementById('practicalMarks');
            const paperMarksInput = document.getElementById('paperMarks');
            
            const practicalErrorMessageDiv = document.getElementById('practical-error-message');
            const paperErrorMessageDiv = document.getElementById('paper-error-message');
            practicalMarksInput.addEventListener('input', function() {
                const value = parseInt(practicalMarksInput.value, 10);

                if (value < 0 || value > 100) {
                    // Show the error message div for practical marks
                    practicalErrorMessageDiv.style.display = 'block';
                    practicalMarksInput.value = '';  
                    practicalMarksInput.style.backgroundColor = '';  
                    practicalMarksInput.style.color = '';  
                } else {
                    practicalErrorMessageDiv.style.display = 'none';

                    if (value >= 90) {
                        practicalMarksInput.style.backgroundColor = 'green';
                        practicalMarksInput.style.color = 'white';
                    } else if (value >= 75) {
                        practicalMarksInput.style.backgroundColor = 'lightgreen';
                        practicalMarksInput.style.color = 'white';
                    } else if (value >= 65) {
                        practicalMarksInput.style.backgroundColor = 'yellow';
                        practicalMarksInput.style.color = 'black';
                    } else if (value >= 35) {
                        practicalMarksInput.style.backgroundColor = 'orange';
                        practicalMarksInput.style.color = 'white';
                    } else {
                        practicalMarksInput.style.backgroundColor = 'red';
                        practicalMarksInput.style.color = 'white';
                    }
                }
            });

            paperMarksInput.addEventListener('input', function() {
                const value = parseInt(paperMarksInput.value, 10);

                if (value < 0 || value > 100) {
                    paperErrorMessageDiv.style.display = 'block';
                    paperMarksInput.value = '';  
                    paperMarksInput.style.backgroundColor = '';  
                    paperMarksInput.style.color = '';  
                } else {
                    paperErrorMessageDiv.style.display = 'none';

                    if (value >= 90) {
                        paperMarksInput.style.backgroundColor = 'green';
                        paperMarksInput.style.color = 'white';
                    } else if (value >= 75) {
                        paperMarksInput.style.backgroundColor = 'lightgreen';
                        paperMarksInput.style.color = 'white';
                    } else if (value >= 65) {
                        paperMarksInput.style.backgroundColor = 'yellow';
                        paperMarksInput.style.color = 'black';
                    } else if (value >= 35) {
                        paperMarksInput.style.backgroundColor = 'orange';
                        paperMarksInput.style.color = 'white';
                    } else {
                        paperMarksInput.style.backgroundColor = 'red';
                        paperMarksInput.style.color = 'white';
                    }
                }
            });
        });
    </script>

    <?php include_once("../includes/footer.php"); ?>
    <?php include_once("../includes/js-links-inc.php"); ?>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>
