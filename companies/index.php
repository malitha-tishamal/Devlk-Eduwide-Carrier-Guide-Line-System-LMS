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

// Step 1: Get total number of former students
$countQuery = "SELECT COUNT(*) as total FROM former_students";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();
$totalFormerStudents = $rowCount['total'];
$stmtCount->close();

// Base query parts
$select = "
    SELECT 
        fs.id,
        fs.username AS full_name,
        fs.profile_picture,
        fs.facebook,
        fs.github,
        fs.linkedin,
        fs.blog,
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
        // Must have a job title in experiences
        $where[] = "w.title IS NOT NULL";
    } elseif ($now_status === 'study') {
        // Must have education and no work (studying)
        $where[] = "e.school IS NOT NULL AND w.title IS NULL";
    } elseif ($now_status === 'intern') {
        // Work title contains 'intern' (case insensitive)
        $where[] = "LOWER(w.title) LIKE '%intern%'";
    } elseif ($now_status === 'free') {
        // No education and no work (free)
        $where[] = "e.school IS NULL AND w.title IS NULL";
    }
}

// Build WHERE clause if any filters
$whereSQL = "";
if (count($where) > 0) {
    $whereSQL = " WHERE " . implode(" AND ", $where);
}

// Step 2 & 3: Final query with filters, grouping and ordering
if ($totalFormerStudents > 100) {
    // For > 100 students, only show those with education, limit 100
    // Add filter for education presence also to keep logic consistent
    if ($whereSQL) {
        $whereSQL .= " AND e.school IS NOT NULL";
    } else {
        $whereSQL = " WHERE e.school IS NOT NULL";
    }

    $query = $select . $whereSQL . " GROUP BY fs.id ORDER BY fs.username ASC LIMIT 100";
} else {
    // For <= 100 students, get all with filters and priority sorting
    $query = $select . $whereSQL . " GROUP BY fs.id ORDER BY priority ASC, fs.username ASC LIMIT 100";
}

$stmt = $conn->prepare($query);

if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>People You May Know</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("../includes/css-links-inc.php"); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .modern-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
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
    </style>
</head>

<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/company-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>People You May Know</h1>
    </div>

    <section class="section">
        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="education_year" class="col-form-label">Education Year:</label>
                </div>

                <div class="col-auto">
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

                <div class="col-auto">
                    <select name="now_status" id="now_status" class="form-select">
                        <option value="">-- Select Status --</option>
                        <option value="study" <?= ($now_status === 'study') ? 'selected' : '' ?>>Study</option>
                        <option value="work" <?= ($now_status === 'work') ? 'selected' : '' ?>>Work</option>
                        <option value="intern" <?= ($now_status === 'intern') ? 'selected' : '' ?>>Intern</option>
                        <option value="free" <?= ($now_status === 'free') ? 'selected' : '' ?>>Free</option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php if (count($suggestions) > 0): ?>
                <?php foreach ($suggestions as $person): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="modern-card">
                            <div class="d-flex align-items-center mb-3">
                                <img src="../oddstudents/<?= htmlspecialchars($person['profile_picture']) ?>" alt="Profile" class="profile-img me-3">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($person['full_name']) ?></h5>
                                    <a href="profile.php?former_student_id=<?= $person['id']; ?>" class="btn btn-sm btn-outline-primary">View Profile</a>

                                    <?php if ($person['priority'] == 1): ?>
                                        <span class="badge bg-success ms-2">Has Work Experience</span>
                                    <?php elseif ($person['priority'] == 2): ?>
                                        <span class="badge bg-info text-dark ms-2">Has Education Only</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary ms-2">No Work/Education</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="edu-work-container mb-3">
                                <?php if (!empty($person['job_role'])): ?>
                                    <div>
                                        <h6 class="text-muted mb-1"><i class="bi bi-briefcase-fill"></i> Work</h6>
                                        <p class="mb-0"><strong><?= htmlspecialchars($person['job_role']) ?></strong> at <?= htmlspecialchars($person['job_company']) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($person['school'])): ?>
                                    <div>
                                        <h6 class="text-muted mb-1"><i class="bi bi-mortarboard"></i> Education</h6>
                                        <p class="mb-0"><?= htmlspecialchars($person['school']) ?> - <?= htmlspecialchars($person['course']) ?> (<?= htmlspecialchars($person['start_year']) ?>)</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="social-icons">
                                <?php if (!empty($person['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($person['facebook']) ?>" target="_blank"><i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook</a>
                                <?php endif; ?>
                                <?php if (!empty($person['github'])): ?>
                                    <a href="<?= htmlspecialchars($person['github']) ?>" target="_blank"><i class="fab fa-github" style="color:#171515;"></i> Github</a>
                                <?php endif; ?>
                                <?php if (!empty($person['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($person['linkedin']) ?>" target="_blank"><i class="fab fa-linkedin" style="color:#0077B5;"></i> LinkedIn</a>
                                <?php endif; ?>
                                <?php if (!empty($person['blog'])): ?>
                                    <a href="<?= htmlspecialchars($person['blog']) ?>" target="_blank"><i class="fas fa-blog" style="color:#fc4f08;"></i> Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="alert alert-info">No suggestions found at the moment.</div></div>
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
