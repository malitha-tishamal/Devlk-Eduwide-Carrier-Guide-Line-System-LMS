<?php
session_start();
require_once '../includes/db-conn.php';

// Sample input: in real app, get these via GET or POST
$student_reg_id = 'gal-it-2023-f-0000';  // example
$semester = 'Semester III';             // example

// Get student name
$student_result = mysqli_query($conn, "SELECT username FROM students WHERE reg_id = '$student_reg_id'");
$student_data = mysqli_fetch_assoc($student_result);
$student_name = $student_data ? $student_data['username'] : 'Unknown';

// Category function
function getCategory($percentage) {
    if ($percentage >= 75) return 'Distinction';
    elseif ($percentage >= 60) return 'Good';
    elseif ($percentage >= 40) return 'Average';
    else return 'Weak';
}

function getBadgeClass($category) {
    return match($category) {
        'Distinction' => 'success',
        'Good' => 'primary',
        'Average' => 'warning',
        'Weak' => 'danger',
        default => 'secondary',
    };
}

// Get all marks for student in that semester
$query = "
    SELECT subject, practical_marks, paper_marks, (practical_marks + paper_marks) AS final_marks
    FROM marks
    WHERE student_id = '$student_reg_id' AND semester = '$semester'
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Marks for <?= htmlspecialchars($semester) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4><?= htmlspecialchars($student_name) ?> - <?= htmlspecialchars($semester) ?> Results</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary">
                    <tr>
                        <th>Subject</th>
                        <th>Practical Marks</th>
                        <th>Paper Marks</th>
                        <th>Total Marks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $percentage = $row['final_marks'];
                            $category = getCategory($percentage);
                            $badge = getBadgeClass($category);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= $row['practical_marks'] ?></td>
                            <td><?= $row['paper_marks'] ?></td>
                            <td><?= $percentage ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= $category ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No marks found for this semester.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
