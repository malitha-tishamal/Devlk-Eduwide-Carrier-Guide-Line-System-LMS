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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certification_image'])) {
    $certification_name = $_POST['certification_name'];
    $issued_by = $_POST['issued_by'];
    $date = $_POST['date'];
    $link = $_POST['link'];
    $certification_description = $_POST['certification_description'];

    $image_name = $_FILES['certification_image']['name'];
    $image_tmp_name = $_FILES['certification_image']['tmp_name'];
    $image_size = $_FILES['certification_image']['size'];
    $image_error = $_FILES['certification_image']['error'];
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    if (in_array($image_ext, $allowed_extensions) && $image_size < 5000000 && $image_error == 0) {
        $image_new_name = uniqid('', true) . "." . $image_ext;
        $image_upload_path = 'uploads/certifications/' . $image_new_name;
        move_uploaded_file($image_tmp_name, $image_upload_path);

        $stmt = $conn->prepare("INSERT INTO students_certifications (student_id, certification_name, issued_by, date, link, certification_description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $current_user_id, $certification_name, $issued_by, $date, $link, $certification_description, $image_upload_path);
        $stmt->execute();
        $stmt->close();
        

        $stmt = $conn->prepare("SELECT * FROM students_certifications WHERE student_id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $certifications_result = $stmt->get_result();
        $stmt->close();
    } else {
        $error_message_certification = "Invalid image file. Please upload a JPG, JPEG, or PNG image smaller than 5MB.";
    }
} else {

    $stmt = $conn->prepare("SELECT * FROM students_certifications WHERE student_id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $certifications_result = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Certifications</title>
  <?php include_once("includes/css-links-inc.php"); ?>
</head>
<body class="bg-light">
<?php include_once("includes/header.php"); ?>
<?php include_once("includes/students-sidebar.php"); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1>Certifications</h1>
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
            <button class="btn btn-secondary mt-5" data-bs-toggle="modal" data-bs-target="#certificationModal">Add Certification</button>

            <div class="modal fade" id="certificationModal" tabindex="-1" aria-labelledby="certificationModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="certificationModalLabel">Add Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                      <div class="mb-3">
                        <label for="certification_name" class="form-label">Certification Name</label>
                        <input type="text" class="form-control" id="certification_name" name="certification_name" required>
                      </div>
                      <div class="mb-3">
                        <label for="issued_by" class="form-label">Issued By</label>
                        <input type="text" class="form-control" id="issued_by" name="issued_by" required>
                      </div>
                      <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                      </div>
                      <div class="mb-3">
                        <label for="link" class="form-label">Link (if applicable)</label>
                        <input type="url" class="form-control" id="link" name="link">
                      </div>
                      <div class="mb-3">
                        <label for="certification_description" class="form-label">Certification Description</label>
                        <textarea class="form-control" id="certification_description" name="certification_description" rows="3" required></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="certification_image" class="form-label">Certification Image</label>
                        <input type="file" class="form-control" id="certification_image" name="certification_image" accept="image/*" required>
                      </div>
                      <button type="submit" class="btn btn-secondary">Submit Certification</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Display Error Message -->
            <?php if (isset($error_message_certification)): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?= $error_message_certification; ?>
              </div>
            <?php endif; ?>

            <!-- Display Certifications -->
            <h4 class="card-title mt-5">Your Certifications</h4>
            <div class="row">
              <?php while ($certification = $certifications_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                  <div class="card">
                    <img src="<?= $certification['image_path']; ?>" class="card-img-top" alt="Certification Image">
                    <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($certification['certification_name']); ?></h5>
                      <p class="card-text"><strong>Issued By:</strong> <?= htmlspecialchars($certification['issued_by']); ?></p>
                      <p class="card-text"><strong>Date:</strong> <?= htmlspecialchars($certification['date']); ?></p>
                      <p class="card-text"><?= htmlspecialchars($certification['certification_description']); ?></p>
                      <?php if ($certification['link']): ?>
                        <p class="card-text"><a href="<?= $certification['link']; ?>" target="_blank">View More</a></p>
                      <?php endif; ?>
                      
                      <a href="edit_certification.php?id=<?= $certification['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                      <a href="delete_certification.php?id=<?= $certification['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
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
