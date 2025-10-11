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

// Fetch admins from the database
$sql = "SELECT * FROM admins WHERE LOWER(status) IN ('active', 'approved') ORDER BY username ASC";
$result = $conn->query($sql);
$total_admins = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Admin Contacts - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --dark-color: #1d3557;
            --light-color: #f8f9fa;
        }

        .admin-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .admin-card .card-body {
            padding: 25px;
        }

        .admin-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin: 0 auto 15px;
            display: block;
        }

        .admin-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
            text-align: center;
        }

        .admin-role {
            color: var(--primary-color);
            font-weight: 500;
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .contact-details {
            flex: 1;
        }

        .contact-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .contact-value {
            font-size: 0.95rem;
            color: var(--dark-color);
            font-weight: 500;
        }

        .contact-link {
            color: var(--dark-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .contact-link:hover {
            color: var(--primary-color);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .stats-icon {
            font-size: 3rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .empty-state h5 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #495057;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .btn-contact {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-contact:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .admin-id {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .card-header-section {
            position: relative;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .admin-card .card-body {
                padding: 20px;
            }
            
            .admin-avatar {
                width: 70px;
                height: 70px;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>

    <?php include_once("../includes/header.php") ?>

    <?php include_once("../includes/company-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Admin Contacts</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Contacts</li>
                    <li class="breadcrumb-item active">Admins</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <!-- Stats Card -->
                    <div class="stats-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="stats-number"><?= $total_admins ?></div>
                                <div class="stats-label">Total Administrators</div>
                                <p class="mt-2 mb-0" style="opacity: 0.9;">Contact platform administrators for assistance and support</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-users-cog stats-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="section-title">
                                <i class="fas fa-users-cog"></i>Platform Administrators
                            </h4>
                            <p class="text-muted mb-4">Get in touch with our platform administrators for any assistance or queries.</p>

                            <?php if ($result->num_rows > 0): ?>
                                <div class="row">
                                    <?php while ($admin = $result->fetch_assoc()): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="card admin-card">
                                                <div class="card-body">
                                                    <div class="card-header-section">
                                                        <span class="admin-id">ID: <?= $admin['id'] ?></span>
                                                        <img src="../admin/<?= htmlspecialchars($admin['profile_picture']) ?>" 
                                                             alt="<?= htmlspecialchars($admin['username']) ?>" 
                                                             class="admin-avatar"
                                                             onerror="this.src='../uploads/profile_pictures/default.png'">
                                                        <h5 class="admin-name"><?= htmlspecialchars($admin['username']) ?></h5>
                                                        <div class="admin-role">Platform Administrator</div>
                                                    </div>
                                                    
                                                    <div class="contact-info">
                                                        <!-- Email -->
                                                        <div class="contact-item">
                                                            <div class="contact-icon">
                                                                <i class="fas fa-envelope"></i>
                                                            </div>
                                                            <div class="contact-details">
                                                                <div class="contact-label">Email</div>
                                                                <a href="mailto:<?= htmlspecialchars($admin['email']) ?>" class="contact-value contact-link">
                                                                    <?= htmlspecialchars($admin['email']) ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Mobile -->
                                                        <div class="contact-item">
                                                            <div class="contact-icon">
                                                                <i class="fas fa-phone"></i>
                                                            </div>
                                                            <div class="contact-details">
                                                                <div class="contact-label">Mobile</div>
                                                                <a href="tel:<?= htmlspecialchars($admin['mobile']) ?>" class="contact-value contact-link">
                                                                    <?= htmlspecialchars($admin['mobile']) ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Action Buttons -->
                                                    <div class="action-buttons">
                                                        <a href="mailto:<?= htmlspecialchars($admin['email']) ?>" class="btn btn-contact">
                                                            <i class="fas fa-envelope me-1"></i>Email
                                                        </a>
                                                        <a href="tel:<?= htmlspecialchars($admin['mobile']) ?>" class="btn btn-contact">
                                                            <i class="fas fa-phone me-1"></i>Call
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h5>No Administrators Found</h5>
                                    <p class="text-muted">There are currently no active administrators in the system.</p>
                                </div>
                            <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Add any interactive functionality here
            console.log('Admin contacts page loaded');
            
            // Smooth hover effects
            const adminCards = document.querySelectorAll('.admin-card');
            adminCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
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