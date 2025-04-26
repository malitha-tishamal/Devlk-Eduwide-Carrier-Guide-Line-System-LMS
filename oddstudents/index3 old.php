<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$current_user_id = $_SESSION['former_student_id'];

// Get current user education + work
$stmt = $conn->prepare("SELECT * FROM former_students WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

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

// AI-Like Weighted Matching (simulated)
$query = "
SELECT fs.id, fs.username AS full_name, fs.profile_picture, fs.facebook, fs.github, fs.linkedin, fs.blog,
       e.school, e.field_of_study AS course, w.company AS job_company, w.title AS job_role,
       (
         (CASE WHEN e.school = ? THEN 3 ELSE 0 END) +
         (CASE WHEN e.field_of_study = ? THEN 2 ELSE 0 END) +
         (CASE WHEN e.start_year = ? THEN 1 ELSE 0 END) +
         (CASE WHEN e.end_year = ? THEN 1 ELSE 0 END) +
         (CASE WHEN w.company = ? THEN 3 ELSE 0 END) +
         (CASE WHEN w.title = ? THEN 2 ELSE 0 END)
       ) AS score
FROM former_students fs
LEFT JOIN education e ON fs.id = e.user_id
LEFT JOIN experiences w ON fs.id = w.user_id
WHERE fs.id != ?
GROUP BY fs.id
HAVING score > 0
ORDER BY score DESC, RAND()
LIMIT 20";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssisssi", $school, $field, $start_year, $end_year, $company, $title, $current_user_id);
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
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #0d6efd;
        }

        .social-icons a {
            color: #444;
            margin-right: 12px;
            transition: color 0.2s;
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
<?php include_once("../includes/formers-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>People You May Know</h1>
    </div>

    <section class="section">
        <div class="row">
            <?php if (count($suggestions) > 0): ?>
                <?php foreach ($suggestions as $person): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="modern-card">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= htmlspecialchars($person['profile_picture']) ?>" alt="Profile" class="profile-img me-3">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($person['full_name']) ?></h5>
                                    <a href="profile.php?former_student_id=<?= $person['id']; ?>" class="btn btn-sm btn-outline-primary">View Profile</a>
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
                                        <p class="mb-0"><?= htmlspecialchars($person['school']) ?> - <?= htmlspecialchars($person['course']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="social-icons">
                                <?php if (!empty($person['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($person['facebook']) ?>" target="_blank"><span style="color: #1877F2;"><i class="fab fa-facebook"> Facebook</i></span></a>
                                <?php endif; ?>
                                <?php if (!empty($person['github'])): ?>
                                    <a href="<?= htmlspecialchars($person['github']) ?>" target="_blank"><span style="color:  #171515;"><i class="fab fa-github"> Github</i></span></a>
                                <?php endif; ?>
                                <?php if (!empty($person['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($person['linkedin']) ?>" target="_blank"><span style="color: #0077B5;"><i class="fab fa-linkedin"> LinkedIn</i></span></a>
                                <?php endif; ?>
                                <?php if (!empty($person['blog'])): ?>
                                    <a href="<?= htmlspecialchars($person['blog']) ?>" target="_blank"><span style="color: #fc4f08;"><i class="fas fa-blog"> Blog</i></span></a>
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

<?php $conn->close(); ?>
