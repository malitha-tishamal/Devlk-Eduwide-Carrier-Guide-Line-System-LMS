<?php
require_once '../includes/db-conn.php';
session_start();

// Get former student ID from URL
if (!isset($_GET['former_student_id'])) {
    echo "Invalid profile!";
    exit();
}

// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$student_id = intval($_GET['former_student_id']);

// Fetch student basic info
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

// Fetch education
$edu_sql = "SELECT * FROM education WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($edu_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$education = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch work experience
$work_sql = "SELECT * FROM experiences WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($work_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch summary
$summary_sql = "SELECT summary FROM summaries WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($summary_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$summary_result = $stmt->get_result();
$summary = $summary_result->fetch_assoc()['summary'] ?? '';
$stmt->close();

// Fetch about section
$about_sql = "SELECT about_text FROM about WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($about_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$about_result = $stmt->get_result();
$about = $about_result->fetch_assoc()['about_text'] ?? '';
$stmt->close();

// Fetch achievements
$achievements_sql = "SELECT * FROM former_students_achievements WHERE former_student_id = ? ORDER BY event_date DESC";
$stmt = $conn->prepare($achievements_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch certifications
$certifications_sql = "SELECT * FROM former_students_certifications WHERE former_student_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($certifications_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$certifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch skills
$skills_sql = "
    SELECT s.skill_name, s.category 
    FROM former_student_skills fs 
    JOIN (
        SELECT id, skill_name, 'IT' AS category FROM it_student_skills
        UNION ALL
        SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
    ) s ON fs.skill_id = s.id
    WHERE fs.student_id = ?
    ORDER BY s.category, s.skill_name
";
$stmt = $conn->prepare($skills_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$skills_result = $stmt->get_result();
$skills = $skills_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch projects with images and links count
$projects_sql = "
    SELECT p.*, 
           COUNT(DISTINCT pp.id) as image_count, 
           COUNT(DISTINCT pl.id) as link_count
    FROM former_student_projects p 
    LEFT JOIN former_student_project_photos pp ON p.id = pp.project_id 
    LEFT JOIN former_student_project_links pl ON p.id = pl.project_id 
    WHERE p.student_id = ? 
    GROUP BY p.id 
    ORDER BY p.created_at DESC
";
$stmt = $conn->prepare($projects_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$projects_result = $stmt->get_result();
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch course name from hnd_courses
$course_name = '';
if (!empty($student['course_id'])) {
    $course_sql = "SELECT name FROM hnd_courses WHERE id = ?";
    $stmt = $conn->prepare($course_sql);
    $stmt->bind_param("i", $student['course_id']);
    $stmt->execute();
    $course_result = $stmt->get_result();
    $course_row = $course_result->fetch_assoc();
    $course_name = $course_row['name'] ?? '';
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($student['username']) ?>'s Profile - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --dark-color: #1d3557;
            --light-color: #f8f9fa;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .skill-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
        }

        .timeline-item {
            border-left: 3px solid var(--primary-color);
            padding-left: 20px;
            padding-bottom: 20px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 5px;
            width: 14px;
            height: 14px;
            background: var(--primary-color);
            border-radius: 50%;
        }

        .project-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .project-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-icon:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .category-badge {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            margin-left: 5px;
        }

        .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #555;
        }

        .contact-info i {
            width: 20px;
            margin-right: 10px;
            color: var(--primary-color);
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            border: none;
            padding: 12px 20px;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background: transparent;
        }

        .achievement-card, .certification-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            height: 100%;
        }

        .achievement-card:hover, .certification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .modal-img {
            width: 100%;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Student Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="manage-former-students.php">Former Students</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Profile Header -->
                    <div class="profile-header text-center">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <img src="../oddstudents/<?= htmlspecialchars($student['profile_picture']) ?>" 
                                     alt="Profile Picture" class="profile-picture">
                            </div>
                            <div class="col-md-6">
                                <h1 class="mb-2"><?= htmlspecialchars($student['username']) ?></h1>
                                <?php if (!empty($course_name)): ?>
                                    <p class="fs-5 mb-2"><i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($course_name) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($summary)): ?>
                                    <p class="mb-3"><?= nl2br(htmlspecialchars($summary)) ?></p>
                                <?php endif; ?>
                                
                                <!-- Social Media Links -->
                                <div class="social-links mt-3">
                                    <?php if (!empty($student['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($student['linkedin']) ?>" target="_blank" class="social-icon">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($student['facebook'])): ?>
                                        <a href="<?= htmlspecialchars($student['facebook']) ?>" target="_blank" class="social-icon">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($student['github'])): ?>
                                        <a href="<?= htmlspecialchars($student['github']) ?>" target="_blank" class="social-icon">
                                            <i class="fab fa-github"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($student['blog'])): ?>
                                        <a href="<?= htmlspecialchars($student['blog']) ?>" target="_blank" class="social-icon">
                                            <i class="fas fa-blog"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Quick Stats -->
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="stats-card">
                                            <div class="stats-number"><?= count($skills) ?></div>
                                            <div class="stats-label">Skills</div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stats-card">
                                            <div class="stats-number"><?= count($projects) ?></div>
                                            <div class="stats-label">Projects</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number"><?= count($education) ?></div>
                                            <div class="stats-label">Education</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stats-card">
                                            <div class="stats-number"><?= count($experiences) ?></div>
                                            <div class="stats-label">Experiences</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>About
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills" type="button" role="tab">
                                <i class="fas fa-code me-2"></i>Skills
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                                <i class="fas fa-project-diagram me-2"></i>Projects
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab">
                                <i class="fas fa-graduation-cap me-2"></i>Education
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab">
                                <i class="fas fa-briefcase me-2"></i>Experience
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">
                                <i class="fas fa-trophy me-2"></i>Achievements
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="profileTabsContent">
                        
                        <!-- About Tab -->
                        <div class="tab-pane fade show active" id="about" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="section-card">
                                        <div class="card-body">
                                            <h4 class="section-title">
                                                <i class="fas fa-user-circle"></i>About Me
                                            </h4>
                                            <?php if (!empty($about)): ?>
                                                <p class="lead"><?= nl2br(htmlspecialchars($about)) ?></p>
                                            <?php else: ?>
                                                <div class="empty-state">
                                                    <i class="fas fa-user-circle"></i>
                                                    <h5>No About Information</h5>
                                                    <p>This student hasn't added an about section yet.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
<div class="section-card">
    <div class="card-body">
        <h4 class="section-title">
            <i class="fas fa-address-card"></i>Contact Information
        </h4>
        <div class="row">
            <?php if (!empty($student['email'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($student['email']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($student['mobile'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($student['mobile']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($student['nic'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-id-card"></i>
                        <span>NIC: <?= htmlspecialchars($student['nic']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($student['reg_id'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-id-badge"></i>
                        <span>Reg ID: <?= htmlspecialchars($student['reg_id']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($student['study_year'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Study Year: <?= htmlspecialchars($student['study_year']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($student['nowstatus'])): ?>
                <div class="col-md-6">
                    <div class="contact-info">
                        <i class="fas fa-user-tag"></i>
                        <span>Current Status: <?= htmlspecialchars($student['nowstatus']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

                                <div class="col-lg-4">
                                    <!-- Quick Links -->
                                    <div class="section-card">
                                        <div class="card-body">
                                            <h4 class="section-title">
                                                <i class="fas fa-external-link-alt"></i>Quick Links
                                            </h4>
                                            <div class="d-grid gap-2">
                                                <?php if (!empty($student['linkedin'])): ?>
                                                    <a href="<?= htmlspecialchars($student['linkedin']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <i class="fab fa-linkedin me-2"></i>LinkedIn Profile
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($student['github'])): ?>
                                                    <a href="<?= htmlspecialchars($student['github']) ?>" target="_blank" class="btn btn-outline-dark btn-sm">
                                                        <i class="fab fa-github me-2"></i>GitHub Profile
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($student['blog'])): ?>
                                                    <a href="<?= htmlspecialchars($student['blog']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-blog me-2"></i>Personal Blog
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Certifications Preview -->
                                    <?php if (!empty($certifications)): ?>
                                        <div class="section-card">
                                            <div class="card-body">
                                                <h4 class="section-title">
                                                    <i class="fas fa-certificate"></i>Recent Certifications
                                                </h4>
                                                <?php foreach (array_slice($certifications, 0, 3) as $cert): ?>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <i class="fas fa-certificate text-warning me-3 fs-4"></i>
                                                        <div>
                                                            <h6 class="mb-0"><?= htmlspecialchars($cert['certification_name']) ?></h6>
                                                            <small class="text-muted"><?= htmlspecialchars($cert['issued_by']) ?></small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($certifications) > 3): ?>
                                                    <a href="#achievements" class="btn btn-sm btn-outline-primary w-100" onclick="switchTab('achievements-tab')">
                                                        View All Certifications
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Tab -->
                        <div class="tab-pane fade" id="skills" role="tabpanel">
                            <div class="section-card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="fas fa-code"></i>Technical Skills
                                    </h4>
                                    <?php if (!empty($skills)): ?>
                                        <div class="mb-4">
                                            <?php
                                            $current_category = '';
                                            foreach ($skills as $skill):
                                                if ($current_category != $skill['category']):
                                                    $current_category = $skill['category'];
                                            ?>
                                                    <h5 class="mt-4 mb-3 text-primary"><?= $skill['category'] ?> Skills</h5>
                                            <?php endif; ?>
                                                <span class="skill-badge">
                                                    <?= htmlspecialchars($skill['skill_name']) ?>
                                                    <span class="category-badge"><?= $skill['category'] ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-code"></i>
                                            <h5>No Skills Added</h5>
                                            <p>This student hasn't added any skills yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Projects Tab -->
                        <div class="tab-pane fade" id="projects" role="tabpanel">
                            <?php if (!empty($projects)): ?>
                                <div class="row">
                                    <?php foreach ($projects as $project): ?>
                                        <div class="col-lg-6 mb-4">
                                            <div class="project-card">
                                                <?php
                                                // Get first project image
                                                $image_sql = "SELECT image_path FROM former_student_project_photos WHERE project_id = ? LIMIT 1";
                                                $img_stmt = $conn->prepare($image_sql);
                                                $img_stmt->bind_param("i", $project['id']);
                                                $img_stmt->execute();
                                                $img_result = $img_stmt->get_result();
                                                $project_image = $img_result->fetch_assoc();
                                                $img_stmt->close();
                                                ?>
                                                <?php if ($project_image): ?>
                                                    <img src="../<?= htmlspecialchars($project_image['image_path']) ?>" 
                                                         class="project-image" 
                                                         alt="<?= htmlspecialchars($project['title']) ?>">
                                                <?php else: ?>
                                                    <div class="project-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-project-diagram fa-3x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                                                    <p class="card-text text-muted"><?= nl2br(htmlspecialchars(substr($project['description'], 0, 150))) ?>...</p>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <small class="text-muted">
                                                            <i class="far fa-calendar me-1"></i>
                                                            <?= date('M Y', strtotime($project['start_date'])) ?>
                                                            <?php if($project['end_date']): ?>
                                                                - <?= date('M Y', strtotime($project['end_date'])) ?>
                                                            <?php else: ?>
                                                                - Present
                                                            <?php endif; ?>
                                                        </small>
                                                        <div>
                                                            <span class="badge bg-light text-dark me-1">
                                                                <i class="far fa-image"></i> <?= $project['image_count'] ?>
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i class="fas fa-link"></i> <?= $project['link_count'] ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#projectModal<?= $project['id'] ?>">
                                                        View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Project Modal -->
                                        <div class="modal fade" id="projectModal<?= $project['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><?= htmlspecialchars($project['title']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Description:</strong></p>
                                                        <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                                                        
                                                        <p><strong>Timeline:</strong> 
                                                            <?= date('M j, Y', strtotime($project['start_date'])) ?>
                                                            <?php if($project['end_date']): ?>
                                                                - <?= date('M j, Y', strtotime($project['end_date'])) ?>
                                                            <?php else: ?>
                                                                - Present
                                                            <?php endif; ?>
                                                        </p>
                                                        
                                                        <!-- Project Images -->
                                                        <?php
                                                        $photos_sql = "SELECT * FROM former_student_project_photos WHERE project_id = ?";
                                                        $photos_stmt = $conn->prepare($photos_sql);
                                                        $photos_stmt->bind_param("i", $project['id']);
                                                        $photos_stmt->execute();
                                                        $photos = $photos_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                                        $photos_stmt->close();
                                                        ?>
                                                        <?php if (!empty($photos)): ?>
                                                            <p><strong>Project Images:</strong></p>
                                                            <div class="row">
                                                                <?php foreach ($photos as $photo): ?>
                                                                    <div class="col-4 mb-3">
                                                                        <img src="../<?= htmlspecialchars($photo['image_path']) ?>" 
                                                                             class="img-thumbnail" 
                                                                             style="width: 100%; height: 100px; object-fit: cover;"
                                                                             data-bs-toggle="modal" 
                                                                             data-bs-target="#imageModal"
                                                                             data-img="../<?= htmlspecialchars($photo['image_path']) ?>">
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Project Links -->
                                                        <?php
                                                        $links_sql = "SELECT * FROM former_student_project_links WHERE project_id = ?";
                                                        $links_stmt = $conn->prepare($links_sql);
                                                        $links_stmt->bind_param("i", $project['id']);
                                                        $links_stmt->execute();
                                                        $links = $links_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                                        $links_stmt->close();
                                                        ?>
                                                        <?php if (!empty($links)): ?>
                                                            <p><strong>Project Links:</strong></p>
                                                            <div class="project-links">
                                                                <?php foreach ($links as $link): ?>
                                                                    <div class="project-link-item mb-2">
                                                                        <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                                                        <strong><?= htmlspecialchars($link['link_type']) ?>:</strong>
                                                                        <a href="<?= htmlspecialchars($link['link_url']) ?>" target="_blank" class="ms-2">
                                                                            <?= htmlspecialchars($link['link_url']) ?>
                                                                        </a>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="section-card">
                                    <div class="card-body">
                                        <div class="empty-state">
                                            <i class="fas fa-project-diagram"></i>
                                            <h5>No Projects Added</h5>
                                            <p>This student hasn't added any projects yet.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Education Tab -->
                        <div class="tab-pane fade" id="education" role="tabpanel">
                            <div class="section-card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="fas fa-graduation-cap"></i>Education History
                                    </h4>
                                    <?php if (!empty($education)): ?>
                                        <div class="timeline">
                                            <?php foreach ($education as $edu): ?>
                                                <div class="timeline-item">
                                                    <h5 class="mb-1"><?= htmlspecialchars($edu['school']) ?></h5>
                                                    <p class="mb-1 text-primary fw-bold"><?= htmlspecialchars($edu['degree']) ?> in <?= htmlspecialchars($edu['field_of_study']) ?></p>
                                                    <p class="mb-2 text-muted">
                                                        <i class="far fa-calendar me-1"></i>
                                                        <?= htmlspecialchars($edu['start_month']) ?> <?= htmlspecialchars($edu['start_year']) ?> - 
                                                        <?= htmlspecialchars($edu['end_month']) ?> <?= htmlspecialchars($edu['end_year']) ?>
                                                    </p>
                                                    <?php if (!empty($edu['description'])): ?>
                                                        <p class="mb-0"><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-graduation-cap"></i>
                                            <h5>No Education History</h5>
                                            <p>This student hasn't added any education information yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Experience Tab -->
                        <div class="tab-pane fade" id="experience" role="tabpanel">
                            <div class="section-card">
                                <div class="card-body">
                                    <h4 class="section-title">
                                        <i class="fas fa-briefcase"></i>Work Experience
                                    </h4>
                                    <?php if (!empty($experiences)): ?>
                                        <div class="timeline">
                                            <?php foreach ($experiences as $exp): ?>
                                                <div class="timeline-item">
                                                    <h5 class="mb-1"><?= htmlspecialchars($exp['title']) ?> at <?= htmlspecialchars($exp['company']) ?></h5>
                                                    <p class="mb-1 text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($exp['location']) ?>
                                                    </p>
                                                    <p class="mb-2 text-muted">
                                                        <i class="far fa-calendar me-1"></i>
                                                        <?= htmlspecialchars($exp['start_month']) ?> <?= htmlspecialchars($exp['start_year']) ?> - 
                                                        <?= htmlspecialchars($exp['end_month']) ?> <?= htmlspecialchars($exp['end_year']) ?>
                                                    </p>
                                                    <?php if (!empty($exp['description'])): ?>
                                                        <p class="mb-0"><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-briefcase"></i>
                                            <h5>No Work Experience</h5>
                                            <p>This student hasn't added any work experience yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Achievements Tab -->
                        <div class="tab-pane fade" id="achievements" role="tabpanel">
                            <!-- Achievements Section -->
                            <?php if (!empty($achievements)): ?>
                                <div class="section-card">
                                    <div class="card-body">
                                        <h4 class="section-title">
                                            <i class="fas fa-trophy"></i>Achievements
                                        </h4>
                                        <div class="row">
                                            <?php foreach ($achievements as $ach): ?>
                                                <div class="col-md-6 col-lg-4 mb-4">
                                                    <div class="achievement-card">
                                                        <?php if (!empty($ach['image_path']) && file_exists('../oddstudents/' . $ach['image_path'])): ?>
                                                            <img src="../oddstudents/<?= htmlspecialchars($ach['image_path']) ?>" 
                                                                 class="card-img-top" 
                                                                 alt="Achievement Image" 
                                                                 style="height: 200px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                                <i class="fas fa-trophy fa-3x text-warning"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?= htmlspecialchars($ach['event_title']) ?></h5>
                                                            <p class="card-text"><strong>Event:</strong> <?= htmlspecialchars($ach['event_name']) ?></p>
                                                            <p class="card-text"><strong>Organized by:</strong> <?= htmlspecialchars($ach['organized_by']) ?></p>
                                                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($ach['event_date']) ?></p>
                                                            <p class="card-text"><?= nl2br(htmlspecialchars($ach['event_description'])) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Certifications Section -->
                            <?php if (!empty($certifications)): ?>
                                <div class="section-card">
                                    <div class="card-body">
                                        <h4 class="section-title">
                                            <i class="fas fa-certificate"></i>Certifications
                                        </h4>
                                        <div class="row">
                                            <?php foreach ($certifications as $cert): ?>
                                                <div class="col-md-6 col-lg-4 mb-4">
                                                    <div class="certification-card">
                                                        <?php if (!empty($cert['image_path']) && file_exists('../oddstudents/' . $cert['image_path'])): ?>
                                                            <img src="../oddstudents/<?= htmlspecialchars($cert['image_path']) ?>" 
                                                                 class="card-img-top" 
                                                                 alt="Certification Image" 
                                                                 style="height: 200px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                                <i class="fas fa-certificate fa-3x text-warning"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?= htmlspecialchars($cert['certification_name']) ?></h5>
                                                            <p class="card-text"><strong>Issued by:</strong> <?= htmlspecialchars($cert['issued_by']) ?></p>
                                                            <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($cert['date']) ?></p>
                                                            <?php if (!empty($cert['link'])): ?>
                                                                <p class="card-text">
                                                                    <strong>Link:</strong> 
                                                                    <a href="<?= htmlspecialchars($cert['link']) ?>" target="_blank">View Certificate</a>
                                                                </p>
                                                            <?php endif; ?>
                                                            <p class="card-text"><?= nl2br(htmlspecialchars($cert['certification_description'])) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($achievements) && empty($certifications)): ?>
                                <div class="section-card">
                                    <div class="card-body">
                                        <div class="empty-state">
                                            <i class="fas fa-trophy"></i>
                                            <h5>No Achievements or Certifications</h5>
                                            <p>This student hasn't added any achievements or certifications yet.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Project Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Project Image" class="modal-img">
                </div>
            </div>
        </div>
    </div>

    <?php include_once("../includes/footer.php") ?>
    <?php include_once("../includes/js-links-inc.php") ?>

    <script>
        // Function to switch tabs
        function switchTab(tabId) {
            const tabElement = document.getElementById(tabId);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }

        // Image modal functionality
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.getElementById('modalImage');
        
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('img-thumbnail') && e.target.hasAttribute('data-img')) {
                modalImage.src = e.target.getAttribute('data-img');
            }
        });

        // Auto-activate tab from URL hash
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash) {
                const tabId = hash.substring(1) + '-tab';
                switchTab(tabId);
            }
        });

        // Print profile functionality
        function printProfile() {
            window.print();
        }

        // Download as PDF (placeholder - you would need a PDF generation library)
        function downloadAsPDF() {
            alert('PDF download functionality would be implemented here with a library like jsPDF');
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>