<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = $_SESSION['student_id'];

// Get user data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (isset($_GET['id'])) {
    $achievement_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM students_achievements WHERE id = ? AND student_id = ?");
    $stmt->bind_param("ii", $achievement_id, $current_user_id);
    $stmt->execute();
    $achievement = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$achievement) {
        header("Location: pages-achievements.php");
        exit();
    }
} else {
    header("Location: pages-achievements.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $organized_by = $_POST['organized_by'];
    $event_date = $_POST['event_date'];
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];

    $image_path = $achievement['image_path']; 

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $image_name = $_FILES['event_image']['name'];
        $image_tmp_name = $_FILES['event_image']['tmp_name'];
        $image_size = $_FILES['event_image']['size'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array($image_ext, $allowed_extensions) && $image_size < 5000000) {
            $image_new_name = uniqid('', true) . "." . $image_ext;
            $image_upload_path = 'uploads/achievements/' . $image_new_name;
            move_uploaded_file($image_tmp_name, $image_upload_path);
            $image_path = $image_upload_path;
        } else {
            $error_message = "Invalid image file. Please upload a JPG, JPEG, or PNG image smaller than 5MB.";
        }
    }

    if (!isset($error_message)) {
        $stmt = $conn->prepare("UPDATE students_achievements SET event_name = ?, organized_by = ?, event_date = ?, event_title = ?, event_description = ?, image_path = ? WHERE id = ? AND student_id = ?");
        $stmt->bind_param("ssssssii", $event_name, $organized_by, $event_date, $event_title, $event_description, $image_path, $achievement_id, $current_user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: pages-achievements.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Achievement | Alumni Portal</title>
  <?php include_once("includes/css-links-inc.php"); ?>
</head>
<body class="bg-light">
<?php include_once("includes/header.php"); ?>
<?php include_once("includes/students-sidebar.php"); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Edit Achievement</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="pages-achievements.php">Achievements</a></li>
        <li class="breadcrumb-item active">Edit Achievement</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-12">
        <div class="card border">
          <div class="card-body p-4">
            <h4 class="card-title">Edit Your Achievement</h4>

            <?php if (isset($error_message)): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?= $error_message; ?>
              </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="event_title" class="form-label">Event Achievement Title</label>
                <input type="text" class="form-control" id="event_title" name="event_title" value="<?= htmlspecialchars($achievement['event_title']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="event_name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="event_name" name="event_name" value="<?= htmlspecialchars($achievement['event_name']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="organized_by" class="form-label">Organized By</label>
                <input type="text" class="form-control" id="organized_by" name="organized_by" value="<?= htmlspecialchars($achievement['organized_by']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="event_date" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" value="<?= htmlspecialchars($achievement['event_date']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="event_description" class="form-label">Event Description</label>
                <textarea class="form-control" id="event_description" name="event_description" rows="3" ><?= htmlspecialchars($achievement['event_description']); ?></textarea>
              </div>
              <div class="mb-3">
                <label for="event_image" class="form-label">Event Image</label>
                <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                <?php if ($achievement['image_path']): ?>
                  <img src="<?= $achievement['image_path']; ?>" alt="Current Image" class="img-thumbnail mt-3" style="max-width: 200px;">
                <?php endif; ?>
              </div>
              <button type="submit" class="btn btn-primary">Update Achievement</button>
               <a href="pages-achievements.php"><button type="cancel" class="btn btn-danger">Cancel</button></a>
            </form>
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
