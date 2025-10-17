<?php
session_start();
require_once '../includes/db-conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$company_name = $_SESSION['company_name'];

// Get filter values from GET (sanitize)
$study_year = isset($_GET['study_year']) ? intval($_GET['study_year']) : '';
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : '';

// Enhanced AI Matching Algorithm for Active Students
class ActiveStudentMatcher {
    private $conn;
    private $company_id;
    
    public function __construct($conn, $company_id) {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }
    
    // Calculate match score (0-100)
    public function calculateMatchScore($student, $skills) {
        $totalScore = 0;
        $maxPossibleScore = 0;
        
        // 1. Academic Performance (25 points)
        $academic_score = $this->calculateAcademicScore($student);
        $totalScore += $academic_score;
        $maxPossibleScore += 25;
        
        // 2. Skills Match (35 points)
        $skills_score = $this->calculateSkillsScore($skills);
        $totalScore += $skills_score;
        $maxPossibleScore += 35;
        
        // 3. Profile Completeness (20 points)
        $profile_score = $this->calculateProfileCompleteness($student, $skills);
        $totalScore += $profile_score;
        $maxPossibleScore += 20;
        
        // 4. Social & Professional Presence (20 points)
        $social_score = $this->calculateSocialScore($student);
        $totalScore += $social_score;
        $maxPossibleScore += 20;
        
        // Normalize to 0-100 scale
        $finalScore = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
        
        return [
            'score' => round($finalScore, 1),
            'breakdown' => [
                'academic' => $academic_score,
                'skills' => $skills_score,
                'profile' => $profile_score,
                'social' => $social_score
            ]
        ];
    }
    
    private function calculateAcademicScore($student) {
        $score = 0;
        
        // Study Year Relevance (15 points)
        if (!empty($student['study_year'])) {
            $current_year = date("Y");
            $study_year = $student['study_year'];
            
            // Higher years get more points (closer to graduation)
            if ($study_year >= ($current_year + 1)) {
                $score = 15; // Final year or beyond
            } elseif ($study_year == $current_year) {
                $score = 12; // Current year
            } elseif ($study_year == ($current_year - 1)) {
                $score = 8;  // Previous year
            } else {
                $score = 5;  // Older batches
            }
        }
        
        // Course Relevance Bonus (10 points)
        if (!empty($student['course_name'])) {
            $course = strtolower($student['course_name']);
            
            // Technical courses get higher weights
            if (strpos($course, 'computer') !== false || 
                strpos($course, 'it') !== false ||
                strpos($course, 'software') !== false ||
                strpos($course, 'engineering') !== false) {
                $score += 8;
            } elseif (strpos($course, 'business') !== false || 
                     strpos($course, 'management') !== false ||
                     strpos($course, 'finance') !== false) {
                $score += 6;
            } else {
                $score += 4; // Other courses
            }
        }
        
        return min(25, $score);
    }
    
    private function calculateSkillsScore($skills) {
        if (empty($skills)) return 0;
        
        // Different skill categories have different weights for students
        $category_weights = [
            'IT' => 1.3,
            'Engineering' => 1.3,
            'Business Finance' => 1.1,
            'HND Management' => 1.1,
            'HND Business Admin' => 1.0,
            'HND Accountancy' => 1.0,
            'HND Agriculture' => 0.9,
            'HND Building Services' => 0.9,
            'HND English' => 0.8,
            'HND Food Tech' => 0.9,
            'HND Mechanical' => 1.0,
            'HND Quantity Survey' => 0.9,
            'HND THM' => 0.8
        ];
        
        $weighted_skill_count = 0;
        $unique_categories = [];
        $technical_skill_count = 0;
        
        foreach ($skills as $skill) {
            $category = $skill['category'];
            $weight = $category_weights[$category] ?? 1.0;
            $weighted_skill_count += $weight;
            
            // Count technical skills separately
            if ($category === 'IT' || $category === 'Engineering') {
                $technical_skill_count++;
            }
            
            if (!in_array($category, $unique_categories)) {
                $unique_categories[] = $category;
            }
        }
        
        // Bonus for diverse skill categories
        $diversity_bonus = min(5, count($unique_categories) * 0.5);
        
        // Bonus for technical skills
        $technical_bonus = min(3, $technical_skill_count * 0.5);
        
        // Normalize to 35 points maximum
        $base_score = min(27, $weighted_skill_count * 1.8);
        
        return min(35, $base_score + $diversity_bonus + $technical_bonus);
    }
    
