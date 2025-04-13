<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['former_student_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['former_student_id'];

// Fetch education details
$edu_query = "SELECT school, field_of_study, start_year, end_year FROM education WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($edu_query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$edu_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch work experience
$work_query = "SELECT company, title FROM experiences WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($work_query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$work_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$school = $edu_data['school'] ?? '';
$field = $edu_data['field_of_study'] ?? '';
$start_year = $edu_data['start_year'] ?? '';
$end_year = $edu_data['end_year'] ?? '';
$company = $work_data['company'] ?? '';
$title = $work_data['title'] ?? '';

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
LIMIT 5";

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggested Connections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">People You May Know</h3>
    <?php if (count($suggestions) > 0): ?>
        <div class="row">
            <?php foreach ($suggestions as $person): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?= htmlspecialchars($person['profile_picture']) ?>" class="rounded-circle me-3" width="60" height="60" alt="Profile Picture">
                                <div>
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($person['full_name']) ?></h5>
                                    <a href="view-profile.php?id=<?= $person['id'] ?>" class="btn btn-sm btn-outline-primary">View Profile</a>
                                </div>
                            </div>
                            <div>
                                <?php if (!empty($person['job_role'])): ?>
                                    <p><strong>Job:</strong> <?= htmlspecialchars($person['job_role']) ?> at <?= htmlspecialchars($person['job_company']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($person['school'])): ?>
                                    <p><strong>Education:</strong> <?= htmlspecialchars($person['school']) ?> - <?= htmlspecialchars($person['course']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if (!empty($person['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($person['facebook']) ?>" target="_blank" class="me-2"><i class="fab fa-facebook"></i> Facebook</a>
                                <?php endif; ?>
                                <?php if (!empty($person['github'])): ?>
                                    <a href="<?= htmlspecialchars($person['github']) ?>" target="_blank" class="me-2"><i class="fab fa-github"></i> GitHub</a>
                                <?php endif; ?>
                                <?php if (!empty($person['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($person['linkedin']) ?>" target="_blank" class="me-2"><i class="fab fa-linkedin"></i> LinkedIn</a>
                                <?php endif; ?>
                                <?php if (!empty($person['blog'])): ?>
                                    <a href="<?= htmlspecialchars($person['blog']) ?>" target="_blank"><i class="fas fa-blog"></i> Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No suggestions found at the moment.</div>
    <?php endif; ?>
</div>
</body>
</html>
