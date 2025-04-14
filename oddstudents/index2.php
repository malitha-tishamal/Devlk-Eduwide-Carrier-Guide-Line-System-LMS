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

$edu_stmt = $conn->prepare("SELECT school, field_of_study FROM education WHERE user_id = ? ORDER BY id DESC LIMIT 1");
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
$company = $work_data['company'] ?? '';
$title = $work_data['title'] ?? '';

$current_profile = implode(" ", [$school, $field, $company, $title]);

// Fetch all other users
$all_stmt = $conn->prepare("SELECT former_students.id, username, school, field_of_study, company, title
    FROM former_students
    LEFT JOIN education ON former_students.id = education.user_id
    LEFT JOIN experiences ON former_students.id = experiences.user_id
    WHERE former_students.id != ?");
$all_stmt->bind_param("i", $current_user_id);
$all_stmt->execute();
$all_users = $all_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$all_stmt->close();

$other_profiles = [];
$other_ids = [];
foreach ($all_users as $u) {
    $other_ids[] = $u['id'];
    $other_profiles[] = implode(" ", [$u['school'], $u['field_of_study'], $u['company'], $u['title']]);
}

$payload = json_encode([
    "user_profile" => $current_profile,
    "other_profiles" => $other_profiles,
    "other_ids" => $other_ids
]);

$ch = curl_init('http://localhost:5005/get_similar');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

$ai_results = json_decode($response, true);

// Check if the AI response is valid
if (isset($ai_results) && is_array($ai_results) && !empty($ai_results)) {
    $ids = array_column($ai_results, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $suggestions = [];

    if (!empty($ids)) {
        $query = "SELECT fs.id, fs.username AS full_name, fs.profile_picture, fs.facebook, fs.github, fs.linkedin, fs.blog,
                         e.school, e.field_of_study AS course, w.company AS job_company, w.title AS job_role
                  FROM former_students fs
                  LEFT JOIN education e ON fs.id = e.user_id
                  LEFT JOIN experiences w ON fs.id = w.user_id
                  WHERE fs.id IN ($placeholders)
                  GROUP BY fs.id";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }
        $stmt->close();
    }
} else {
    // Handle the case where no valid AI results are returned
    $suggestions = [];
    // Optionally, log the error or handle it as needed
    error_log("No valid AI results returned or AI API request failed.");
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
