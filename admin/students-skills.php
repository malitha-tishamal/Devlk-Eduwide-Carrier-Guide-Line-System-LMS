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

// Fetch filtering parameters from GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$study_year = isset($_GET['study_year']) ? $_GET['study_year'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the SQL query with filters
$sql = "SELECT * FROM students WHERE 1";

// Apply search filter if provided
if ($search !== '') {
    $sql .= " AND (username LIKE '%$search%' OR reg_id LIKE '%$search%')";
}

// Apply study year filter if provided
if ($study_year !== '') {
    $sql .= " AND study_year = '$study_year'";
}

// Apply status filter if provided
if ($status !== '') {
    $sql .= " AND status = '$status'";
}

$result = $conn->query($sql);
$total_students = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Active Students - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --dark-color: #1d3557;
            --light-color: #f8f9fa;
        }

        .student-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 25px;
            height: 100%;
        }

        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .student-card .card-body {
            padding: 25px;
        }

        .student-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin: 0 auto 15px;
            display: block;
        }

        .student-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
            text-align: center;
        }

        .student-reg-id {
            color: var(--primary-color);
            font-weight: 500;
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }

        .stat-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .stat-item:hover .stat-number,
        .stat-item:hover .stat-label {
            color: white;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 2px;
            display: block;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }

        .skills-section {
            margin: 15px 0;
        }

        .skills-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            max-height: 80px;
            overflow-y: auto;
            padding: 5px;
        }

        .skill-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .skill-category {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 0.6rem;
            margin-left: 4px;
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
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            flex-shrink: 0;
            font-size: 0.8rem;
        }

        .contact-details {
            flex: 1;
        }

        .contact-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 1px;
        }

        .contact-value {
            font-size: 0.85rem;
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

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-approved {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-rejected {
            background: #fce4ec;
            color: #c2185b;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .btn-profile {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            flex: 1;
            justify-content: center;
        }

        .btn-profile:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .student-id {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .card-header-section {
            position: relative;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 15px;
        }

        .filters-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: none;
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

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .overview-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .overview-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .overview-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .overview-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .no-skills {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: center;
            font-style: italic;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .student-card .card-body {
                padding: 20px;
            }
            
            .student-avatar {
                width: 70px;
                height: 70px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/company-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Active Students</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item">Talent Pool</li>
                    <li class="breadcrumb-item active">Active Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <!-- Overview Stats -->
                    <div class="stats-overview">
                        <div class="overview-card">
                            <i class="fas fa-users overview-icon"></i>
                            <div class="overview-number"><?= $total_students ?></div>
                            <div class="overview-label">Total Students</div>
                        </div>
                        <div class="overview-card">
                            <i class="fas fa-check-circle overview-icon"></i>
                            <div class="overview-number">
                                <?php
                                $approved_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'approved'")->fetch_assoc()['count'];
                                echo $approved_count;
                                ?>
                            </div>
                            <div class="overview-label">Approved Students</div>
                        </div>
                        <div class="overview-card">
                            <i class="fas fa-clock overview-icon"></i>
                            <div class="overview-number">
                                <?php
                                $pending_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'pending'")->fetch_assoc()['count'];
                                echo $pending_count;
                                ?>
                            </div>
                            <div class="overview-label">Pending Approval</div>
                        </div>
                    </div>

                    <!-- Filters Card -->
                    <div class="card filters-card">
                        <div class="card-body">
                            <h4 class="section-title">
                                <i class="fas fa-filter"></i>Filter Students
                            </h4>
                            <form method="GET" action="">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control" placeholder="Name or Reg ID" value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Study Year</label>
                                        <select name="study_year" class="form-select">
                                            <option value="">All Years</option>
                                            <?php
                                            $current_year = date("Y");
                                            for ($year = 2022; $year <= $current_year + 2; $year++) {
                                                $selected = ($study_year == "$year") ? 'selected' : '';
                                                echo "<option value='$year' $selected>Year $year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Account Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <?php
                                            $statusOptions = ['approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected'];
                                            foreach ($statusOptions as $value => $label) {
                                                $selected = ($status === $value) ? 'selected' : '';
                                                echo "<option value=\"$value\" $selected>$label</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="section-title">
                                <i class="fas fa-user-graduate"></i>Student Profiles
                            </h4>

                            <?php if ($result->num_rows > 0): ?>
                                <div class="row">
                                    <?php while ($student = $result->fetch_assoc()): 
                                        // Fetch projects count for this student
                                        $projects_count = $conn->query("SELECT COUNT(*) as count FROM active_student_projects WHERE student_id = " . $student['id'])->fetch_assoc()['count'];
                                        
                                        // Fetch actual skills for this student
                                        $skills_sql = "
                                            SELECT s.skill_name, s.category 
                                            FROM active_student_skills a 
                                            JOIN (
                                                SELECT id, skill_name, 'IT' AS category FROM it_student_skills
                                                UNION ALL
                                                SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
                                            ) s ON a.skill_id = s.id
                                            WHERE a.student_id = ?
                                            ORDER BY s.category, s.skill_name
                                            LIMIT 8
                                        ";
                                        $skills_stmt = $conn->prepare($skills_sql);
                                        $skills_stmt->bind_param("i", $student['id']);
                                        $skills_stmt->execute();
                                        $skills_result = $skills_stmt->get_result();
                                        $skills = $skills_result->fetch_all(MYSQLI_ASSOC);
                                        $skills_stmt->close();
                                        
                                        $skills_count = count($skills);
                                    ?>
                                        <div class="col-xl-4 col-lg-6 col-md-6">
                                            <div class="card student-card">
                                                <div class="card-body">
                                                    <div class="card-header-section">
                                                        <span class="student-id">ID: <?= $student['id'] ?></span>
                                                        <img src="../<?= htmlspecialchars($student['profile_picture']) ?>" 
                                                             alt="<?= htmlspecialchars($student['username']) ?>" 
                                                             class="student-avatar"
                                                             onerror="this.src='../uploads/profile_pictures/default.png'">
                                                        <h5 class="student-name"><?= htmlspecialchars($student['username']) ?></h5>
                                                        <div class="student-reg-id"><?= htmlspecialchars($student['reg_id']) ?></div>
                                                        <div class="d-flex justify-content-center">
                                                            <span class="status-badge status-<?= $student['status'] ?>">
                                                                <?= $statusOptions[$student['status']] ?? ucfirst($student['status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Stats -->
                                                    <div class="stats-container">
                                                        <div class="stat-item">
                                                            <span class="stat-number"><?= $projects_count ?></span>
                                                            <span class="stat-label">Projects</span>
                                                        </div>
                                                        <div class="stat-item">
                                                            <span class="stat-number"><?= $skills_count ?></span>
                                                            <span class="stat-label">Total Skills</span>
                                                        </div>
                                                        <div class="stat-item">
                                                            <span class="stat-number"><?= htmlspecialchars($student['study_year']) ?></span>
                                                            <span class="stat-label">Study Year</span>
                                                        </div>
                                                        <div class="stat-item">
                                                            <span class="stat-number"><?= htmlspecialchars($student['nic']) ?></span>
                                                            <span class="stat-label">NIC</span>
                                                        </div>
                                                    </div>

                                                    <!-- Skills Section -->
                                                    <div class="skills-section">
                                                        <div class="skills-label">
                                                            <i class="fas fa-code me-1"></i>Key Skills
                                                        </div>
                                                        <div class="skills-container">
                                                            <?php if (!empty($skills)): ?>
                                                                <?php foreach ($skills as $skill): ?>
                                                                    <span class="skill-badge">
                                                                        <?= htmlspecialchars($skill['skill_name']) ?>
                                                                        <span class="skill-category"><?= $skill['category'] ?></span>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <div class="no-skills">No skills added yet</div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Contact Info -->
                                                    <div class="contact-info">
                                                        <!-- Email -->
                                                        <div class="contact-item">
                                                            <div class="contact-icon">
                                                                <i class="fas fa-envelope"></i>
                                                            </div>
                                                            <div class="contact-details">
                                                                <div class="contact-label">Email</div>
                                                                <a href="mailto:<?= htmlspecialchars($student['email']) ?>" class="contact-value contact-link">
                                                                    <?= htmlspecialchars($student['email']) ?>
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
                                                                <a href="tel:<?= htmlspecialchars($student['mobile']) ?>" class="contact-value contact-link">
                                                                    <?= htmlspecialchars($student['mobile']) ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Action Buttons -->
                                                    <div class="action-buttons">
                                                        <a href="student-profile.php?student_id=<?= $student['id'] ?>" class="btn btn-profile">
                                                            <i class="fas fa-eye me-1"></i>View Full Profile
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-user-graduate"></i>
                                    <h5>No Students Found</h5>
                                    <p class="text-muted">No active students match your current filters. Try adjusting your search criteria.</p>
                                    <a href="?" class="btn btn-primary mt-3">
                                        <i class="fas fa-refresh me-2"></i>Clear Filters
                                    </a>
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
            // Add smooth hover effects
            const studentCards = document.querySelectorAll('.student-card');
            studentCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Add animation to stat items
            const statItems = document.querySelectorAll('.stat-item');
            statItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Skills container scrolling
            const skillsContainers = document.querySelectorAll('.skills-container');
            skillsContainers.forEach(container => {
                if (container.scrollHeight > container.clientHeight) {
                    container.style.boxShadow = 'inset 0 -5px 10px -5px rgba(0,0,0,0.1)';
                }
            });

            console.log('Active students page loaded with <?= $result->num_rows ?> students');
        });
    </script>

</body>

</html>

<?php
// Close database connection
$conn->close();
?>