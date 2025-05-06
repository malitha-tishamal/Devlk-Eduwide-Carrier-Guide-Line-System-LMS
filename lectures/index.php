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

$query = "
    SELECT id, username, nic, email, mobile, linkedin, blog, github, facebook, profile_picture 
    FROM lectures
";
$result = mysqli_query($conn, $query);
$lecturers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch subjects for each lecturer
$subjects_query = "
    SELECT s.id, s.name, s.code
    FROM subjects s
    JOIN lectures_assignment la ON s.id = la.subject_id
    WHERE la.lecturer_id = ?
";
// Fetch all students
$query = "SELECT id, username, nic, email, mobile, linkedin, blog, github, facebook, profile_picture FROM students"; 
$result = mysqli_query($conn, $query); 
$students = mysqli_fetch_all($result, MYSQLI_ASSOC); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Home - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style type="text/css">

.card.lecturer-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    background: #fff;
    transition: transform 0.3s ease;
}

.card.lecturer-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.card-img-wrapper {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.card-img-top {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 5px solid rgba(13, 110, 253, 1);
    object-fit: cover;
}

.card-body {
    text-align: left;
}

.card-title {
    font-size: 0.9rem;
    font-weight: bold;
}

.card-text {
    font-size: 0.9rem;
    color: #555;
}

.social-links a {
    margin: 0 10px;
    font-size: 1.5rem;
    color: #555;
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: #007bff;
}

.social-links i {
    transition: transform 0.2s ease;
}

.social-links a:hover i {
    transform: scale(1.2);
}

ul.list-unstyled li {
    font-size: 1rem;
    color: #333;
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

ul.list-unstyled li i {
    margin-right: 8px;
    color: #007bff;
}

    </style>
</head>
<body>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>IT Department Lectures Pannel</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php include_once("../includes/header.php") ?>
                            <?php include_once("../includes/lectures-sidebar.php") ?>

                            <div class="container mt-4">
                                <div class="row">
                                    <?php foreach ($lecturers as $lecturer): ?>
                                        <div class="col-md-4">
                                            <div class="card lecturer-card shadow-lg rounded">
                                                <div>
                                                    <div class=" text-center mt-3 mb-1">
                                                    <img src="<?php echo $lecturer['profile_picture']; ?>" class="card-img-top " alt="Profile Picture" onerror="this.onerror=null;this.src='uploads/profile_pictures/default.jpg';">
                                                </div>
                                                <div class="card-body" style="min-height: 300px;">
                                                    <h4 class="text-primary text-center "><?php echo $lecturer['username']; ?></h4>
                                                    <div class="card-text  mt-1"><strong>Email:</strong> <?php echo $lecturer['email']; ?></div>
                                                    <div class="card-text mt-1"><strong>Mobile:</strong> <?php echo $lecturer['mobile']; ?></div>

                                                    <!-- Social Links -->
                                                    <div class="social-links">
                                                        <strong>Social Links:</strong><br>
                                                        <?php if ($lecturer['linkedin']) { echo '<a href="' . $lecturer['linkedin'] . '" target="_blank" class="social-icon linkedin"><span style="color: #0077B5;"><i class="fab fa-linkedin"></i></a></span>'; } ?>
                                                        <?php if ($lecturer['blog']) { echo '<a href="' . $lecturer['blog'] . '" target="_blank" class="social-icon blog"><span style="color: #fc4f08;"><i class="fas fa-blog"></i></a></span>'; } ?>
                                                        <?php if ($lecturer['github']) { echo '<a href="' . $lecturer['github'] . '" target="_blank" class="social-icon github"><span style="color:  #171515;"><i class="fab fa-github"></i></a></span>'; } ?>
                                                        <?php if ($lecturer['facebook']) { echo '<a href="' . $lecturer['facebook'] . '" target="_blank" class="social-icon facebook"><span style="color: #1877F2;"><i class="fab fa-facebook"></i></a></span>'; } ?>
                                                    </div>

                                                    <div class="mt-1">
                                                        <strong>Subjects:</strong>
                                                        <ul class="list-unstyled">
                                                            <?php
                                                            $stmt = $conn->prepare($subjects_query);
                                                            $stmt->bind_param("i", $lecturer['id']);
                                                            $stmt->execute();
                                                            $subjects_result = $stmt->get_result();
                                                            while ($subject = $subjects_result->fetch_assoc()) {
                                                                echo '<li><i class="fas fa-book mr-2"></i>' .'[ ' . $subject['code'].' ] ' .  $subject['name'] .'</li>';
                                                            }
                                                            $stmt->close();
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

   
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body"> 
                                            <!-- Recently Logged-In Lecturers -->
                                            <?php
                                            $recent_lecturers_query = "SELECT * FROM lectures ORDER BY last_login DESC LIMIT 5";
                                            $recent_lecturers_result = $conn->query($recent_lecturers_query);
                                            ?>
                                            <div class="container mt-2">
                                                <h4 class="mb-2">Recently Logged-In Lecturers</h4>
                                                <div class="row">
                                                    <?php while ($lecturer = $recent_lecturers_result->fetch_assoc()): ?>
                                                        <div class="col-md-4 col-lg-3">
                                                            <div class="card mini-card shadow-lg">
                                                                <div class="d-flex align-items-center p-2">
                                                                    <img src="<?php echo $lecturer['profile_picture']; ?>" 
                                                                         alt="Profile Picture"
                                                                         class="rounded-circle me-2"
                                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                                         onerror="this.onerror=null;this.src='uploads/profile_pictures/default.jpg';">
                                                                    <div>
                                                                        <h6 class="mb-0"><?php echo $lecturer['username']; ?></h6>
                                                                        <small class="text-muted">
                                                                            <?php 
                                                                            if (!empty($lecturer['last_login'])) {
                                                                                echo "Last login: " . date("M d, Y h:i A", strtotime($lecturer['last_login']));
                                                                            } else {
                                                                                echo "Last login: N/A";
                                                                            }
                                                                            ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>

                                            <!-- Recently Logged-In Students -->
                                            <?php 
                                            $recent_students_query = "SELECT * FROM students ORDER BY last_login DESC LIMIT 5";
                                            $recent_students_result = $conn->query($recent_students_query);
                                            ?>
                                            <div class="container mt-2">
                                                <h4 class="mb-2">Recently Logged-In Students</h4>
                                                <div class="row">
                                                    <?php while ($student = $recent_students_result->fetch_assoc()): ?>
                                                        <div class="col-md-4 col-lg-3">
                                                            <div class="card mini-card shadow-lg">
                                                                <div class="d-flex align-items-center p-2">
                                                                    <img src="../<?php echo $student['profile_picture']; ?>" 
                                                                         alt="Profile Picture"
                                                                         class="rounded-circle me-2"
                                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                                         onerror="this.onerror=null;this.src='../uploads/profile_pictures/default.png';">
                                                                    <div>
                                                                        <h6 class="mb-0"><?php echo $student['username']; ?></h6>
                                                                        <small class="text-muted">
                                                                            <?php 
                                                                            if (!empty($student['last_login'])) {
                                                                                echo "Last login: " . date("M d, Y h:i A", strtotime($student['last_login']));
                                                                            } else {
                                                                                echo "Last login: N/A";
                                                                            }
                                                                            ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>

                                            <!-- Recently Logged-In Former Students -->
                                            <?php
                                            $recent_former_students_query = "SELECT * FROM former_students ORDER BY last_login DESC LIMIT 5";
                                            $recent_former_students_result = $conn->query($recent_former_students_query);
                                            ?>
                                            <div class="container mt-2">
                                                <h4 class="mb-2">Recently Logged-In Former Students</h4>
                                                <div class="row">
                                                    <?php while ($former_student = $recent_former_students_result->fetch_assoc()): ?>
                                                        <div class="col-md-4 col-lg-3">
                                                            <div class="card mini-card shadow-lg">
                                                                <div class="d-flex align-items-center p-2">
                                                                    <img src="../oddstudents/<?php echo $former_student['profile_picture']; ?>" 
                                                                         alt="Profile Picture"
                                                                         class="rounded-circle me-2"
                                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                                         onerror="this.onerror=null;this.src='../oddstudents/uploads/profile_pictures/default.png';">
                                                                    <div>
                                                                        <h6 class="mb-0"><?php echo $former_student['username']; ?></h6>
                                                                        <small class="text-muted">
                                                                            <?php 
                                                                            if (!empty($former_student['last_login'])) {
                                                                                echo "Last login: " . date("M d, Y h:i A", strtotime($former_student['last_login']));
                                                                            } else {
                                                                                echo "Last login: N/A";
                                                                            }
                                                                            ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>

                                            <style>
                                                .mini-card {
                                                    border-radius: 12px;
                                                    transition: transform 0.2s;
                                                    background-color: #f8f9fa;
                                                    margin-bottom: 10px;
                                                }

                                                .mini-card:hover {
                                                    transform: scale(1.02);
                                                    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
                                                }
                                            </style>
 
                                
                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include_once("../includes/footer.php") ?>
    <?php include_once("../includes/js-links-inc.php") ?>
</body>
</html>
