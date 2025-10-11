<?php
require_once '../includes/db-conn.php';
session_start();

if (!isset($_GET['admin_id'])) {
    echo "Invalid profile!";
    exit();
}

$admin_id = intval($_GET['admin_id']);

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

// Fetch admin basic info
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo "Profile not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($admin['username']) ?>'s Profile - EduWide</title>
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--blue-700) 50%, var(--blue-900) 100%);
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
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-cyan));
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
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-cyan));
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
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan));
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
            background: linear-gradient(135deg, var(--primary-blue), var(--blue-700));
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
            background: linear-gradient(135deg, var(--blue-600), var(--blue-800));
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

        .social-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
            color: white;
            font-weight: 500;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan));
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
            
            .social-label {
                display: none;
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
            <h1 class="gradient-text">Admin Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage-admins.php">Admins</a></li>
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
                                <img src="<?= htmlspecialchars($admin['profile_picture']) ?>" 
                                     alt="Profile Picture" 
                                     class="profile-img"
                                     onerror="this.src='../uploads/profile_pictures/default.png'">
                            </div>
                            <div class="col-md-6 text-center text-md-start">
                                <h1 class="text-white mb-2"><?= htmlspecialchars($admin['username']) ?></h1>
                                <p class="text-white opacity-90 mb-3">
                                    <i class="fas fa-user-shield me-2"></i>System Administrator
                                </p>
                                
                                <!-- Quick Stats -->
                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="stat-number"></div>
                                        <div class="stat-label"></div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number"></div>
                                        <div class="stat-label"></div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number"></div>
                                        <div class="stat-label"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <!-- Status Badge -->
                                <div class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill mb-3">
                                    <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                    Active Administrator
                                </div>
                                
                                <!-- Social Media Links -->
                                <div class="social-links-modern">
                                    <?php if (!empty($admin['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($admin['linkedin']) ?>" target="_blank" class="social-link linkedin" title="LinkedIn">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($admin['facebook'])): ?>
                                        <a href="<?= htmlspecialchars($admin['facebook']) ?>" target="_blank" class="social-link facebook" title="Facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($admin['github'])): ?>
                                        <a href="<?= htmlspecialchars($admin['github']) ?>" target="_blank" class="social-link github" title="GitHub">
                                            <i class="fab fa-github"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($admin['blog'])): ?>
                                        <a href="<?= htmlspecialchars($admin['blog']) ?>" target="_blank" class="social-link blog" title="Blog">
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
                                                    <i class="fas fa-id-card"></i>Admin ID
                                                </span>
                                                <span class="info-value">#<?= htmlspecialchars($admin['id']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-user"></i>Username
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($admin['username']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-address-card"></i>NIC Number
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($admin['nic']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-calendar-alt"></i>Member Since
                                                </span>
                                                <span class="info-value">
                                                    <?= $admin['created_at'] ? date('M j, Y', strtotime($admin['created_at'])) : 'N/A' ?>
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
                                                <span class="info-value"><?= htmlspecialchars($admin['email']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-phone"></i>Mobile Number
                                                </span>
                                                <span class="info-value"><?= htmlspecialchars($admin['mobile']) ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-sign-in-alt"></i>Last Login
                                                </span>
                                                <span class="info-value">
                                                    <?= $admin['last_login'] ? 
                                                        date('M j, Y g:i A', strtotime($admin['last_login'])) : 
                                                        'Never' ?>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">
                                                    <i class="fas fa-user-tag"></i>Role
                                                </span>
                                                <span class="info-value badge bg-primary">Administrator</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-3 justify-content-end">
                                        <a href="manage-admins.php" class="btn-modern">
                                            <i class="fas fa-arrow-left"></i>Back to Admins
                                        </a>
                                        <a href="edit-admin.php?admin_id=<?= $admin['id'] ?>" class="btn-modern" style="background: linear-gradient(135deg, var(--accent-teal), #0f766e);">
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

            console.log('Admin profile page loaded successfully');
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