<?php
require_once '../includes/db-conn.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure lecturer_id is passed in URL
if (!isset($_GET['lecturer_id'])) {
    echo "Invalid profile!";
    exit();
}

$lecturer_id = intval($_GET['lecturer_id']);

// Fetch logged-in admin user details
$user_id = $_SESSION['admin_id'] ?? null;
if (!$user_id) {
    echo "Unauthorized access!";
    exit();
}

$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch lecturer basic info
$sql = "SELECT * FROM lectures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();
$stmt->close();

if (!$lecturer) {
    echo "Profile not found.";
    exit();
}

// Fetch courses taught by this lecturer with proper join to subjects table
$sql_courses = "SELECT s.*, la.id as assignment_id 
                FROM lectures_assignment la 
                JOIN subjects s ON la.subject_id = s.id 
                WHERE la.lecturer_id = ?";
$stmt = $conn->prepare($sql_courses);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$courses = [];
while($course = $courses_result->fetch_assoc()) {
    $courses[] = $course;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lecturer['username']) ?>'s Profile - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-200: #bfdbfe;
            --blue-300: #93c5fd;
            --blue-400: #60a5fa;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --blue-800: #1e40af;
            --blue-900: #1e3a8a;
            --accent-purple: #8b5cf6;
            --accent-teal: #0d9488;
            --accent-cyan: #06b6d4;
            --dark-blue: #1e293b;
            --darker-blue: #0f172a;
            --light-blue: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-blue: 0 10px 15px -3px rgb(37 99 235 / 0.1), 0 4px 6px -4px rgb(37 99 235 / 0.1);
            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
            min-height: 100vh;
        }

        .main {
            background: transparent;
        }

        /* Modern Header */
        .profile-header {
            background: linear-gradient(135deg, var(--accent-purple) 0%, var(--primary-blue) 50%, var(--blue-700) 100%);
            color: white;
            border-radius: var(--radius-xl);
            padding: 40px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }

        .profile-img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.9);
            object-fit: cover;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            background: white;
        }

        .profile-img:hover {
            transform: scale(1.05);
            border-color: white;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
        }

        /* Enhanced Card Design */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-xl);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-purple), var(--primary-blue));
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--blue-200);
        }

        .modern-card .card-body {
            padding: 30px;
        }

        /* Info Cards */
        .info-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--blue-100);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-purple), var(--primary-blue));
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--blue-200);
        }

        .info-card .card-body {
            padding: 25px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 20px;
        }

        .card-title i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-purple), var(--primary-blue));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-100);
            transition: background-color 0.2s ease;
        }

        .info-item:hover {
            background-color: var(--blue-50);
            border-radius: 6px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            color: var(--primary-blue);
            width: 16px;
        }

        .info-value {
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Enhanced Buttons */
        .btn-modern {
            background: linear-gradient(135deg, var(--accent-purple), var(--primary-blue));
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            color: white;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-blue), var(--blue-700));
            color: white;
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        /* Social Media Links */
        .social-links-modern {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .social-link:hover {
            background: white;
            transform: translateY(-3px) scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .social-link:hover::before {
            left: 100%;
        }

        .social-link.linkedin:hover { color: #0077b5; }
        .social-link.facebook:hover { color: #1877f2; }
        .social-link.github:hover { color: #333; }
        .social-link.blog:hover { color: var(--primary-blue); }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--accent-purple), var(--primary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Course List */
        .course-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: var(--radius);
            background: var(--blue-50);
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .course-item:hover {
            background: var(--blue-100);
            transform: translateX(5px);
        }

        .course-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-purple), var(--primary-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .course-info {
            flex: 1;
        }

        .course-title {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 5px;
        }

        .course-meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-header {
                padding: 30px 20px;
                text-align: center;
            }
            
            .profile-img {
                width: 120px;
                height: 120px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .social-links-modern {
                flex-wrap: wrap;
                gap: 10px;
            }
        }

        /* Animation Classes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--blue-50);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-blue);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--blue-700);
        }

        /* Breadcrumb Styling */
        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: var(--gray-600);
        }
    </style>
</head>
<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1 class="gradient-text">Lecturer Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage-lectures.php">Lecturers</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Profile Header -->
                    <div class="profile-header animate-fade-in-up">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <img src="../lectures/<?= htmlspecialchars($lecturer['profile_picture']) ?>" 
                                     alt="Profile Picture" 
                                     class="profile-img"
                                     onerror="this.src='../uploads/profile_pictures/default.png'">
                            </div>
                            <div class="col-md-6 text-center text-md-start">
                                <h1 class="text-white mb-2"><?= htmlspecialchars($lecturer['username']) ?></h1>
                                <p class="text-white opacity-90 mb-3">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Lecturer at EduWide
                                </p>
                                
                                <!-- Quick Stats -->
                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="stat-number"><?= count($courses) ?></div>
                                        <div class="stat-label">Courses</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number"><?= $lecturer['status'] === 'active' ? 'Active' : 'Inactive' ?></div>
                                        <div class="stat-label">Status</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number"><?= $lecturer['created_at'] ? date('Y', strtotime($lecturer['created_at'])) : 'N/A' ?></div>
                                        <div class="stat-label">Joined</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <!-- Status Badge -->
                                <div class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill mb-3">
                                    <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                    <?= $lecturer['status'] === 'active' ? 'Active' : 'Inactive' ?> Lecturer
                                </div>
                                
                                <!-- Social Media Links -->
                                <div class="social-links-modern">
                                    <?php if (!empty($lecturer['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($lecturer['linkedin']) ?>" target="_blank" class="social-link linkedin" title="LinkedIn">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($lecturer['facebook'])): ?>
                                        <a href="<?= htmlspecialchars($lecturer['facebook']) ?>" target="_blank" class="social-link facebook" title="Facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($lecturer['github'])): ?>
                                        <a href="<?= htmlspecialchars($lecturer['github']) ?>" target="_blank" class="social-link github" title="GitHub">
                                            <i class="fab fa-github"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($lecturer['blog'])): ?>
                                        <a href="<?= htmlspecialchars($lecturer['blog']) ?>" target="_blank" class="social-link blog" title="Blog">
                                            <i class="fas fa-blog"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="modern-card animate-fade-in-up">
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-user-circle"></i>
                                                Personal Information
                                            </h5>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-id-card"></i>Lecturer ID
                                                </span>
                                                <span class="info-value">#<?= htmlspecialchars($lecturer['id']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-user"></i>Full Name
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($lecturer['username']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-address-card"></i>NIC Number
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($lecturer['nic']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-calendar-alt"></i>Member Since
                                                </span>
                                                <span class="info-value">
                                                    <?= $lecturer['created_at'] ? date('M j, Y', strtotime($lecturer['created_at'])) : 'N/A' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-address-book"></i>
                                                Contact Information
                                            </h5>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-envelope"></i>Email Address
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($lecturer['email']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-phone"></i>Mobile Number
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($lecturer['mobile']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-sign-in-alt"></i>Last Login
                                                </span>
                                                <span class="info-value">
                                                    <?= $lecturer['last_login'] ? 
                                                        date('M j, Y g:i A', strtotime($lecturer['last_login'])) : 
                                                        'Never' ?>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-user-tag"></i>Role
                                                </span>
                                                <span class="info-value badge bg-primary">Lecturer</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Courses Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="info-card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-book-open"></i>
                                                Assigned Courses
                                            </h5>
                                            <?php if (count($courses) > 0): ?>
                                                <?php foreach($courses as $course): ?>
                                                <div class="course-item">
                                                    <div class="course-icon">
                                                        <i class="fas fa-book"></i>
                                                    </div>
                                                    <div class="course-info">
                                                        <div class="course-title"><?= htmlspecialchars($course['name']) ?></div>
                                                        <div class="course-meta">
                                                            <span><i class="fas fa-code"></i> <?= htmlspecialchars($course['code']) ?></span>
                                                            <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($course['semester']) ?></span>
                                                            <span><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars($course['course']) ?></span>
                                                        </div>
                                                        <?php if (!empty($course['description'])): ?>
                                                            <div class="course-description mt-2 text-sm text-gray-600">
                                                                <?= nl2br(htmlspecialchars($course['description'])) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-success">Active</span>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-center py-4">
                                                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No courses assigned to this lecturer yet.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-3 justify-content-end">
                                        <a href="manage-lectures.php" class="btn-modern">
                                            <i class="fas fa-arrow-left"></i>Back to Lecturers
                                        </a>
                                        <a href="edit-lecture.php?lecturer_id=<?= $lecturer['id'] ?>" class="btn-modern" style="background: linear-gradient(135deg, var(--accent-teal), #0f766e);">
                                            <i class="fas fa-edit"></i>Edit Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>
    <?php include_once("../includes/js-links-inc.php") ?>

    <script>
        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all modern cards
            document.querySelectorAll('.modern-card, .info-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            console.log('Lecturer profile page loaded successfully');
        });

        // Add hover effects for social links
        document.querySelectorAll('.social-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.1)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>