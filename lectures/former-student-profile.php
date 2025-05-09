<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_GET['former_student_id'])) {
    echo "Invalid profile!";
    exit();
}

$student_id = intval($_GET['former_student_id']); // Use GET, not SESSION

// Fetch the lecturer user (logged-in user)
$user_id = $_SESSION['lecturer_id'];
$sql = "SELECT * FROM lectures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch the former student's profile
$sql = "SELECT * FROM former_students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc(); 
$stmt->close();

if (!$student) {
    echo "Profile not found.";
    exit();
}


$edu_sql = "SELECT * FROM education WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($edu_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$work_sql = "SELECT * FROM experiences WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($work_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$summary_sql = "SELECT summary FROM summaries WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($summary_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$summary_result = $stmt->get_result();
$summary = $summary_result->fetch_assoc()['summary'] ?? '';
$stmt->close();


$about_sql = "SELECT about_text FROM about WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($about_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$about_result = $stmt->get_result();
$about = $about_result->fetch_assoc()['about_text'] ?? '';
$stmt->close();

$achievements_sql = "SELECT * FROM former_students_achievements WHERE former_student_id = ? ORDER BY event_date DESC";
$stmt = $conn->prepare($achievements_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$certifications_sql = "SELECT * FROM former_students_certifications WHERE former_student_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($certifications_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$certifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($student['username']) ?>'s Profile - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styles */
        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 8px;
        }

        .profile-header img {
            border-radius: 50%;
            width: 140px;
            height: 140px;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }

        .profile-header h2 {
            margin-top: 15px;
            font-size: 2rem;
            color: #333;
        }

        .profile-header p {
            color: #555;
            font-size: 1rem;
        }

        .section-title {
            margin-top: 10px;
            font-size: 1.6rem;
            font-weight: 500;
            color: #333;
            border-bottom: 2px solid #0d6efd;
            display: inline-block;
            padding-bottom: 5px;
        }

        .card {
            margin-bottom: 30px;
            border-radius: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #ffffff;
        }

        .card-body {
            padding: 0px 20px 0px 20px;
        }

        .list-group-item {
            border: none;
            padding-left: 0;
            font-size: 1rem;
            color: #333;
        }

        .list-group-item strong {
            color: #0d6efd;
        }

        .btn-outline-primary {
            border: 2px solid #0d6efd;
            color: #0d6efd;
            border-radius: 30px;
            padding: 10px 25px;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .text-muted {
            color: #6c757d;
        }

        /* Social media links */
        .social-media-links {
            display: flex;
            justify-content: center;
            gap: 2px;
            margin-top: 20px;
        }

        .social-media-links a {
            font-size: 1.5rem;
            color: #0d6efd;
            transition: color 0.3s;
        }

        .social-media-links a:hover {
            color: #004085;
        }

        /* Two-column layout */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .col-md-6 {
            flex: 1;
            min-width: 300px;
        }

        .col-md-6 ul {
            padding-left: 20px;
        }

        .col-md-6 h5 {
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .col-md-6 p {
            font-size: 1rem;
            color: #333;
        }

        .timeline {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .timeline-item {
            display: flex;
            flex-direction: row;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
            padding-left: 20px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 10px;
            width: 16px;
            height: 16px;
            background-color: #0d6efd;
            border-radius: 50%;
        }

        .timeline-item h5 {
            font-size: 1.2rem;
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .timeline-item p {
            color: #555;
            font-size: 1rem;
        }

        .timeline-item span {
            font-weight: bold;
            color: #0d6efd;
        }

        /* Hover Effects */
        .timeline-item:hover {
            background-color: #f7f8fa;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card .card-body p {
            font-size: 1rem;
            color: #333;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .row {
                display: block;
            }

            .col-md-6 {
                margin-bottom: 20px;
            }
        }

    </style>
</head>
<body>

    <?php include_once("../includes/header.php") ?>

    <?php include_once("../includes/lectures-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Profile Overview</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">People</a></li>
                    <li class="breadcrumb-item"><a href="">Profile</a></li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="container">
                                <h3 class="card-title"><?= htmlspecialchars($student['username']) ?> Profile</h3>

                                <!-- Profile Header -->
                                <div class="profile-header">
                                    <img src="../oddstudents/<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile Picture">
                                    <h2 class="mt-2"><?= htmlspecialchars($student['username']) ?></h2>

                                    <?php if (!empty($summary)): ?>
                                        <p class="text-muted"><?= nl2br(htmlspecialchars($summary)) ?></p>
                                    <?php endif; ?>

                                    <!-- Social Media Links -->
                                    <div class="social-media-links">
                                        <?php if (!empty($student['linkedin'])): ?>
                                           <a href="<?= htmlspecialchars($student['linkedin']) ?>" target="_blank" class="fab fa-linkedin"></a>
                                        <?php endif; ?><span class="text-primary "><b> LinkedIn &nbsp;&nbsp;&nbsp;</b></span>
                                        <?php if (!empty($student['facebook'])): ?>
                                            <a href="<?= htmlspecialchars($student['facebook'])?>" target="_blank" class="fab fa-facebook"></a>
                                        <?php endif; ?><span class="text-primary "><b>Facebook &nbsp;&nbsp;&nbsp;</b></span>
                                        <?php if (!empty($student['github'])): ?>
                                            <a href="<?= htmlspecialchars($student['github']) ?>" target="_blank" class="fab fa-github"></a>
                                        <?php endif; ?><span class="text-primary "><b>Github &nbsp;&nbsp;&nbsp;</b></span>
                                        <?php if (!empty($student['blog'])): ?>
                                            <a href="<?= htmlspecialchars($student['blog']) ?>" target="_blank" class="fas fa-blog"></a>
                                        <?php endif; ?><span class="text-primary "><b>Blog </b></span>
                                    </div>
                                </div>

                                <!-- About Me Section -->
                                <?php if (!empty($about)): ?>
                                    <div class="card shadow-lg">
                                        <div class="card-body">
                                            <h4 class="section-title">About Me</h4>
                                            <p><?= nl2br(htmlspecialchars($about)) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Education and Work Experience Section -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="section-title">Education</h4>
                                        <ul class="timeline">
                                            <?php foreach ($education as $edu): ?>
                                                <li class="timeline-item">
                                                    <div>
                                                        <h5><?= htmlspecialchars($edu['school']) ?></h5>
                                                        <p><span><?= htmlspecialchars($edu['degree']) ?></span> - <?= htmlspecialchars($edu['field_of_study']) ?></p>
                                                        <p>From: <?= htmlspecialchars($edu['start_month']) ?> <?= htmlspecialchars($edu['start_year']) ?> to <?= htmlspecialchars($edu['end_month']) ?> <?= htmlspecialchars($edu['end_year']) ?></p>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <h4 class="section-title">Work Experience</h4>
                                        <ul class="timeline">
                                            <?php foreach ($experiences as $exp): ?>
                                                <li class="timeline-item">
                                                    <div>
                                                        <h5><?= htmlspecialchars($exp['title']) ?> at <?= htmlspecialchars($exp['company']) ?></h5>
                                                        <p><span><?= htmlspecialchars($exp['employment_type']) ?></span> - <?= htmlspecialchars($exp['location']) ?></p>
                                                        <p>From: <?= htmlspecialchars($exp['start_month']) ?> <?= htmlspecialchars($exp['start_year']) ?> to <?= htmlspecialchars($exp['end_month']) ?> <?= htmlspecialchars($exp['end_year']) ?></p>
                                                        <p><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <?php if (!empty($achievements)): ?>
                                    <div class="card shadow-lg">
                                        <div class="card-body">
                                            <h4 class="section-title mt-2">Student Achievements</h4>
                                            <div class="row">
                                                <?php foreach ($achievements as $ach): ?>
                                                    <div class="col-md-6 col-lg-4 ">
                                                        <div class="card h-100 border shadow-sm">
                                                            <?php if (!empty($ach['image_path']) && file_exists('../oddstudents/' . $ach['image_path'])): ?>
                                                                <img src="../oddstudents/<?= htmlspecialchars($ach['image_path']) ?>" class="card-img-top" alt="Achievement Image" style="width: 300px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <div class="card-body">
                                                                <h5 class="card-title"><?= htmlspecialchars($ach['event_title']) ?></h5>
                                                                <p><strong>Event Name:</strong> <?= htmlspecialchars($ach['event_name']) ?></p>
                                                                <p><strong>Organized By:</strong> <?= htmlspecialchars($ach['organized_by']) ?></p>
                                                                <p><strong>Date:</strong> <?= htmlspecialchars($ach['event_date']) ?></p>
                                                                <p><?= nl2br(htmlspecialchars($ach['event_description'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>


                                    <?php if (!empty($certifications)): ?>
                                        <div class="card shadow-lg ">
                                            <div class="card-body">
                                                <h4 class="section-title ">Certifications</h4>
                                                <div class="row">
                                                    <?php foreach ($certifications as $cert): ?>
                                                        <div class="col-md-6 col-lg-4 ">
                                                            <div class="card h-100 border shadow-sm">
                                                                <?php if (!empty($cert['image_path']) && file_exists('../oddstudents/' . $cert['image_path'])): ?>
                                                                    <img src="../oddstudents/<?= htmlspecialchars($cert['image_path']) ?>" class="card-img-top" alt="Certification Image" style="width: 300px; object-fit: cover;">
                                                                <?php endif; ?>
                                                                <div class="card-body">
                                                                    <h5 class="card-title"><?= htmlspecialchars($cert['certification_name']) ?></h5>
                                                                    <p><strong>Issued By:</strong> <?= htmlspecialchars($cert['issued_by']) ?></p>
                                                                    <p><strong>Date:</strong> <?= htmlspecialchars($cert['date']) ?></p>
                                                                    <?php if (!empty($cert['link'])): ?>
                                                                        <p><strong>Link:</strong> <a href="<?= htmlspecialchars($cert['link']) ?>" target="_blank">View</a></p>
                                                                    <?php endif; ?>
                                                                    <p><?= nl2br(htmlspecialchars($cert['certification_description'])) ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <a href="manage-former-students-edu.php" class="btn btn-primary mb-4"><i class="bi bi-arrow-bar-left"></i> Back </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>

    <?php include_once("../includes/js-links-inc.php") ?>
</body>
</html>
