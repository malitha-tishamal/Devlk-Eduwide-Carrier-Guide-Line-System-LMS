<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch admin details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic, mobile, profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch all IT and Engineering skills
$skills_sql = "
SELECT id, skill_name, 'IT' AS category FROM it_student_skills
UNION ALL
SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
ORDER BY category, skill_name
";
$skills_result = $conn->query($skills_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skills Management</title>
<?php include_once("../includes/css-links-inc.php"); ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
.table-actions a { margin-right: 5px; }
</style>
</head>
<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/sadmin-sidebar.php"); ?>

 <main id="main" class="main">
        <div class="pagetitle">
            <h1>Skills Manage</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Skills Manage</li>
                </ol>
            </nav>
        </div>

<!-- ================== Section Wrapper ================== -->
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title">All Skills</h4>
                        <div>
                            <label>Filter by Category: </label>
                            <select id="categoryFilter" class="form-select">
                                <option value="">All</option>
                                <option value="IT">IT</option>
                                <option value="Engineering">Engineering</option>
                            </select>
                        </div>
                    </div>

                    <table id="skillsTable" class="table" style="width:100%">
                        <thead>
                            <tr>

                                <th>Skill Name</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $skills_result->fetch_assoc()): ?>
                            <tr>

                                <td><?= htmlspecialchars($row['skill_name']) ?></td>
                                <td><?= $row['category'] ?></td>
                                <td class="table-actions">
                                    <a href="edit_skill.php?id=<?= $row['id'] ?>&type=<?= strtolower($row['category']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_skill.php?id=<?= $row['id'] ?>&type=<?= strtolower($row['category']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================== End Section ================== -->

</main>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#skillsTable').DataTable({
        "paging": true,
        "info": true,
        "responsive": true,
        "order": [[1, "asc"]] // order by category by default
    });

    // Category filter
    $('#categoryFilter').on('change', function(){
        var val = $(this).val();
        if(val) {
            table.column(1).search('^' + val + '$', true, false).draw(); // column 1 is Category
        } else {
            table.column(1).search('').draw();
        }
    });
});
</script>


</body>
</html>

<?php $conn->close(); ?>
