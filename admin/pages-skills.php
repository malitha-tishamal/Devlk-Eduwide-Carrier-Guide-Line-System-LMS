<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch admin info
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic, mobile, profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle Add / Edit / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $skill_name = trim($_POST['skill_name']);
        $category = trim($_POST['category']);
        $table = strtolower($category) === 'engineering' ? 'engineering_skills' : 'it_student_skills';
        $stmt = $conn->prepare("INSERT INTO $table (skill_name) VALUES (?)");
        $stmt->bind_param("s", $skill_name);
        $stmt->execute();
        exit('success');
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $skill_name = trim($_POST['skill_name']);
        $category = trim($_POST['category']);
        $table = strtolower($category) === 'engineering' ? 'engineering_skills' : 'it_student_skills';
        $stmt = $conn->prepare("UPDATE $table SET skill_name=? WHERE id=?");
        $stmt->bind_param("si", $skill_name, $id);
        $stmt->execute();
        exit('success');
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        $category = trim($_POST['category']);
        $table = strtolower($category) === 'engineering' ? 'engineering_skills' : 'it_student_skills';
        $stmt = $conn->prepare("DELETE FROM $table WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        exit('success');
    }
    exit('error');
}

// Fetch all skills
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

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
.table-actions a { margin-right: 5px; }
.modal-content { border-radius: 10px; }
</style>
</head>
<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/sadmin-sidebar.php"); ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Skills Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Skills Management</li>
            </ol>
        </nav>
    </div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title mb-0">All Skills</h4>
                        <div class="d-flex align-items-center gap-2">
                            <select id="categoryFilter" class="form-select" style="width:auto;">
                                <option value="">All Categories</option>
                                <option value="IT">IT</option>
                                <option value="Engineering">Engineering</option>
                            </select>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                                <i class="bi bi-plus-circle"></i> Add Skill
                            </button>
                        </div>
                    </div>

                    <table id="skillsTable" class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Skill Name</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $skills_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['skill_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td class="table-actions">
                                    <button class="btn btn-warning btn-sm editBtn"
                                        data-id="<?= $row['id'] ?>"
                                        data-skill="<?= htmlspecialchars($row['skill_name']) ?>"
                                        data-category="<?= $row['category'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn"
                                        data-id="<?= $row['id'] ?>"
                                        data-category="<?= $row['category'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
</main>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addSkillForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Skill Name</label>
                    <input type="text" name="skill_name" class="form-control mb-3" required>
                    <label>Category</label>
                    <select name="category" class="form-select" required>
                        <option value="IT">IT</option>
                        <option value="Engineering">Engineering</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Skill Modal -->
<div class="modal fade" id="editSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSkillForm">
                <input type="hidden" name="id">
                <input type="hidden" name="category">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Skill Name</label>
                    <input type="text" name="skill_name" class="form-control mb-3" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#skillsTable').DataTable({
        responsive: true,
        order: [[2, "asc"]],
    });

    // Filter
    $('#categoryFilter').on('change', function() {
        table.column(2).search(this.value).draw();
    });

    // Add Skill
    $('#addSkillForm').submit(function(e) {
        e.preventDefault();
        $.post('', $(this).serialize() + '&action=add', function(res) {
            if (res === 'success') location.reload();
            else alert('Error adding skill.');
        });
    });

    // Edit Skill - open modal
    $(document).on('click', '.editBtn', function() {
        $('#editSkillForm [name=id]').val($(this).data('id'));
        $('#editSkillForm [name=skill_name]').val($(this).data('skill'));
        $('#editSkillForm [name=category]').val($(this).data('category'));
        $('#editSkillModal').modal('show');
    });

    // Save Edit
    $('#editSkillForm').submit(function(e) {
        e.preventDefault();
        $.post('', $(this).serialize() + '&action=edit', function(res) {
            if (res === 'success') location.reload();
            else alert('Error updating skill.');
        });
    });

    // Delete Skill
    $(document).on('click', '.deleteBtn', function() {
        if (!confirm('Are you sure you want to delete this skill?')) return;
        $.post('', { id: $(this).data('id'), category: $(this).data('category'), action: 'delete' }, function(res) {
            if (res === 'success') location.reload();
            else alert('Error deleting skill.');
        });
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>
