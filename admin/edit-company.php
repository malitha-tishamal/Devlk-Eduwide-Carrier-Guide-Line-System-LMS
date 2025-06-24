<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-companies.php");
    exit();
}

$company_id = intval($_GET['id']);

// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic,mobile,profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


// Fetch current company details
$sql = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();
$stmt->close();

if (!$company) {
    echo "Company not found.";
    exit();
}

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    // Handle profile picture upload
    $profile_picture = $company['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "../companies/";
        $fileName = basename($_FILES["profile_picture"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath);
        $profile_picture = $fileName;
    }

    $updateSql = "UPDATE companies SET username = ?, email = ?, mobile = ?, address = ?, category = ?, status = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssssssi", $username, $email, $mobile, $address, $category, $status, $profile_picture, $company_id);
    $stmt->execute();

    header("Location: manage-companies.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Company - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>
<body>

<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/sadmin-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Company</h1>
    </div>

    <section class="section">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="card">
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($company['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($company['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($company['mobile']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" required><?= htmlspecialchars($company['address']) ?></textarea>
                    </div>

                    <?php
                    $categories = [
                        "Software Engineering",
                        "Network Engineering",
                        "Cyber Security",
                        "Education",
                        "Data Science",
                        "Artificial Intelligence",
                        "Machine Learning",
                        "Blockchain Technology",
                        "Cloud Computing",
                        "DevOps",
                        "Mobile Development",
                        "Web Development",
                        "Game Development",
                        "UI/UX Design",
                        "Digital Marketing",
                        "Product Management",
                        "Business Analysis",
                        "Cybersecurity Research",
                        "System Administration",
                        "Data Engineering"
                    ];
                    ?>

                    <div class="row mt-3">
                        <div class="col-lg-3 col-md-4 label">Company Category</div>
                        <div class="col-lg-9 col-md-8">
                            <select id="category" name="category" class="form-select w-75" required>
                                <option value="" disabled>Select a category</option>

                                <?php
                                foreach ($categories as $cat) {
                                    $selected = ($company['category'] == $cat) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $company['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $company['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="disabled" <?= $company['status'] == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label><br>
                        <?php if (!empty($company['profile_picture'])): ?>
                            <img src="../companies/<?= htmlspecialchars($company['profile_picture']) ?>" width="80" class="mb-2">
                        <?php endif; ?>
                        <input type="file" name="profile_picture" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Company</button>
                    <a href="manage-companies.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </section>
</main>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>

</body>
</html>

<?php $conn->close(); ?>
