<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['company_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['company_id'];
$sql = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get filter values
$search = isset($_GET['search']) ? $_GET['search'] : '';
$study_year = isset($_GET['study_year']) ? $_GET['study_year'] : '';
$nowstatus = isset($_GET['nowstatus']) ? $_GET['nowstatus'] : '';

// Build SQL
$sql2 = "SELECT * FROM former_students WHERE 1";

if ($search !== '') {
    $search_safe = $conn->real_escape_string($search);
    $sql2 .= " AND (username LIKE '%$search_safe%' OR reg_id LIKE '%$search_safe%')";
}
if ($study_year !== '') {
    $study_year_safe = $conn->real_escape_string($study_year);
    $sql2 .= " AND study_year = '$study_year_safe'";
}
if ($nowstatus !== '') {
    $nowstatus_safe = $conn->real_escape_string($nowstatus);
    $sql2 .= " AND nowstatus = '$nowstatus_safe'";
}

$result = $conn->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Former Students - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>
    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/company-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Former Students</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Former Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Former Students</h5>

                            <!-- Filters -->
                            <form method="GET" action="">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" placeholder="Search by Name or Reg ID" value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="study_year" class="form-select">
                                            <option value="">All Years</option>
                                            <?php
                                            $current_year = date("Y");
                                            for ($year = 2000; $year <= $current_year + 2; $year++) {
                                                $selected = ($study_year == "$year") ? 'selected' : '';
                                                echo "<option value='$year' $selected>Year $year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="nowstatus" class="form-select">
                                            <option value="">All Status</option>
                                            <?php
                                            $statusOptions = ['study', 'work', 'intern', 'free'];
                                            foreach ($statusOptions as $statusOption) {
                                                $selected = ($nowstatus === $statusOption) ? 'selected' : '';
                                                echo "<option value=\"$statusOption\" $selected>$statusOption</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Student Table -->
                            <table class="table datatable">
                                <thead class="align-middle text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile Picture</th>
                                        <th>Username</th>
                                        <th>Reg ID</th>
                                        <th>NIC</th>
                                        <th>Study Year</th>
                                        <th>Now Status</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td><img src='../oddstudents/" . htmlspecialchars($row["profile_picture"]) . "' alt='Profile' width='50'></td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['reg_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nic']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['study_year']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nowstatus']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                            echo "<td class='text-center'><a href='former_student-profile.php?former_student_id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary btn-sm w-100'>Profile</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='10' class='text-center'>No students found.</td></tr>";
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
    <?php include_once("../includes/js-links-inc.php"); ?>
</body>

</html>

<?php $conn->close(); ?>
