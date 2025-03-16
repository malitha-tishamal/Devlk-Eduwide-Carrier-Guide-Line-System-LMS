<?php
include '../includes/db-conn.php';

// Fetch all lecturers
$lecturers_result = $conn->query("SELECT * FROM lectures");

// Fetch all subjects
$subjects_result = $conn->query("SELECT * FROM subjects");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Subjects to Lecturer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Assign Subjects to Lecturer</h2>
        <form action="assign_subject_process.php" method="POST">
            <div class="form-group">
                <label for="lecturer">Lecturer</label>
                <select class="form-control" name="lecturer_id" id="lecturer">
                    <option value="">Select Lecturer</option>
                    <?php
                    while ($row = $lecturers_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subjects">Subjects</label><br>
                <?php
                while ($row = $subjects_result->fetch_assoc()) {
                    echo "<div class='form-check'>
                            <input class='form-check-input' type='checkbox' name='subject_ids[]' value='" . $row['id'] . "'>
                            <label class='form-check-label'>" . $row['code'] . " - " . $row['name'] . "</label>
                          </div>";
                }
                ?>
            </div>

            <button type="submit" class="btn btn-primary">Assign Subjects</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