    private function calculateProfileCompleteness($student, $skills) {
        $completeness = 0;
        
        // Profile picture (3 points)
        if (!empty($student['profile_picture']) && $student['profile_picture'] != 'default.png') {
            $completeness += 3;
        }
        
        // Contact information (4 points)
        if (!empty($student['email'])) {
            $completeness += 2;
        }
        if (!empty($student['mobile'])) {
            $completeness += 2;
        }
        
        // Academic info (5 points)
        if (!empty($student['course_name'])) {
            $completeness += 3;
        }
        if (!empty($student['study_year'])) {
            $completeness += 2;
        }
        
        // Skills (4 points)
        if (!empty($skills)) {
            $completeness += 4;
        }
        
        // Registration info (4 points)
        if (!empty($student['reg_id'])) {
            $completeness += 2;
        }
        if (!empty($student['nic'])) {
            $completeness += 2;
        }
        
        return min(20, $completeness);
    }
    
    private function calculateSocialScore($student) {
        $score = 0;
        
        // LinkedIn is most valuable for professional matching
        if (!empty($student['linkedin'])) {
            if (filter_var($student['linkedin'], FILTER_VALIDATE_URL) !== false) {
                $score += 6;
            } else if (trim($student['linkedin']) !== '') {
                $score += 3;
            }
        }
        
        // GitHub for technical roles
        if (!empty($student['github'])) {
            if (filter_var($student['github'], FILTER_VALIDATE_URL) !== false) {
                $score += 5;
            } else if (trim($student['github']) !== '') {
                $score += 2;
            }
        }
        
        // Blog/portfolio
        if (!empty($student['blog'])) {
            if (filter_var($student['blog'], FILTER_VALIDATE_URL) !== false) {
                $score += 4;
            } else if (trim($student['blog']) !== '') {
                $score += 2;
            }
        }
        
        // Facebook
        if (!empty($student['facebook'])) {
            if (filter_var($student['facebook'], FILTER_VALIDATE_URL) !== false) {
                $score += 3;
            } else if (trim($student['facebook']) !== '') {
                $score += 1;
            }
        }
        
        // Email presence bonus
        if (!empty($student['email'])) {
            $score += 2;
        }
        
        return min(20, $score);
    }
}

// Initialize matcher
$matcher = new ActiveStudentMatcher($conn, $user_id);

// Get total number of active students
$countQuery = "SELECT COUNT(*) as total FROM students WHERE status = 'approved'";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();
$totalActiveStudents = $rowCount['total'];
$stmtCount->close();

// Base query for active students
$select = "
    SELECT 
        s.id,
        s.username AS full_name,
        s.profile_picture,
        s.facebook,
        s.github,
        s.linkedin,
        s.blog,
        s.study_year,
        s.email,
        s.mobile,
        s.nic,
        s.reg_id,
        c.course_name,
        c.id as course_id
    FROM students s
    LEFT JOIN courses c ON s.course_id = c.id
    WHERE s.status = 'approved'
";

// Filters array for prepared statement binding
$where = [];
$params = [];
$paramTypes = "";

// Filter: Study Year
if ($study_year) {
    $where[] = "s.study_year = ?";
    $params[] = $study_year;
    $paramTypes .= "i";
}

// Filter: Course
if ($course_id) {
    $where[] = "s.course_id = ?";
    $params[] = $course_id;
    $paramTypes .= "i";
}

// Build WHERE clause if any filters
$whereSQL = "";
if (count($where) > 0) {
    $whereSQL = " AND " . implode(" AND ", $where);
}

// Final query with filters
$query = $select . $whereSQL . " ORDER BY s.study_year DESC, s.username ASC LIMIT 150";

