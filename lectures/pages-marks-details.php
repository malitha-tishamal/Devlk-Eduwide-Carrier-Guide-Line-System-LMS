<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['lecturer_id'];
$sql = "SELECT username, email, nic, mobile, profile_picture FROM lectures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


$year_filter = isset($_POST['year']) ? $_POST['year'] : '';
$semester_filter = isset($_POST['semester']) ? $_POST['semester'] : '';


$sql = "SELECT * FROM marks WHERE entered_by_id = ? AND entered_by_role = 'lecturer'";
$param_types = "i";
$params = [$user_id];

if (!empty($year_filter)) {
    $sql .= " AND year = ?";
    $param_types .= "s";
    $params[] = $year_filter;
}
if (!empty($semester_filter)) {
    $sql .= " AND semester = ?";
    $param_types .= "s";
    $params[] = $semester_filter;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Marks Details - EduWide</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>
<body>

<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/lectures-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Marks Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Marks</li>
                <li class="breadcrumb-item active">Marks Details</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" class="mt-3 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <label for="year">Select Batch Year</label>
                                    <select class="form-select" id="year" name="year" required>
                                        <option value="">Select Batch Year</option>
                                        <?php
                                        $year_sql = "SELECT DISTINCT study_year FROM students ORDER BY study_year DESC";
                                        $year_result = $conn->query($year_sql);
                                        if ($year_result->num_rows > 0) {
                                            while ($row = $year_result->fetch_assoc()) {
                                                $selected = ($row['study_year'] == $year_filter) ? 'selected' : '';
                                                echo "<option value='{$row['study_year']}' $selected>{$row['study_year']}</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No Data Available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="semester">Select Semester</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="Semester I" <?= $semester_filter == 'Semester I' ? 'selected' : ''; ?>>Semester I</option>
                                        <option value="Semester II" <?= $semester_filter == 'Semester II' ? 'selected' : ''; ?>>Semester II</option>
                                        <option value="Semester III" <?= $semester_filter == 'Semester III' ? 'selected' : ''; ?>>Semester III</option>
                                        <option value="Semester IV" <?= $semester_filter == 'Semester IV' ? 'selected' : ''; ?>>Semester IV</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3">Filter</button>
                        </form>

                        <table class="table datatable">
                            <thead class="align-middle text-center">
                                <tr>
                                    <th>Student</th>
                                    <th>Academic Year</th>
                                    <th>Subject</th>
                                    <th>Semester</th>
                                    <th>Practical Marks</th>
                                    <th>Paper Marks</th>
                                    <th>Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) : ?>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                                            <td><?= htmlspecialchars($row['year']) ?></td>
                                            <td><?= htmlspecialchars($row['subject']) ?></td>
                                            <td><?= htmlspecialchars($row['semester']) ?></td>
                                            <td><?= htmlspecialchars($row['practical_marks']) ?></td>
                                            <td><?= htmlspecialchars($row['paper_marks']) ?></td>
                                            <td><?= htmlspecialchars($row['special_notes']) ?></td>
                                            <td class="text-center">
                                                <a href="edit-marks.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No results found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

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