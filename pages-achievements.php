<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$current_user_id = $_SESSION['student_id'];

// Get user data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['event_image'])) {
    $event_name = $_POST['event_name'];
    $organized_by = $_POST['organized_by'];
    $event_date = $_POST['event_date'];
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];

    $image_name = $_FILES['event_image']['name'];
    $image_tmp_name = $_FILES['event_image']['tmp_name'];
    $image_size = $_FILES['event_image']['size'];
    $image_error = $_FILES['event_image']['error'];
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    if (in_array($image_ext, $allowed_extensions) && $image_size < 5000000 && $image_error == 0) {
        $image_new_name = uniqid('', true) . "." . $image_ext;
        $image_upload_path = 'uploads/achievements/' . $image_new_name;
        move_uploaded_file($image_tmp_name, $image_upload_path);

        $stmt = $conn->prepare("INSERT INTO students_achievements (student_id, event_name, organized_by, event_date, event_title, event_description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $current_user_id, $event_name, $organized_by, $event_date, $event_title, $event_description, $image_upload_path);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT * FROM students_achievements WHERE student_id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $achievements_result = $stmt->get_result();
        $stmt->close();
    } else {
        $error_message = "Invalid image file. Please upload a JPG, JPEG, or PNG image smaller than 5MB.";
    }
} else {

    $stmt = $conn->prepare("SELECT * FROM students_achievements WHERE student_id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $achievements_result = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Achievements </title>
  <?php include_once("includes/css-links-inc.php"); ?>
</head>
<body class="bg-light">
<?php include_once("includes/header.php"); ?>
<?php include_once("includes/students-sidebar.php"); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1>Achievements</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Achievements</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-12">
        <div class="card border">
          <div class="card-body p-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#achievementModal">Add Achievement</button>

            <div class="modal fade" id="achievementModal" tabindex="-1" aria-labelledby="achievementModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="achievementModalLabel">Add Achievement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                      <div class="mb-3">
                        <label for="event_title" class="form-label">Event Achievement Title</label>
                        <input type="text" class="form-control" id="event_title" name="event_title" required>
                      </div>
                      <div class="mb-3">
                        <label for="event_name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="event_name" name="event_name" required>
                      </div>
                      <div class="mb-3">
                        <label for="organized_by" class="form-label">Organized By</label>
                        <input type="text" class="form-control" id="organized_by" name="organized_by" required>
                      </div>
                      <div class="mb-3">
                        <label for="event_date" class="form-label">Event Date</label>
                        <input type="date" class="form-control" id="event_date" name="event_date" required>
                      </div>
                      <div class="mb-3">
                        <label for="event_description" class="form-label">Event Description</label>
                        <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="event_image" class="form-label">Event Image</label>
                        <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*" required>
                      </div>
                      <button type="submit" class="btn btn-primary">Submit Achievement</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>


            <!-- Display Error Message -->
            <?php if (isset($error_message)): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?= $error_message; ?>
              </div>
            <?php endif; ?>

            <!-- Display Achievements -->
            <h4 class="card-title mt-5">Your Achievements</h4>
            <div class="row">
              <?php while ($achievement = $achievements_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                  <div class="card">
                    <img src="<?= $achievement['image_path']; ?>" class="card-img-top" alt="Event Image">
                    <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($achievement['event_title']); ?></h5>
                      <p class="card-text"><strong>Event Name:</strong> <?= htmlspecialchars($achievement['event_name']); ?></p>
                      <p class="card-text"><strong>Organized By:</strong> <?= htmlspecialchars($achievement['organized_by']); ?></p>
                      <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($achievement['event_date']); ?></p>
                      <p class="card-text"><?= htmlspecialchars($achievement['event_description']); ?></p>


                      <a href="edit_achievement.php?id=<?= $achievement['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                      <a href="delete_achievement.php?id=<?= $achievement['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once("includes/footer.php"); ?>
<?php include_once("includes/js-links-inc.php"); ?>

</body>
</html>
