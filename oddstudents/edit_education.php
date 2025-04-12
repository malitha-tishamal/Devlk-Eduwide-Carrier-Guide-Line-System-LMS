<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    echo 'Unauthorized access';
    exit();
}

$user_id = $_SESSION['former_student_id'];

// Check if the 'id' parameter is passed in the URL
if (isset($_GET['id'])) {
    $education_id = $_GET['id'];

    $user_id = $_SESSION['former_student_id'];
    $sql = "SELECT * FROM former_students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // This will fetch the user details
    $stmt->close();

    // Fetch the education record from the database
    $query = "SELECT * FROM education WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $education_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the record exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo 'No education record found';
        exit();
    }
} else {
    echo 'Invalid request';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Education</title>
    <?php include_once ("../includes/css-links-inc.php"); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once ("../includes/header.php") ?>
    <?php include_once ("../includes/formers-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Your Path</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="">
                    <div class="card">
                        <div class="card-body pt-3">
                        <div class="container my-5">
                            <h3>Edit Education</h3>
                            <form action="save_education.php" method="POST">
                                <input type="hidden" name="education_id" value="<?php echo $row['id']; ?>">

                                <div class="mb-3">
                                    <label for="school" class="form-label">School</label>
                                    <input type="text" class="form-control w-75" id="school" name="school" value="<?php echo htmlspecialchars($row['school']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="degree" class="form-label">Degree</label>
                                    <input type="text" class="form-control w-75" id="degree" name="degree" value="<?php echo htmlspecialchars($row['degree']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="field" class="form-label">Field of Study</label>
                                    <input type="text" class="form-control w-75" id="field" name="field" value="<?php echo htmlspecialchars($row['field_of_study']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="start_month" class="form-label">Start Month</label>
                                    <input type="text" class="form-control w-75" id="start_month" name="start_month" value="<?php echo htmlspecialchars($row['start_month']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="start_year" class="form-label">Start Year</label>
                                    <input type="number" class="form-control w-75" id="start_year" name="start_year" value="<?php echo $row['start_year']; ?>" >
                                </div>
                                
                                <div class="mb-3">
                                    <label for="end_month" class="form-label">End Month</label>
                                    <input type="text" class="form-control w-75" id="end_month" name="end_month" value="<?php echo htmlspecialchars($row['end_month']); ?>" >
                                </div>
                                
                                <div class="mb-3">
                                    <label for="end_year" class="form-label">End Year</label>
                                    <input type="number" class="form-control w-75" id="end_year" name="end_year" value="<?php echo $row['end_year']; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="grade" class="form-label">Grade</label>
                                    <input type="text" class="form-control w-75" id="grade" name="grade" value="<?php echo htmlspecialchars($row['grade']); ?>" >
                                </div>
                                
                                <div class="mb-3">
                                    <label for="activities" class="form-label">Activities & Societies</label>
                                    <input type="text" class="form-control w-75" id="activities" name="activities" value="<?php echo htmlspecialchars($row['activities']); ?>" >
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control w-75" id="description" name="description" rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

                    <?php include_once ("../includes/footer4.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php include_once ("../includes/js-links-inc.php") ?>
</body>
</html>