$stmt = $conn->prepare($query);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    // Fetch skills for this student
    $skills_sql = "
        SELECT s.skill_name, s.category
        FROM active_student_skills ass
        JOIN (
            SELECT id, skill_name, 'Business Finance' AS category FROM business_finance_skills
            UNION ALL
            SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
            UNION ALL
            SELECT id, skill_name, 'HND Accountancy' AS category FROM hnd_accountancy_skills
            UNION ALL
            SELECT id, skill_name, 'HND Agriculture' AS category FROM hnd_agriculture_skills
            UNION ALL
            SELECT id, skill_name, 'HND Building Services' AS category FROM hnd_building_services_skills
            UNION ALL
            SELECT id, skill_name, 'HND Business Admin' AS category FROM hnd_business_admin_skills
            UNION ALL
            SELECT id, skill_name, 'HND English' AS category FROM hnd_english_skills
            UNION ALL
            SELECT id, skill_name, 'HND Food Tech' AS category FROM hnd_food_tech_skills
            UNION ALL
            SELECT id, skill_name, 'HND Management' AS category FROM hnd_management_skills
            UNION ALL
            SELECT id, skill_name, 'HND Mechanical' AS category FROM hnd_mechanical_skills
            UNION ALL
            SELECT id, skill_name, 'HND Quantity Survey' AS category FROM hnd_quantity_survey_skills
            UNION ALL
            SELECT id, skill_name, 'HND THM' AS category FROM hnd_thm_skills
            UNION ALL
            SELECT id, skill_name, 'IT' AS category FROM it_student_skills
        ) s ON ass.skill_id = s.id
        WHERE ass.student_id = ?
        ORDER BY s.category, s.skill_name
    ";
    $skills_stmt = $conn->prepare($skills_sql);
    $skills_stmt->bind_param("i", $row['id']);
    $skills_stmt->execute();
    $skills_result = $skills_stmt->get_result();
    $row['skills'] = $skills_result->fetch_all(MYSQLI_ASSOC);
    $skills_stmt->close();
    
    // Calculate AI match score
    $matchResult = $matcher->calculateMatchScore($row, $row['skills']);
    $row['match_score'] = $matchResult['score'];
    $row['score_breakdown'] = $matchResult['breakdown'];
    
    $suggestions[] = $row;
}
$stmt->close();

// Sort suggestions by match score (highest first)
usort($suggestions, function($a, $b) {
    return $b['match_score'] - $a['match_score'];
});

// Limit to top 100 candidates after sorting
$suggestions = array_slice($suggestions, 0, 100);

