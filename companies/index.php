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

$company_name = $_SESSION['company_name'];

// Get filter values from GET (sanitize)
$education_year = isset($_GET['education_year']) ? intval($_GET['education_year']) : '';
$now_status = isset($_GET['now_status']) ? $_GET['now_status'] : '';

// Enhanced AI Matching Algorithm
class CandidateMatcher {
    private $conn;
    private $company_id;
    
    public function __construct($conn, $company_id) {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }
    
    // Calculate match score (0-100)
    public function calculateMatchScore($candidate, $skills) {
        $totalScore = 0;
        $maxPossibleScore = 0;
        
        // 1. Education Match (20 points) - FIXED
        $education_score = $this->calculateEducationScore($candidate);
        $totalScore += $education_score;
        $maxPossibleScore += 20;
        
        // 2. Skills Match (30 points)
        $skills_score = $this->calculateSkillsScore($skills);
        $totalScore += $skills_score;
        $maxPossibleScore += 30;
        
        // 3. Experience Level (25 points)
        $experience_score = $this->calculateExperienceScore($candidate);
        $totalScore += $experience_score;
        $maxPossibleScore += 25;
        
        // 4. Profile Completeness (15 points)
        $profile_score = $this->calculateProfileCompleteness($candidate, $skills);
        $totalScore += $profile_score;
        $maxPossibleScore += 15;
        
        // 5. Social Presence (10 points) - FIXED
        $social_score = $this->calculateSocialScore($candidate);
        $totalScore += $social_score;
        $maxPossibleScore += 10;
        
        // Normalize to 0-100 scale
        $finalScore = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
        
        return [
            'score' => round($finalScore, 1),
            'breakdown' => [
                'education' => $education_score,
                'skills' => $skills_score,
                'experience' => $experience_score,
                'profile' => $profile_score,
                'social' => $social_score
            ]
        ];
    }
    
    private function calculateEducationScore($candidate) {
        $score = 0;
        
        // 1. Education Year Relevance (20 points)
        if (!empty($candidate['start_year'])) {
            $current_year = date("Y");
            $years_since_education = $current_year - $candidate['start_year'];
            
            // Improved calculation - more balanced approach
            if ($years_since_education <= 2) {
                $score = 20; // Very recent
            } elseif ($years_since_education <= 5) {
                $score = 15; // Recent
            } elseif ($years_since_education <= 10) {
                $score = 10; // Somewhat recent
            } else {
                $score = 5;  // Experienced but older education
            }
        } else {
            // If no start year but has education info, give some points
            if (!empty($candidate['school']) || !empty($candidate['course'])) {
                $score = 8; // Basic points for having education info
            }
        }
        
        // 2. Education Level Bonus
        if (!empty($candidate['course'])) {
            $course = strtolower($candidate['course']);
            if (strpos($course, 'master') !== false || 
                strpos($course, 'phd') !== false ||
                strpos($course, 'postgraduate') !== false) {
                $score += 5;
            } elseif (strpos($course, 'bachelor') !== false || 
                     strpos($course, 'degree') !== false) {
                $score += 3;
            } elseif (strpos($course, 'diploma') !== false || 
                     strpos($course, 'hnd') !== false) {
                $score += 2;
            }
        }
        
        return min(20, $score);
    }
    
