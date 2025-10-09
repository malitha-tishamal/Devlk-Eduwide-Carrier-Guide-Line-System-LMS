<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch admin details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic, mobile, profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch assigned subjects with lecturer name, profile picture, and full subject info
$query = "
    SELECT 
        la.id AS assignment_id,
        la.lecturer_id,
        la.subject_id,
        l.username,
        l.profile_picture,
        GROUP_CONCAT(s.semester ORDER BY s.code) AS semesters,
        GROUP_CONCAT(s.code ORDER BY s.code) AS subject_codes,
        GROUP_CONCAT(s.name ORDER BY s.code) AS subject_names,
        GROUP_CONCAT(s.description ORDER BY s.code) AS subject_descriptions,
        GROUP_CONCAT(s.course ORDER BY s.code) AS course_names
    FROM lectures_assignment la
    JOIN lectures l ON la.lecturer_id = l.id
    JOIN subjects s ON la.subject_id = s.id
    GROUP BY la.lecturer_id
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Assigned Subjects</title>
<?php include_once("../includes/css-links-inc.php"); ?>

<style>
.subject-list { font-size: 1rem; color: #333; line-height: 1.6; }
.subject-item { background-color: #f9f9f9; padding: 8px 10px; border-radius: 8px; margin: 6px 0; box-shadow: 0 0 4px rgba(0,0,0,0.05); }
.subject-item strong { color: #333; }
.subject-item small { color: #666; display: block; }
.lecturer-card { text-align: center; }
.lecturer-card img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-bottom: 8px; border: 2px solid #ddd; }
.table th, .table td { text-align: center; vertical-align: middle; }
</style>
</head>

<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/sadmin-sidebar.php"); ?>

<main id="main" class="main">
<div class="pagetitle">
    <h1>Manage Assigned Subjects</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item">Subjects</li>
            <li class="breadcrumb-item active">Assigned Management</li>
        </ol>
    </nav>
</div>

<section class="section">
<div class="row">
<div class="col-lg-12">
<div class="card">
<div class="card-body">
<div class="container mt-3 mb-3">

<?php if ($result->num_rows > 0): ?>
<table class="table datatable table-bordered table-striped">
    <thead class="thead-light">
        <tr>
            <th>Lecturer</th>
            <th>Assigned Subjects</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="lecturer-card">
                <img src="../lectures/<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
                <div><strong><?php echo htmlspecialchars($row['username']); ?></strong></div>
            </td>

            <td class="subject-list">
            <?php
            $semesters = explode(',', $row['semesters']);
            $subject_codes = explode(',', $row['subject_codes']);
            $subject_names = explode(',', $row['subject_names']);
            $subject_descriptions = explode(',', $row['subject_descriptions']);
            $course_names  = explode(',', $row['course_names']);

            for ($i = 0; $i < count($subject_codes); $i++) {
                echo "<div class='subject-item'>
                        <strong>" . htmlspecialchars($subject_codes[$i]) . " - " . htmlspecialchars($subject_names[$i]) . "</strong>
                        <small>Semester: " . htmlspecialchars($semesters[$i]) . "</small>
                        <small>Course: " . htmlspecialchars($course_names[$i]) . "</small>
                        <small>Description: " . htmlspecialchars($subject_descriptions[$i]) . "</small>
                      </div>";
            }
            ?>
            </td>

            <td>
                <a href="edit_assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-center text-muted">No subjects assigned to any lecturers yet.</p>
<?php endif; ?>

</div>
</div>
</div>
</div>
</div>
</section>
</main>

<?php include_once("../includes/footer.php"); ?>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>
<?php include_once("../includes/js-links-inc.php"); ?>
</body>
</html>

<?php
$conn->close();
?>
