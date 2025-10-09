<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check assignment_id
if (!isset($_GET['assignment_id'])) {
    echo "Assignment ID is missing!";
    exit;
}
$assignment_id = $_GET['assignment_id'];

// Fetch assignment details
$query = "SELECT la.id as assignment_id, la.lecturer_id, la.subject_id, l.username, l.profile_picture, s.code as subject_code, s.name as subject_name, s.course
          FROM lectures_assignment la
          JOIN lectures l ON la.lecturer_id = l.id
          JOIN subjects s ON la.subject_id = s.id
          WHERE la.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Assignment not found!";
    exit;
}

$row = $result->fetch_assoc();
$lecturer_id = $row['lecturer_id'];
$subject_id = $row['subject_id'];
$current_course = $row['course']; // currently assigned subject's course
$stmt->close();

// Fetch all lecturers
$lecturers_result = $conn->query("SELECT id, username, profile_picture FROM lectures WHERE status='approved'");

// Fetch all subjects
$subjects_result = $conn->query("SELECT id, code, name, course FROM subjects ORDER BY course, code ASC");

// Fetch all unique courses for the dropdown
$courses_result = $conn->query("SELECT DISTINCT course FROM subjects ORDER BY course ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Assignment</title>
<?php include_once("../includes/css-links-inc.php"); ?>
<style>
    .lecturer-img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 10px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/sadmin-sidebar.php"); ?>

<main class="main">
<div class="pagetitle">
    <h1>Edit Assignment</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Lectures</li>
            <li class="breadcrumb-item active">Edit Assignment</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-body">
<div class="container mt-3">

<form action="update_assignment.php" method="POST">
    <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">

    <!-- Lecturer Dropdown -->
    <div class="form-group mb-3">
        <label for="lecturer">Lecturer</label>
        <select class="form-control w-50" name="lecturer_id" id="lecturer" required>
            <option value="">Select Lecturer</option>
            <?php while ($lecturer = $lecturers_result->fetch_assoc()): ?>
                <option value="<?php echo $lecturer['id']; ?>" <?php echo ($lecturer['id'] == $lecturer_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($lecturer['username']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Course Dropdown -->
    <div class="form-group mb-3">
        <label for="course">Course</label>
        <select class="form-control w-50" id="course">
            <option value="">Select Course</option>
            <?php while ($course = $courses_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($course['course']); ?>" <?php echo ($course['course'] == $current_course) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($course['course']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Subject Dropdown -->
    <div class="form-group mb-3">
        <label for="subject">Subject</label>
        <select class="form-control w-75" name="subject_id" id="subject" required>
            <option value="">Select Subject</option>
            <?php
            $subjects_result = $conn->query("SELECT id, code, name, course FROM subjects ORDER BY course, code ASC");
            while ($subject = $subjects_result->fetch_assoc()):
            ?>
                <option value="<?php echo $subject['id']; ?>" data-course="<?php echo htmlspecialchars($subject['course']); ?>" <?php echo ($subject['id'] == $subject_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($subject['code']) . " - " . htmlspecialchars($subject['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Update Assignment</button>
    <a href="pages-assign-subject-manage.php" class="btn btn-secondary mt-3">Cancel</a>
</form>

</div>
</div>
</div>
</div>
</div>
</section>
</main>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>

<script>
$(document).ready(function(){
    // Filter subjects based on selected course
    $("#course").change(function(){
        var selectedCourse = $(this).val();
        $("#subject option").each(function(){
            var course = $(this).data("course");
            if(selectedCourse === "" || course === selectedCourse){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        // Reset selection
        $("#subject").val('');
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>
