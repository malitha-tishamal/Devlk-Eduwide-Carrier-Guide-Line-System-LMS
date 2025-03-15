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

// Build the SQL query with filters
$sql2 = "SELECT * FROM former_students WHERE nowstatus = 'study'";

// Apply search filter if provided
if ($search !== '') {
    $sql2 .= " AND (username LIKE '%$search%' OR reg_id LIKE '%$search%')";
}

// Apply study year filter if provided
if ($study_year !== '') {
    $sql2 .= " AND study_year = '$study_year'";
}

// Apply status filter if provided
if ($status !== '') {
    $sql2 .= " AND status = '$status'";
}

// Execute the query with the applied filters
$result = $conn->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Former Students Manage - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>
    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Former Students (Still Study)</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Manage Former Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Former Still Study Students Management</h5>
                            <p>Manage Former Students (Still Study) here.</p>

                            <!-- Search Bar and Filters -->
                            <form method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" placeholder="Search by Name or Reg ID" value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="study_year" class="form-select">
                                            <option value="">All Years</option>
                                            <?php
                                            $current_year = date("Y");
                                            for ($year = 2000; $year <= $current_year + 2; $year++) {
                                                $selected = ($study_year == "Year $year") ? 'selected' : '';
                                                echo "<option value='$year' $selected>Year $year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="active" <?php echo ($status == "active" ? 'selected' : ''); ?>>Active</option>
                                            <option value="pending" <?php echo ($status == "pending" ? 'selected' : ''); ?>>Pending</option>
                                            <option value="disabled" <?php echo ($status == "disabled" ? 'selected' : ''); ?>>Disabled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Table with user data -->
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
                                        <th>Now Status</th>
                                        <th>University</th>
                                        <th>Course Name</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th colspan="13" class="text-center"></th>
                                        <th class="text-center">Approve</th>
                                        <th class="text-center">Disable</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo " <td><img src='../oddstudents/" . $row["profile_picture"] . "' alt='Profile' width='50'></td>";
                                            echo "<td>" . $row['username'] . "</td>";
                                            echo "<td>" . $row['reg_id'] . "</td>";
                                            echo "<td>" . $row['nic'] . "</td>";
                                            echo "<td>" . $row['study_year'] . "</td>";
                                            echo "<td>" . $row['email'] . "</td>";
                                            echo "<td>" . $row['mobile'] . "</td>";
                                            echo "<td>" . $row['nowstatus'] . "</td>";
                                            echo "<td>" . $row['university'] . "</td>";
                                            echo "<td>" . $row['course_name'] . "</td>";
                                            echo "<td>" . $row['country'] . "</td>";

                                            // Status Column with Color
                                            echo "<td>";
                                            $status = strtolower($row['status']);
                                            if ($status === 'active' || $status === 'approved') {
                                                echo "<span class='btn btn-success btn-sm w-100 text-center'>Approved</span>";
                                            } elseif ($status === 'disabled') {
                                                echo "<span class='btn btn-danger btn-sm w-100 text-center'>Disabled</span>";
                                            } elseif ($status === 'pending') {
                                                echo "<span class='btn btn-warning btn-sm w-100 text-center'>Pending</span>";
                                            } else {
                                                echo "<span class='btn btn-secondary btn-sm w-100 text-center'>" . ucfirst($row['status']) . "</span>";
                                            }
                                            echo "</td>";

                                            // Action Buttons
                                            echo "<td class='text-center'>
                                                    <button class='btn btn-success btn-sm w-100 approve-btn' data-id='" . $row['id'] . "'>Approve</button>
                                                  </td>";
                                            echo "<td class='text-center'>
                                                    <button class='btn btn-warning btn-sm w-100 disable-btn' data-id='" . $row['id'] . "'>Disable</button>
                                                  </td>";
                                            echo "<td class='text-center'>
                                                    <button class='btn btn-danger btn-sm w-100 delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9' class='text-center'>No users found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- End Table with user data -->

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once("../includes/js-links-inc.php") ?>
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function () {
        const approveButtons = document.querySelectorAll('.approve-btn');
        const disableButtons = document.querySelectorAll('.disable-btn');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-former-students-edu.php?approve_id=${userId}`;
            });
        });

        disableButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                window.location.href = `process-former-students-edu.php?disable_id=${userId}`;
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this user?")) {
                    window.location.href = `process-former-students-edu.php?delete_id=${userId}`;
                }
            });
        });
      });
    </script>

</body>

</html>

<?php
// Close database connection
$conn->close();
?>
