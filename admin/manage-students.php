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

// Fetch filtering parameters from GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$study_year = isset($_GET['study_year']) ? $_GET['study_year'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';

// Fetch all courses for the dropdown
$coursesResult = $conn->query("SELECT id, name FROM hnd_courses ORDER BY name ASC");

// Build SQL query with optional filters
$sql2 = "SELECT s.*, hc.name AS course_name
         FROM students s
         LEFT JOIN hnd_courses hc ON s.course_id = hc.id
         WHERE 1";

if ($search !== '') {
    $sql2 .= " AND (s.username LIKE '%$search%' OR s.reg_id LIKE '%$search%')";
}
if ($study_year !== '') {
    $sql2 .= " AND s.study_year = '$study_year'";
}
if ($status !== '') {
    $sql2 .= " AND s.status = '$status'";
}
if ($course_id !== '') {
    $sql2 .= " AND s.course_id = '$course_id'";
}

// Execute query
$result = $conn->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Active Students Manage - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>
    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Active Students</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Manage Active Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Active Students Management</h5>
                            <p>Manage Active Students here.</p>

                            <!-- Filters Form -->
                            <form method="GET" action="">
                                <div class="row mb-3">
                                    <!-- Search -->
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" placeholder="Search by Name or Reg ID" value="<?php echo htmlspecialchars($search); ?>">
                                    </div>

                                    <!-- Study Year -->
                                    <div class="col-md-2">
                                        <select name="study_year" class="form-select">
                                            <option value="">All Years</option>
                                            <?php
                                            $current_year = date("Y");
                                            for ($year = 2020; $year <= $current_year + 2; $year++) {
                                                $selected = ($study_year == "$year") ? 'selected' : '';
                                                echo "<option value='$year' $selected>Year $year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-2">
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="active" <?php echo ($status == "active" ? 'selected' : ''); ?>>Active</option>
                                            <option value="pending" <?php echo ($status == "pending" ? 'selected' : ''); ?>>Pending</option>
                                            <option value="disabled" <?php echo ($status == "disabled" ? 'selected' : ''); ?>>Disabled</option>
                                        </select>
                                    </div>

                                    <!-- Course -->
                                    <div class="col-md-3">
                                        <select name="course_id" class="form-select">
                                            <option value="">All Courses</option>
                                            <?php
                                            if ($coursesResult->num_rows > 0) {
                                                while ($course = $coursesResult->fetch_assoc()) {
                                                    $selected = ($course_id == $course['id']) ? 'selected' : '';
                                                    echo "<option value='{$course['id']}' $selected>{$course['name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Table -->
                            <table class="table datatable">
                                <thead class="align-middle text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile Picture</th>
                                        <th>Username</th>
                                        <th>Reg ID</th>
                                        <th>NIC</th>
                                        <th>Study Year</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Approve</th>
                                        <th>Disable</th>
                                        <th>Delete</th>
                                        <th>Edit</th>
                                        <th>Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td><img src='../" . htmlspecialchars($row["profile_picture"]) . "' alt='Profile' width='120'></td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reg_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nic']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['study_year']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";

                                            // Status Column
                                            $statusVal = strtolower($row['status']);
                                            if ($statusVal === 'active') {
                                                echo "<td><span class='btn btn-success btn-sm w-100'>Active</span></td>";
                                            } elseif ($statusVal === 'disabled') {
                                                echo "<td><span class='btn btn-danger btn-sm w-100'>Disabled</span></td>";
                                            } elseif ($statusVal === 'pending') {
                                                echo "<td><span class='btn btn-warning btn-sm w-100'>Pending</span></td>";
                                            } else {
                                                echo "<td><span class='btn btn-secondary btn-sm w-100'>" . ucfirst($row['status']) . "</span></td>";
                                            }

                                            // Action Buttons
                                            echo "<td class='text-center'><button class='btn btn-success btn-sm w-100 approve-btn' data-id='" . $row['id'] . "'>Approve</button></td>";
                                            echo "<td class='text-center'><button class='btn btn-warning btn-sm w-100 disable-btn' data-id='" . $row['id'] . "'>Disable</button></td>";
                                            echo "<td class='text-center'><button class='btn btn-danger btn-sm w-100 delete-btn' data-id='" . $row['id'] . "'>Delete</button></td>";
                                            echo "<td class='text-center'><a href='edit-student.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm w-100'>Edit</a></td>";
                                            echo "<td class='text-center'><a href='student-profile.php?student_id=" . $row['id'] . "' class='btn btn-primary btn-sm w-100'>Profile</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='15' class='text-center'>No students found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- End Table -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php include_once("../includes/js-links-inc.php") ?>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const approveButtons = document.querySelectorAll('.approve-btn');
        const disableButtons = document.querySelectorAll('.disable-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-students.php?approve_id=${userId}`;
            });
        });

        disableButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-students.php?disable_id=${userId}`;
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this student?")) {
                    window.location.href = `process-students.php?delete_id=${userId}`;
                }
            });
        });
      });
    </script>
</body>
</html>

<?php $conn->close(); ?>
