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

// Query to fetch assigned subjects with lecturer name and concatenated subject details (code and name)
$query = "SELECT la.id as assignment_id, 
                 la.lecturer_id, 
                 la.subject_id,
                 l.username, 
                 GROUP_CONCAT(s.code ORDER BY s.code) AS subject_codes, 
                 GROUP_CONCAT(s.name ORDER BY s.name) AS subject_names
          FROM lectures_assignment la
          JOIN lectures l ON la.lecturer_id = l.id
          JOIN subjects s ON la.subject_id = s.id
          GROUP BY la.id"; // Group by assignment_id to ensure each row is unique

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Subjects Manage</title>
     <?php include_once("../includes/css-links-inc.php"); ?>
         <style>
        .subject-list {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }

        .subject-item {
            background-color: #f9f9f9;
            padding: 5px;
            border-radius: 5px;
            margin: 5px 0;
        }

        .table th, .table td {
            text-align: center;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-warning {
            background-color: #ffb74d;
            border-color: #ff9800;
        }

        .btn-warning:hover {
            background-color: #ff9800;
            border-color: #fb8c00;
        }

        .btn-danger {
            background-color: #f44336;
            border-color: #d32f2f;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
            border-color: #c62828;
        }

        .btn-info {
            background-color: #03a9f4;
            border-color: #0288d1;
        }

        .btn-info:hover {
            background-color: #0288d1;
            border-color: #0277b3;
        }
    </style>
</head>
<body>
     <?php include_once("../includes/header.php") ?>

    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Lectures - Assign Subjects</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                     <li class="breadcrumb-item"><a href="index.html">Subject</a></li>
                      <li class="breadcrumb-item"><a href="index.html"> Manage Assign Subjects</a></li>
                </ol>
            </nav>
        </div>

    <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="container mt-2 mb-2">
                                
                                <?php if ($result->num_rows > 0): ?>
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Lecturer</th>
                                                <th>Subjects</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td class="subject-list">
                                                        <?php
                                                        // Split the subject codes and names into an array
                                                        $subject_codes = explode(',', $row['subject_codes']);
                                                        $subject_names = explode(',', $row['subject_names']);
                                                        
                                                        // Combine them into a formatted list
                                                        $subjects = [];
                                                        for ($i = 0; $i < count($subject_codes); $i++) {
                                                            $subjects[] = "<div class='subject-item'>" . htmlspecialchars($subject_codes[$i]) . " - " . htmlspecialchars($subject_names[$i]) . "</div>";
                                                        }
                                                        echo implode('', $subjects);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <!-- Action Buttons -->
                                                        <a href="edit_assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>" class="btn btn-warning">Edit</a>
                                                        <a href="delete_assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
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
    <?php include_once("../includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once("../includes/js-links-inc.php") ?>
</body>
</html>

<?php
$conn->close();
?>