    private function calculateSkillsScore($skills) {
        if (empty($skills)) return 0;
        
        // Different skill categories have different weights
        $category_weights = [
            'IT' => 1.2,
            'Engineering' => 1.2,
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
        
        foreach ($skills as $skill) {
            $category = $skill['category'];
            $weight = $category_weights[$category] ?? 1.0;
            $weighted_skill_count += $weight;
            
            if (!in_array($category, $unique_categories)) {
                $unique_categories[] = $category;
            }
        }
        
        // Bonus for diverse skill categories
        $diversity_bonus = min(5, count($unique_categories) * 0.5);
        
        // Normalize to 30 points maximum
        $base_score = min(25, $weighted_skill_count * 1.5);
        
        return min(30, $base_score + $diversity_bonus);
    }
    
    private function calculateExperienceScore($candidate) {
        $score = 0;
        
        // Job role complexity analysis
        if (!empty($candidate['job_role'])) {
            $role = strtolower($candidate['job_role']);
            
            // Senior roles get higher scores
            if (strpos($role, 'senior') !== false || 
                strpos($role, 'lead') !== false || 
                strpos($role, 'manager') !== false ||
                strpos($role, 'director') !== false) {
                $score += 15;
            }
            // Mid-level roles
            elseif (strpos($role, 'developer') !== false || 
                   strpos($role, 'engineer') !== false || 
                   strpos($role, 'analyst') !== false ||
                   strpos($role, 'specialist') !== false) {
                $score += 10;
            }
            // Entry-level or intern
            elseif (strpos($role, 'junior') !== false || 
                   strpos($role, 'intern') !== false ||
                   strpos($role, 'trainee') !== false) {
                $score += 5;
            }
            else {
                $score += 8; // Default for other roles
            }
            
            // Bonus for known company
            if (!empty($candidate['job_company']) && strlen($candidate['job_company']) > 3) {
                $score += 5;
            }
        }
        
        // Education level consideration
        if (!empty($candidate['course'])) {
            $course = strtolower($candidate['course']);
            if (strpos($course, 'master') !== false || 
                strpos($course, 'phd') !== false ||
                strpos($course, 'postgraduate') !== false) {
                $score += 5;
            }
        }
        
        return min(25, $score);
    }
    
    private function calculateProfileCompleteness($candidate, $skills) {
        $completeness = 0;
        
        // Profile picture
        if (!empty($candidate['profile_picture']) && $candidate['profile_picture'] != 'default.png') {
            $completeness += 2;
        }
        
        // Education info
        if (!empty($candidate['school']) && !empty($candidate['course'])) {
            $completeness += 4;
        } elseif (!empty($candidate['school']) || !empty($candidate['course'])) {
            $completeness += 2; // Partial points
        }
        
        // Work experience
        if (!empty($candidate['job_role'])) {
            $completeness += 4;
        }
        
        // Skills
        if (!empty($skills)) {
            $completeness += 3;
        }
        
        // Social links (at least one)
        $hasSocial = !empty($candidate['linkedin']) || !empty($candidate['github']) || 
                    !empty($candidate['facebook']) || !empty($candidate['blog']);
        if ($hasSocial) {
            $completeness += 2;
        }
        
        return $completeness;
    }
    
    private function calculateSocialScore($candidate) {
        $score = 0;
        
        // LinkedIn is most valuable for professional matching
        if (!empty($candidate['linkedin'])) {
            // Check if it's a valid URL, not just empty string
            if (filter_var($candidate['linkedin'], FILTER_VALIDATE_URL) !== false) {
                $score += 4;
            } else if (trim($candidate['linkedin']) !== '') {
                $score += 2; // Partial points for non-URL but not empty
            }
        }
        
        // GitHub for technical roles
        if (!empty($candidate['github'])) {
            if (filter_var($candidate['github'], FILTER_VALIDATE_URL) !== false) {
                $score += 3;
            } else if (trim($candidate['github']) !== '') {
                $score += 1;
            }
        }
        
        // Blog/portfolio
        if (!empty($candidate['blog'])) {
            if (filter_var($candidate['blog'], FILTER_VALIDATE_URL) !== false) {
                $score += 2;
            } else if (trim($candidate['blog']) !== '') {
                $score += 1;
            }
        }
        
        // Facebook
        if (!empty($candidate['facebook'])) {
            if (filter_var($candidate['facebook'], FILTER_VALIDATE_URL) !== false) {
                $score += 1;
            }
        }
        
        return min(10, $score);
    }
}

// Initialize matcher
$matcher = new CandidateMatcher($conn, $user_id);

// Step 1: Get total number of former students
$countQuery = "SELECT COUNT(*) as total FROM former_students";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();
$totalFormerStudents = $rowCount['total'];
$stmtCount->close();

// Base query parts - FIXED to ensure we get education data
$select = "
    SELECT 
        fs.id,
        fs.username AS full_name,
        fs.profile_picture,
        fs.facebook,
        fs.github,
        fs.linkedin,
        fs.blog,
        fs.study_year,
        e.school,
        e.field_of_study AS course,
        e.start_year,
        w.company AS job_company,
        w.title AS job_role,
        MAX(
            CASE 
                WHEN w.title IS NOT NULL THEN 1     
                WHEN e.school IS NOT NULL THEN 2     
                ELSE 3                           
            END
        ) AS priority
    FROM former_students fs
    LEFT JOIN education e ON fs.id = e.user_id
    LEFT JOIN experiences w ON fs.id = w.user_id
";

// Filters array for prepared statement binding
$where = [];
$params = [];
$paramTypes = "";

// Filter: Education Year
if ($education_year) {
    $where[] = "e.start_year = ?";
    $params[] = $education_year;
    $paramTypes .= "i";
}

// Filter: Now Status (work, study, intern, free)
if ($now_status) {
    if ($now_status === 'work') {
        $where[] = "w.title IS NOT NULL";
    } elseif ($now_status === 'study') {
        $where[] = "e.school IS NOT NULL AND w.title IS NULL";
    } elseif ($now_status === 'intern') {
        $where[] = "LOWER(w.title) LIKE '%intern%'";
    } elseif ($now_status === 'free') {
        $where[] = "e.school IS NULL AND w.title IS NULL";
    }
}

// Build WHERE clause if any filters
$whereSQL = "";
if (count($where) > 0) {
    $whereSQL = " WHERE " . implode(" AND ", $where);
}

// Modified query to ensure we get candidates with data
if ($totalFormerStudents > 100) {
    if ($whereSQL) {
        $whereSQL .= " AND (e.school IS NOT NULL OR w.title IS NOT NULL)";
    } else {
        $whereSQL = " WHERE (e.school IS NOT NULL OR w.title IS NOT NULL)";
    }
    $query = $select . $whereSQL . " GROUP BY fs.id ORDER BY fs.username ASC LIMIT 150";
} else {
    $query = $select . $whereSQL . " GROUP BY fs.id ORDER BY priority ASC, fs.username ASC LIMIT 150";
}

$stmt = $conn->prepare($query);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    // Fetch skills for this person
    $skills_sql = "
        SELECT s.skill_name, s.category
        FROM former_student_skills fss
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
        ) s ON fss.skill_id = s.id
        WHERE fss.student_id = ?
        ORDER BY s.category, s.skill_name
    ";
    $skills_stmt = $conn->prepare($skills_sql);
    $skills_stmt->bind_param("i", $row['id']);
    $skills_stmt->execute();
    $skills_result = $skills_stmt->get_result();
    $row['skills'] = $skills_result->fetch_all(MYSQLI_ASSOC);
    $skills_stmt->close();
    
    // Calculate AI match score with FIXED functions
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AI-Powered Candidate Matching - EduWide</title>
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

        .edu-work-container {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .edu-work-container div {
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

        .priority-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
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
    </style>
</head>

<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/company-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>AI-Powered Candidate Matching <span class="ai-badge">AI Powered</span></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Smart Connections</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Smart Candidate Filtering</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="education_year" class="form-label">Education Year</label>
                        <select name="education_year" id="education_year" class="form-select">
                            <option value="">All Years</option>
                            <?php
                            $current_year = date("Y");
                            for ($year = 2000; $year <= $current_year + 2; $year++) {
                                $selected = ($education_year == $year) ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="now_status" class="form-label">Current Status</label>
                        <select name="now_status" id="now_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="study" <?= ($now_status === 'study') ? 'selected' : '' ?>>Studying</option>
                            <option value="work" <?= ($now_status === 'work') ? 'selected' : '' ?>>Working</option>
                            <option value="intern" <?= ($now_status === 'intern') ? 'selected' : '' ?>>Internship</option>
                            <option value="free" <?= ($now_status === 'free') ? 'selected' : '' ?>>Available</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-robot me-2"></i>AI Match Candidates
                        </button>
                    </div>
                </form>

                <!-- Active Filters Display -->
                <?php if ($education_year || $now_status): ?>
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Active Filters:</h6>
                        <?php if ($education_year): ?>
                            <span class="filter-badge">
                                <i class="fas fa-graduation-cap"></i>
                                Education Year: <?= $education_year ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($now_status): ?>
                            <span class="filter-badge">
                                <i class="fas fa-user-check"></i>
                                Status: <?= ucfirst($now_status) ?>
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
                    <h4 class="mb-1">AI-Matched <?= count($suggestions) ?> Candidates</h4>
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
                <?php foreach ($suggestions as $person): 
                    // Determine score color class
                    $score_class = 'score-excellent';
                    if ($person['match_score'] < 80) $score_class = 'score-good';
                    if ($person['match_score'] < 60) $score_class = 'score-average';
                    if ($person['match_score'] < 40) $score_class = 'score-poor';
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="modern-card">
                            <!-- AI Match Score -->
                            <div class="match-score <?= $score_class ?>">
                                <?= $person['match_score'] ?>%
                            </div>

                            <!-- Priority Badge -->
                            <?php if ($person['priority'] == 1): ?>
                                <span class="badge bg-success priority-badge">Work Experience</span>
                            <?php elseif ($person['priority'] == 2): ?>
                                <span class="badge bg-info priority-badge">Education</span>
                            <?php else: ?>
                                <span class="badge bg-secondary priority-badge">Basic Profile</span>
                            <?php endif; ?>

                            <!-- Candidate Header -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="../oddstudents/<?= htmlspecialchars($person['profile_picture']) ?>" 
                                     alt="Profile" 
                                     class="profile-img me-3"
                                     onerror="this.src='../uploads/profile_pictures/default.png'">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($person['full_name']) ?></h5>
                                    
                                    <!-- Study Year -->
                                    <?php if (!empty($person['study_year'])): ?>
                                        <div class="mt-1">
                                            <span class="study-year-badge">Batch: <?= htmlspecialchars($person['study_year']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="../admin/former-student-profile.php?former_student_id=<?= $person['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                        View Full Profile
                                    </a>
                                </div>
                            </div>

                            <!-- Score Breakdown -->
                            <!--div class="score-breakdown">
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Education Match:</span>
                                    <span class="score-breakdown-value"><?= $person['score_breakdown']['education'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Skills Match:</span>
                                    <span class="score-breakdown-value"><?= $person['score_breakdown']['skills'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Experience Level:</span>
                                    <span class="score-breakdown-value"><?= $person['score_breakdown']['experience'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Profile Quality:</span>
                                    <span class="score-breakdown-value"><?= $person['score_breakdown']['profile'] ?> pts</span>
                                </div>
                                <div class="score-breakdown-item">
                                    <span class="score-breakdown-label">Social Presence:</span>
                                    <span class="score-breakdown-value"><?= $person['score_breakdown']['social'] ?> pts</span>
                                </div>
                            </div-->

                            <!-- Skills Display -->
                            <?php if (!empty($person['skills'])): ?>
                                <div class="skills-container">
                                    <div class="skills-title">
                                        <i class="fas fa-code me-1"></i>Top Skills
                                    </div>
                                    <div class="skills-list">
                                        <?php 
                                        // Show only first 5 skills
                                        $displaySkills = array_slice($person['skills'], 0, 5);
                                        foreach ($displaySkills as $skill): ?>
                                            <span class="skill-tag"><?= htmlspecialchars($skill['skill_name']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($person['skills']) > 5): ?>
                                        <div class="more-skills-text">
                                            +<?= count($person['skills']) - 5 ?> more skills in profile
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Education & Work Info -->
                            <div class="edu-work-container mb-3">
                                <?php if (!empty($person['job_role'])): ?>
                                    <div>
                                        <h6 class="text-muted mb-1"><i class="fas fa-briefcase me-1"></i>Work</h6>
                                        <p class="mb-0">
                                            <strong><?= htmlspecialchars($person['job_role']) ?></strong>
                                            <?php if (!empty($person['job_company'])): ?>
                                                at <?= htmlspecialchars($person['job_company']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($person['school'])): ?>
                                    <div>
                                        <h6 class="text-muted mb-1"><i class="fas fa-graduation-cap me-1"></i>Education</h6>
                                        <p class="mb-0">
                                            <?= htmlspecialchars($person['school']) ?>
                                            <?php if (!empty($person['course'])): ?>
                                                - <?= htmlspecialchars($person['course']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($person['start_year'])): ?>
                                                <small class="text-muted">(<?= htmlspecialchars($person['start_year']) ?>)</small>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Social Media Links -->
                            <div class="social-icons">
                                <?php if (!empty($person['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($person['linkedin']) ?>" target="_blank" title="LinkedIn">
                                        <i class="fab fa-linkedin" style="color: #0077B5;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($person['github'])): ?>
                                    <a href="<?= htmlspecialchars($person['github']) ?>" target="_blank" title="GitHub">
                                        <i class="fab fa-github" style="color:#171515;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($person['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($person['facebook']) ?>" target="_blank" title="Facebook">
                                        <i class="fab fa-facebook" style="color: #1877F2;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($person['blog'])): ?>
                                    <a href="<?= htmlspecialchars($person['blog']) ?>" target="_blank" title="Blog">
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
                        <i class="fas fa-robot"></i>
                        <h4>No AI-matched candidates found</h4>
                        <p class="text-muted mb-4">Try adjusting your search filters to find better matches.</p>
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