<?php
session_start();
require_once '../includes/db-conn.php';

// Check if user is logged in
if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['former_student_id'];

$sql = "SELECT username, email, nic,mobile,profile_picture FROM former_students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get the experience ID from the URL
if (isset($_GET['id'])) {
    $experience_id = $_GET['id'];

    // Fetch experience data from the database
    $query = "SELECT * FROM experiences WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $experience_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $experience = $result->fetch_assoc();

    // If experience data doesn't exist
    if (!$experience) {
        echo "Experience not found!";
        exit();
    }
} else {
    echo "Experience ID is missing!";
    exit();
}
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Edit - Eduwide</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include_once ("../includes/css-links-inc.php"); ?>
</head>
<body>
    <body>

    <?php include_once ("../includes/header.php") ?>

    <?php include_once ("../includes/formers-sidebar.php") ?>

     <main id="main" class="main">

        <div class="pagetitle">
            <h1>Add Former Student</h1>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Your Path</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-4">

                            <!-- Edit Experience Form -->
                            <form action="update_experience.php" method="POST">
                                <input type="hidden" name="id" class="form-control" value="<?php echo $experience['id']; ?>">
                                <br>
                                
                                <div>
                                    <label for="title">Title:</label>
                                    <input type="text" name="title" class="form-control w-75" id="title" value="<?php echo $experience['title']; ?>" required>
                                </div>
                                <br>

                                <div>
                                    <label for="company">Company:</label>
                                    <input type="text" name="company" class="form-control w-75" id="company" value="<?php echo $experience['company']; ?>" required>
                                </div>
                                <br>

                                <div>
                                    <label for="start_month">Start Month:</label>
                                    <input type="text" name="start_month" class="form-control" id="start_month" value="<?php echo $experience['start_month']; ?>" required>
                                </div>
                                <br>

                                <div>
                                    <label for="start_year">Start Year:</label>
                                    <input type="number" name="start_year" class="form-control" id="start_year" value="<?php echo $experience['start_year']; ?>" required>
                                </div>
                                <br>

                                <div>
                                    <label for="description">Description:</label>
                                    <textarea name="description" class="form-control" id="description"><?php echo $experience['description']; ?></textarea>
                                </div>
                                <br>

                                <div>
                                    <label for="currently_working">Currently working:</label>
                                    <input type="checkbox" name="currently_working" id="currently_working" <?php echo $experience['currently_working'] ? 'checked' : ''; ?>>
                                </div>
                                <br>

                                <div>
                                    <label for="end_month">End Month:</label>
                                    <input type="text" class="form-control" name="end_month" id="end_month" value="<?php echo $experience['end_month']; ?>">
                                </div>
                                <br>

                                <div>
                                    <label for="end_year">End Year:</label>
                                    <input type="number" class="form-control" name="end_year" id="end_year" value="<?php echo $experience['end_year']; ?>">
                                </div>
                                <br>

                                <div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>

                                    <?php include_once ("../includes/footer.php") ?>

                                <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

                                <?php include_once ("../includes/js-links-inc.php") ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</body>

