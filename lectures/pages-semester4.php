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
$sql = "SELECT * FROM lectures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


$sql = "SELECT subjects.* FROM subjects
        INNER JOIN lectures_assignment ON subjects.id = lectures_assignment.subject_id
        WHERE lectures_assignment.lecturer_id = ? AND subjects.semester = 'Semester IV'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Semester II - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            background-color: #e6e6ff;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .card a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
        .card a:hover {
            color: #007bff;
        }
    </style>
</head>

<body>

    <?php include_once("../includes/header.php") ?>

    <?php include_once("../includes/lectures-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Semester IV</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">Semesters</a></li>
                    <li class="breadcrumb-item"><a href="">Semester IV</a></li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <?php
                if ($result->num_rows > 0) {
                    // Output each subject with a link to marks entry and edit option
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-6 mb-3">
                                <div class="card p-3">
                                    <h5>' . $row['code'] . ' - <a href="marks-entry.php?subject_id=' . $row['id'] . '">' . $row['name'] . '</a></h5>
                                    <p class="text-muted">' . $row['description'] . '</p>
                                </div>
                              </div>';
                    }
                } else {
                    echo "No subjects found";
                }
                ?>
            </div>
            <a href="pages-semester3.php" class="btn btn-primary mt-3">Back: Semester III</a>  
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once("../includes/js-links-inc.php") ?>

</body>

</html>

<?php
// Close database connection
$conn->close();
?>