// Get courses for filter dropdown
$courses_sql = "SELECT id, course_name FROM courses ORDER BY course_name";
$courses_result = $conn->query($courses_sql);
$courses = [];
while ($course = $courses_result->fetch_assoc()) {
    $courses[] = $course;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AI-Powered Active Student Matching - EduWide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("../includes/css-links-inc.php"); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .modern-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            margin-bottom: 20px;
            position: relative;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #0d6efd;
        }

        .social-icons a {
            color: #444;
            margin-right: 12px;
            transition: color 0.2s;
            text-decoration: none;
        }

        .social-icons a:hover {
            color: #0d6efd;
        }

        .edu-info-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .edu-info-container div {
            flex: 1;
        }

        /* Skills Section Styles */
        .skills-container {
            margin: 15px 0;
        }

        .skill-tag {
            display: inline-block;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            margin: 2px;
            margin-bottom: 5px;
        }

        .skills-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .more-skills-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
            font-style: italic;
        }

        .study-year-badge {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 5px;
        }

        .course-badge {
            background: linear-gradient(135deg, #20c997, #198754);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 5px;
        }

        /* AI Match Score Styles */
        .match-score {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            z-index: 10;
        }

        .score-excellent { background: linear-gradient(135deg, #28a745, #20c997); }
        .score-good { background: linear-gradient(135deg, #17a2b8, #0dcaf0); }
        .score-average { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .score-poor { background: linear-gradient(135deg, #dc3545, #e83e8c); }

        .score-breakdown {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
            margin-top: 10px;
            font-size: 0.8rem;
        }

        .score-breakdown-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .score-breakdown-label {
            color: #6c757d;
        }

        .score-breakdown-value {
            font-weight: 500;
            color: #495057;
        }

        .ai-badge {
            background: linear-gradient(135deg, #6f42c1, #e83e8c);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            margin-left: 5px;
        }

        .filter-badge {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-flex;
            align-items: center;
        }

        .filter-badge i {
            margin-right: 5px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
        }

        .no-results i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .match-quality {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.8rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .contact-item i {
            width: 16px;
            margin-right: 8px;
            color: #6c757d;
        }
    </style>
</head>

<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/company-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>AI-Powered Active Student Matching <span class="ai-badge">AI Powered</span></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Current Students</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Smart Student Filtering</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="study_year" class="form-label">Study Year</label>
                        <select name="study_year" id="study_year" class="form-select">
                            <option value="">All Years</option>
                            <?php
                            $current_year = date("Y");
                            for ($year = 2020; $year <= $current_year + 2; $year++) {
                                $selected = ($study_year == $year) ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" <?= ($course_id == $course['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($course['course_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-robot me-2"></i>AI Match Students
                        </button>
                    </div>
                </form>

                <!-- Active Filters Display -->
                <?php if ($study_year || $course_id): ?>
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Active Filters:</h6>
                        <?php if ($study_year): ?>
                            <span class="filter-badge">
                                <i class="fas fa-calendar-alt"></i>
                                Study Year: <?= $study_year ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($course_id): 
                            $course_name = '';
                            foreach ($courses as $course) {
                                if ($course['id'] == $course_id) {
                                    $course_name = $course['course_name'];
                                    break;
                                }
                            }
                        ?>
                            <span class="filter-badge">
                                <i class="fas fa-graduation-cap"></i>
                                Course: <?= $course_name ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Results Header -->
        <?php if (count($suggestions) > 0): ?>
        <div class="results-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">AI-Matched <?= count($suggestions) ?> Active Students</h4>
                    <p class="mb-0 match-quality">Sorted by AI Match Score • Best matches first</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark rounded-pill px-3 py-2 d-inline-block">
                        <small><strong>Match Quality:</strong> 
                            <span class="text-success">Excellent (80%+)</span> • 
                            <span class="text-info">Good (60-79%)</span> • 
                            <span class="text-warning">Average (40-59%)</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Results Section -->
        <div class="row">
            <?php if (count($suggestions) > 0): ?>
                <?php foreach ($suggestions as $student): 
                    // Determine score color class
                    $score_class = 'score-excellent';
                    if ($student['match_score'] < 80) $score_class = 'score-good';
                    if ($student['match_score'] < 60) $score_class = 'score-average';
                    if ($student['match_score'] < 40) $score_class = 'score-poor';
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="modern-card">
                            <!-- AI Match Score -->
                            <div class="match-score <?= $score_class ?>">
                                <?= $student['match_score'] ?>%
                            </div>

                            <!-- Student Header -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="../oddstudents/<?= htmlspecialchars($student['profile_picture']) ?>" 
                                     alt="Profile" 
                                     class="profile-img me-3"
                                     onerror="this.src='../uploads/profile_pictures/default.png'">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($student['full_name']) ?></h5>
                                    
                                    <!-- Study Year & Course -->
                                    <div class="mt-1">
                                        <?php if (!empty($student['study_year'])): ?>
                                            <span class="study-year-badge">Batch: <?= htmlspecialchars($student['study_year']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($student['course_name'])): ?>
                                            <span class="course-badge ms-1"><?= htmlspecialchars($student['course_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Registration ID -->
                                    <?php if (!empty($student['reg_id'])): ?>
                                        <small class="text-muted d-block mt-1">ID: <?= htmlspecialchars($student['reg_id']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="contact-info">
                                <?php if (!empty($student['email'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= htmlspecialchars($student['email']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($student['mobile'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?= htmlspecialchars($student['mobile']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Score Breakdown -->
                            <div class="score-breakdown">
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Academic Potential:</span>
                                    <span class="score-breakdown-value"><?= $student['score_breakdown']['academic'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Skills Match:</span>
                                    <span class="score-breakdown-value"><?= $student['score_breakdown']['skills'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Profile Quality:</span>
                                    <span class="score-breakdown-value"><?= $student['score_breakdown']['profile'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Professional Presence:</span>
                                    <span class="score-breakdown-value"><?= $student['score_breakdown']['social'] ?> pts</span>
                                </div>
                            </div>

                            <!-- Skills Display -->
                            <?php if (!empty($student['skills'])): ?>
                                <div class="skills-container">
                                    <div class="skills-title">
                                        <i class="fas fa-code me-1"></i>Top Skills
                                    </div>
                                    <div class="skills-list">
                                        <?php 
                                        // Show only first 6 skills
                                        $displaySkills = array_slice($student['skills'], 0, 6);
                                        foreach ($displaySkills as $skill): ?>
                                            <span class="skill-tag"><?= htmlspecialchars($skill['skill_name']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($student['skills']) > 6): ?>
                                        <div class="more-skills-text">
                                            +<?= count($student['skills']) - 6 ?> more skills
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Social Media Links -->
                            <div class="social-icons mt-3">
                                <?php if (!empty($student['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($student['linkedin']) ?>" target="_blank" title="LinkedIn">
                                        <i class="fab fa-linkedin" style="color: #0077B5;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($student['github'])): ?>
                                    <a href="<?= htmlspecialchars($student['github']) ?>" target="_blank" title="GitHub">
                                        <i class="fab fa-github" style="color:#171515;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($student['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($student['facebook']) ?>" target="_blank" title="Facebook">
                                        <i class="fab fa-facebook" style="color: #1877F2;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($student['blog'])): ?>
                                    <a href="<?= htmlspecialchars($student['blog']) ?>" target="_blank" title="Blog">
                                        <i class="fas fa-blog" style="color: #fc4f08;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-results">
                        <i class="fas fa-user-graduate"></i>
                        <h4>No active students found</h4>
                        <p class="text-muted mb-4">Try adjusting your search filters to find students.</p>
                        <a href="?" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo me-2"></i>Reset All Filters
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>

<?php
$conn->close();
?>