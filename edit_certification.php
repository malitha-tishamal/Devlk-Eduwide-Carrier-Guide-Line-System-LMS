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

$certification_id = $_GET['id'];

// Get certification data
$stmt = $conn->prepare("SELECT * FROM students_certifications WHERE id = ? AND student_id = ?");
$stmt->bind_param("ii", $certification_id, $current_user_id);
$stmt->execute();
$certification = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$certification) {
    echo "Certification not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $certification_name = $_POST['certification_name'];
    $issued_by = $_POST['issued_by'];
    $date = $_POST['date'];
    $link = $_POST['link'];
    $description = $_POST['certification_description'];

    if (!empty($_FILES['certification_image']['name'])) {
        $image_name = $_FILES['certification_image']['name'];
        $image_tmp = $_FILES['certification_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($image_ext, $allowed) && $_FILES['certification_image']['size'] < 5000000) {
            $new_image_name = uniqid('', true) . '.' . $image_ext;
            $image_path = 'uploads/certifications/' . $new_image_name;
            move_uploaded_file($image_tmp, $image_path);

            $stmt = $conn->prepare("UPDATE students_certifications SET certification_name = ?, issued_by = ?, date = ?, link = ?, certification_description = ?, image_path = ? WHERE id = ? AND student_id = ?");
            $stmt->bind_param("ssssssii", $certification_name, $issued_by, $date, $link, $description, $image_path, $certification_id, $current_user_id);
        } else {
            $error = "Invalid image file.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE students_certifications SET certification_name = ?, issued_by = ?, date = ?, link = ?, certification_description = ? WHERE id = ? AND student_id = ?");
        $stmt->bind_param("ssssssi", $certification_name, $issued_by, $date, $link, $description, $certification_id, $current_user_id);
    }

    if (!isset($error)) {
        $stmt->execute();
        $stmt->close();
        header("Location: pages-Certification.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Certification</title>
  <?php include_once("includes/css-links-inc.php"); ?>
</head>
<body class="bg-light">
<?php include_once("includes/header.php"); ?>
<?php include_once("includes/formers-sidebar.php"); ?>

<main class="main container mt-5">
  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
  <?php endif; ?>

  <main id="main" class="main">
  <div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h3>Edit Cetification</h3>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="">Home</a></li>
            <li class="breadcrumb-item active">Cetification</li>
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

            <h3 class="card-title">Edit Cetification</h3>

            <form action="" method="POST" enctype="multipart/form-data" class="card p-4">
              <div class="mb-3">
                <label for="certification_name" class="form-label">Certification Name</label>
                <input type="text" name="certification_name" class="form-control" value="<?= htmlspecialchars($certification['certification_name']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="issued_by" class="form-label">Issued By</label>
                <input type="text" name="issued_by" class="form-control" value="<?= htmlspecialchars($certification['issued_by']); ?>" required>
              </div>
              <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= $certification['date']; ?>" required>
              </div>
              <div class="mb-3">
                <label for="link" class="form-label">Link</label>
                <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($certification['link']); ?>">
              </div>
              <div class="mb-3">
                <label for="certification_description" class="form-label">Description</label>
                <textarea name="certification_description" class="form-control" rows="4" required><?= htmlspecialchars($certification['certification_description']); ?></textarea>
              </div>
              <div class="mb-3">
                <label for="certification_image" class="form-label">Change Image (optional)</label>
                <input type="file" name="certification_image" class="form-control" accept="image/*">
                <div class="mt-2">
                  <img src="<?= $certification['image_path']; ?>" width="150" class="img-thumbnail" alt="Current Image">
                </div>
              </div>
              <div class="d-flex">
                <button type="submit" class="btn btn-primary ">Update Certification</button>&nbsp;&nbsp;&nbsp;
              <a href="pages-Certification.php" class="btn btn-danger ">Cancel</a>
              </div>
            </form>
          </main>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include_once("includes/footer.php"); ?>
<?php include_once("includes/js-links-inc.php"); ?>
</body>
</html>
