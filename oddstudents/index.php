<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Optionally log errors to a file for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt'); // Path to your log file


$current_user_id = $_SESSION['former_student_id'];

// Get current user details including course information
$stmt = $conn->prepare("SELECT fs.*, hc.name as course_name 
                       FROM former_students fs 
                       LEFT JOIN hnd_courses hc ON fs.course_id = hc.id 
                       WHERE fs.id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_user_course_id = $user['course_id'] ?? null;
$current_user_course_name = $user['course_name'] ?? '';

// Get current user education + work
$edu_stmt = $conn->prepare("SELECT school, field_of_study, start_year, end_year FROM education WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$edu_stmt->bind_param("i", $current_user_id);
$edu_stmt->execute();
$edu_data = $edu_stmt->get_result()->fetch_assoc();
$edu_stmt->close();

$work_stmt = $conn->prepare("SELECT company, title FROM experiences WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$work_stmt->bind_param("i", $current_user_id);
$work_stmt->execute();
$work_data = $work_stmt->get_result()->fetch_assoc();
$work_stmt->close();

$school = $edu_data['school'] ?? '';
$field = $edu_data['field_of_study'] ?? '';
$start_year = $edu_data['start_year'] ?? '';
$end_year = $edu_data['end_year'] ?? '';
$company = $work_data['company'] ?? '';
$title = $work_data['title'] ?? '';

// Enhanced AI-Like Weighted Matching with Course Priority
$query = "
SELECT 
    fs.id, 
    fs.username AS full_name, 
    fs.profile_picture, 
    fs.facebook, 
    fs.github, 
    fs.linkedin, 
    fs.blog,
    fs.study_year,
    fs.course_id,
    hc.name as course_name,
    e.school, 
    e.field_of_study AS course, 
    w.company AS job_company, 
    w.title AS job_role,
    COUNT(DISTINCT e.id) AS education_count,
    COUNT(DISTINCT w.id) AS experience_count,
    (
        -- High priority: Same course (most important)
        CASE WHEN fs.course_id = ? THEN 80 ELSE 0 END
        +
        -- Medium priority: Same education institution
        CASE WHEN e.school = ? THEN 40 ELSE 0 END
        +
        -- Medium priority: Same company
        CASE WHEN w.company = ? THEN 30 ELSE 0 END
        +
        -- Low priority: Same field of study
        CASE WHEN e.field_of_study = ? THEN 15 ELSE 0 END
        +
        -- Low priority: Same job title
        CASE WHEN w.title = ? THEN 10 ELSE 0 END
        +
        -- Bonus for having multiple education entries
        (COUNT(DISTINCT e.id) * 2) 
        + 
        -- Bonus for having multiple work experiences
        (COUNT(DISTINCT w.id) * 2)
        +
        -- Bonus for same study year
        CASE WHEN fs.study_year = ? THEN 5 ELSE 0 END
    ) AS score
FROM 
    former_students fs
LEFT JOIN 
    education e ON fs.id = e.user_id
LEFT JOIN 
    experiences w ON fs.id = w.user_id
LEFT JOIN
    hnd_courses hc ON fs.course_id = hc.id
WHERE 
    fs.id != ? AND fs.status = 'approved'
GROUP BY 
    fs.id
HAVING 
    score > 0
ORDER BY 
    score DESC, RAND()
LIMIT 20
";

$current_user_study_year = $user['study_year'] ?? '';

$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", 
    $current_user_course_id, 
    $school, 
    $company, 
    $field, 
    $title, 
    $current_user_study_year, 
    $current_user_id
);

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
    
    $suggestions[] = $row;
}
$stmt->close();

// If no suggestions found based on criteria, show random approved users from same course
if (empty($suggestions) && $current_user_course_id) {
    $fallback_query = "
    SELECT 
        fs.id, 
        fs.username AS full_name, 
        fs.profile_picture, 
        fs.facebook, 
        fs.github, 
        fs.linkedin, 
        fs.blog,
        fs.study_year,
        fs.course_id,
        hc.name as course_name,
        (SELECT school FROM education WHERE user_id = fs.id ORDER BY id DESC LIMIT 1) as school,
        (SELECT field_of_study FROM education WHERE user_id = fs.id ORDER BY id DESC LIMIT 1) as course,
        (SELECT company FROM experiences WHERE user_id = fs.id ORDER BY id DESC LIMIT 1) as job_company,
        (SELECT title FROM experiences WHERE user_id = fs.id ORDER BY id DESC LIMIT 1) as job_role
    FROM 
        former_students fs
    LEFT JOIN
        hnd_courses hc ON fs.course_id = hc.id
    WHERE 
        fs.id != ? AND fs.status = 'approved' AND fs.course_id = ?
    ORDER BY RAND()
    LIMIT 100
    ";
    
    $fallback_stmt = $conn->prepare($fallback_query);
    $fallback_stmt->bind_param("ii", $current_user_id, $current_user_course_id);
    $fallback_stmt->execute();
    $fallback_result = $fallback_stmt->get_result();
    
    while ($row = $fallback_result->fetch_assoc()) {
        // Fetch skills for fallback suggestions too
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
        
        $suggestions[] = $row;
    }
    $fallback_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>People You May Know</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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
            min-width: 150px;
        }

        .course-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 5px;
            display: inline-block;
        }

        .match-score {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .card-header {
            position: relative;
            margin-bottom: 15px;
        }

        .study-year {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 3px;
        }

        .no-suggestions {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }

        .no-suggestions i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
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

        .no-skills {
            color: #6c757d;
            font-style: italic;
            font-size: 0.85rem;
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
    </style>
</head>

<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/formers-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>People You May Know</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Connections</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Current User Info Card -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Profile</h5>
                        <div class="d-flex align-items-center">
                            <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile" class="profile-img me-3">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($user['username']) ?></h6>
                                <?php if (!empty($current_user_course_name)): ?>
                                    <span class="course-badge"><?= htmlspecialchars($current_user_course_name) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($user['study_year'])): ?>
                                    <div class="study-year">Study Year: <?= htmlspecialchars($user['study_year']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <?php if (count($suggestions) > 0): ?>
                <div class="col-12">
                    <h5 class="mb-3">Based on your course and profile</h5>
                </div>
                <?php foreach ($suggestions as $person): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="modern-card">
                            <div class="card-header">
                                <?php if (isset($person['score'])): ?>
                                    <div class="match-score" title="Match Score">
                                        <?= min(99, intval($person['score'])) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex align-items-center">
                                    <img src="../oddstudents/<?= htmlspecialchars($person['profile_picture']) ?>" 
                                         alt="Profile" 
                                         class="profile-img me-3"
                                         onerror="this.src='../uploads/profile_pictures/default.png'">
                                    <div>
                                        <h5 class="mb-1"><?= htmlspecialchars($person['full_name']) ?></h5>
                                        <?php if (!empty($person['course_name'])): ?>
                                            <span class="course-badge"><?= htmlspecialchars($person['course_name']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($person['study_year'])): ?>
                                            <div class="study-year">Study Year: <?= htmlspecialchars($person['study_year']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Display -->
                            <?php if (!empty($person['skills'])): ?>
                                <div class="skills-container">
                                    <div class="skills-title">
                                        <i class="fas fa-code me-1"></i>Skills
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
                                            View profile to see all <?= count($person['skills']) ?> skills
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="edu-work-container mb-3">
                                <?php if (!empty($person['job_role'])): ?>
                                    <div>
                                        <h6 class="text-muted mb-1"><i class="bi bi-briefcase-fill"></i> Work</h6>
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
                                        <h6 class="text-muted mb-1"><i class="bi bi-mortarboard"></i> Education</h6>
                                        <p class="mb-0">
                                            <?= htmlspecialchars($person['school']) ?>
                                            <?php if (!empty($person['course'])): ?>
                                                - <?= htmlspecialchars($person['course']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="social-icons">
                                    <?php if (!empty($person['facebook'])): ?>
                                        <a href="<?= htmlspecialchars($person['facebook']) ?>" target="_blank" title="Facebook">
                                            <i class="fab fa-facebook" style="color: #1877F2;"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($person['github'])): ?>
                                        <a href="<?= htmlspecialchars($person['github']) ?>" target="_blank" title="GitHub">
                                            <i class="fab fa-github" style="color: #171515;"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($person['linkedin'])): ?>
                                        <a href="<?= htmlspecialchars($person['linkedin']) ?>" target="_blank" title="LinkedIn">
                                            <i class="fab fa-linkedin" style="color: #0077B5;"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($person['blog'])): ?>
                                        <a href="<?= htmlspecialchars($person['blog']) ?>" target="_blank" title="Blog">
                                            <i class="fas fa-blog" style="color: #fc4f08;"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <a href="former-student-profile.php?former_student_id=<?= $person['id']; ?>" 
                                   class="btn btn-sm btn-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-suggestions">
                        <i class="fas fa-users"></i>
                        <h5>No suggestions found</h5>
                        <p class="text-muted">We couldn't find any matching profiles based on your course and information.</p>
                        <a href="browse-profiles.php" class="btn btn-primary">Browse All Profiles</a>
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

<?php $conn->close(); ?>