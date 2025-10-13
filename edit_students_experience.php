<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];
$experience_id = $_GET['id'] ?? '';

if (empty($experience_id)) {
    die("Invalid experience ID");
}

// Fetch experience data
$sql = "SELECT * FROM students_experiences WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $experience_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$experience = $result->fetch_assoc();

if (!$experience) {
    die("Experience not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Experience</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Experience</h2>
        <form method="POST" action="update_students_experience.php">
            <input type="hidden" name="id" value="<?= $experience['id'] ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label">Title*</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($experience['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="employment_type" class="form-label">Employment type</label>
                <select class="form-select" id="employment_type" name="employment_type">
                    <option value="">Please select</option>
                    <option value="Full-time" <?= $experience['employment_type'] == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                    <option value="Part-time" <?= $experience['employment_type'] == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                    <option value="Internship" <?= $experience['employment_type'] == 'Internship' ? 'selected' : '' ?>>Internship</option>
                    <option value="Freelance" <?= $experience['employment_type'] == 'Freelance' ? 'selected' : '' ?>>Freelance</option>
                    <option value="Volunteer" <?= $experience['employment_type'] == 'Volunteer' ? 'selected' : '' ?>>Volunteer</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="company" class="form-label">Company*</label>
                <input type="text" class="form-control" id="company" name="company" value="<?= htmlspecialchars($experience['company']) ?>" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="currently_working" name="currently_working" value="1" <?= $experience['currently_working'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="currently_working">I am currently working here</label>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_month" class="form-label">Start Month*</label>
                    <select class="form-select" id="start_month" name="start_month" required>
                        <option value="">Month</option>
                        <?php
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        foreach ($months as $month) {
                            $selected = $experience['start_month'] == $month ? 'selected' : '';
                            echo "<option value='$month' $selected>$month</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="start_year" class="form-label">Start Year*</label>
                    <input type="number" class="form-control" id="start_year" name="start_year" value="<?= $experience['start_year'] ?>" required>
                </div>
            </div>

            <div class="row mb-3" id="end_date_group" style="<?= $experience['currently_working'] ? 'display:none;' : '' ?>">
                <div class="col-md-6">
                    <label for="end_month" class="form-label">End Month</label>
                    <select class="form-select" id="end_month" name="end_month">
                        <option value="">Month</option>
                        <?php
                        foreach ($months as $month) {
                            $selected = $experience['end_month'] == $month ? 'selected' : '';
                            echo "<option value='$month' $selected>$month</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="end_year" class="form-label">End Year</label>
                    <input type="number" class="form-control" id="end_year" name="end_year" value="<?= $experience['end_year'] ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($experience['description']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Experience</button>
            <a href="students-profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        document.getElementById('currently_working').addEventListener('change', function() {
            const endDateGroup = document.getElementById('end_date_group');
            if (this.checked) {
                endDateGroup.style.display = 'none';
            } else {
                endDateGroup.style.display = 'flex';
            }
        });
    </script>
</body>
</html>