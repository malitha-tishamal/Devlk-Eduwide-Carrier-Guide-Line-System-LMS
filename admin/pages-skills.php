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

// Category mapping
$categoryMap = [
    'IT' => 'it_student_skills',
    'Engineering' => 'engineering_skills',
    'Business Finance' => 'business_finance_skills',
    'HND Accountancy' => 'hnd_accountancy_skills',
    'HND Agriculture' => 'hnd_agriculture_skills',
    'HND Building Services' => 'hnd_building_services_skills',
    'HND Business Admin' => 'hnd_business_admin_skills',
    'HND English' => 'hnd_english_skills',
    'HND Food Tech' => 'hnd_food_tech_skills',
    'HND Management' => 'hnd_management_skills',
    'HND Mechanical' => 'hnd_mechanical_skills',
    'HND Quantity Survey' => 'hnd_quantity_survey_skills',
    'HND THM' => 'hnd_thm_skills'
];

// Handle Add / Edit / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    $category = $_POST['category'] ?? '';
    $table = $categoryMap[$category] ?? '';

    if (!$table) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit();
    }

    try {
        if ($action === 'add') {
            $skill_name = trim($_POST['skill_name']);
            
            // Check if skill already exists in this category
            $check_stmt = $conn->prepare("SELECT id FROM $table WHERE skill_name = ?");
            $check_stmt->bind_param("s", $skill_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Skill already exists in this category']);
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO $table (skill_name) VALUES (?)");
            $stmt->bind_param("s", $skill_name);
            $stmt->execute();
            $new_id = $conn->insert_id;
            
            echo json_encode(['success' => true, 'message' => 'Skill added successfully', 'id' => $new_id]);
            exit();
        }

        if ($action === 'edit') {
            $id = $_POST['id'];
            $skill_name = trim($_POST['skill_name']);
            
            // Check if skill already exists (excluding current record)
            $check_stmt = $conn->prepare("SELECT id FROM $table WHERE skill_name = ? AND id != ?");
            $check_stmt->bind_param("si", $skill_name, $id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Skill already exists in this category']);
                exit();
            }
            
            $stmt = $conn->prepare("UPDATE $table SET skill_name=? WHERE id=?");
            $stmt->bind_param("si", $skill_name, $id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Skill updated successfully']);
            exit();
        }

        if ($action === 'delete') {
            $id = $_POST['id'];
            
            // Check if skill is being used by any student
            $usage_check = $conn->query("
                SELECT 'active' as type, student_id FROM active_student_skills WHERE skill_id = $id
                UNION ALL
                SELECT 'former' as type, student_id FROM former_student_skills WHERE skill_id = $id
                LIMIT 1
            ");
            
            if ($usage_check->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete skill. It is currently assigned to students.']);
                exit();
            }
            
            $stmt = $conn->prepare("DELETE FROM $table WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Skill deleted successfully']);
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
}

// Fetch all skills from all tables with counts
$skills_sql = "
SELECT s.id, s.skill_name, s.category, 
       COALESCE(a.active_count, 0) as active_students_count,
       COALESCE(f.former_count, 0) as former_students_count
FROM (
    SELECT id, skill_name, 'IT' AS category FROM it_student_skills
    UNION ALL SELECT id, skill_name, 'Engineering' FROM engineering_skills
    UNION ALL SELECT id, skill_name, 'Business Finance' FROM business_finance_skills
    UNION ALL SELECT id, skill_name, 'HND Accountancy' FROM hnd_accountancy_skills
    UNION ALL SELECT id, skill_name, 'HND Agriculture' FROM hnd_agriculture_skills
    UNION ALL SELECT id, skill_name, 'HND Building Services' FROM hnd_building_services_skills
    UNION ALL SELECT id, skill_name, 'HND Business Admin' FROM hnd_business_admin_skills
    UNION ALL SELECT id, skill_name, 'HND English' FROM hnd_english_skills
    UNION ALL SELECT id, skill_name, 'HND Food Tech' FROM hnd_food_tech_skills
    UNION ALL SELECT id, skill_name, 'HND Management' FROM hnd_management_skills
    UNION ALL SELECT id, skill_name, 'HND Mechanical' FROM hnd_mechanical_skills
    UNION ALL SELECT id, skill_name, 'HND Quantity Survey' FROM hnd_quantity_survey_skills
    UNION ALL SELECT id, skill_name, 'HND THM' FROM hnd_thm_skills
) s
LEFT JOIN (
    SELECT skill_id, COUNT(DISTINCT student_id) as active_count 
    FROM active_student_skills 
    GROUP BY skill_id
) a ON s.id = a.skill_id
LEFT JOIN (
    SELECT skill_id, COUNT(DISTINCT student_id) as former_count 
    FROM former_student_skills 
    GROUP BY skill_id
) f ON s.id = f.skill_id
ORDER BY s.category, s.skill_name
";
$skills_result = $conn->query($skills_sql);

// Get category statistics
$category_stats_sql = "
SELECT category, COUNT(*) as skill_count,
       SUM(active_students_count) as total_active_students,
       SUM(former_students_count) as total_former_students
FROM (
    SELECT s.id, s.category, 
           COALESCE(a.active_count, 0) as active_students_count,
           COALESCE(f.former_count, 0) as former_students_count
    FROM (
        SELECT id, 'IT' AS category FROM it_student_skills
        UNION ALL SELECT id, 'Engineering' FROM engineering_skills
        UNION ALL SELECT id, 'Business Finance' FROM business_finance_skills
        UNION ALL SELECT id, 'HND Accountancy' FROM hnd_accountancy_skills
        UNION ALL SELECT id, 'HND Agriculture' FROM hnd_agriculture_skills
        UNION ALL SELECT id, 'HND Building Services' FROM hnd_building_services_skills
        UNION ALL SELECT id, 'HND Business Admin' FROM hnd_business_admin_skills
        UNION ALL SELECT id, 'HND English' FROM hnd_english_skills
        UNION ALL SELECT id, 'HND Food Tech' FROM hnd_food_tech_skills
        UNION ALL SELECT id, 'HND Management' FROM hnd_management_skills
        UNION ALL SELECT id, 'HND Mechanical' FROM hnd_mechanical_skills
        UNION ALL SELECT id, 'HND Quantity Survey' FROM hnd_quantity_survey_skills
        UNION ALL SELECT id, 'HND THM' FROM hnd_thm_skills
    ) s
    LEFT JOIN (
        SELECT skill_id, COUNT(DISTINCT student_id) as active_count 
        FROM active_student_skills 
        GROUP BY skill_id
    ) a ON s.id = a.skill_id
    LEFT JOIN (
        SELECT skill_id, COUNT(DISTINCT student_id) as former_count 
        FROM former_student_skills 
        GROUP BY skill_id
    ) f ON s.id = f.skill_id
) stats
GROUP BY category
ORDER BY category
";
$category_stats = $conn->query($category_stats_sql);

$total_skills = $skills_result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skills Management - EduWide</title>
<?php include_once("../includes/css-links-inc.php"); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --success-color: #4cc9f0;
    --warning-color: #f8961e;
    --danger-color: #f94144;
    --dark-color: #1d3557;
    --light-color: #f8f9fa;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
}

.stats-card {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-bottom: 25px;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.stats-icon {
    font-size: 2.5rem;
    opacity: 0.8;
    margin-bottom: 10px;
}

.category-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.skill-usage {
    font-size: 0.75rem;
    color: #6c757d;
}

.skill-usage.active {
    color: var(--success-color);
}

.skill-usage.former {
    color: var(--warning-color);
}

.btn-action {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    margin: 2px;
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 15px 15px 0 0;
    border: none;
}

.modal-header .btn-close {
    filter: invert(1);
}

.table th {
    border-top: none;
    background: var(--light-color);
    font-weight: 600;
    color: var(--dark-color);
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
}

.alert {
    border: none;
    border-radius: 10px;
    padding: 15px 20px;
}

.category-filter {
    max-width: 200px;
}

.action-buttons {
    white-space: nowrap;
}

/* Loading overlay */
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.loading-spinner {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-number {
        font-size: 2rem;
    }
    
    .btn-action {
        padding: 4px 8px;
        font-size: 0.7rem;
    }
    
    .category-filter {
        max-width: 150px;
    }
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(67, 97, 238, 0.05);
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}
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
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item">System Management</li>
                <li class="breadcrumb-item active">Skills Management</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-code stats-icon"></i>
                    <div class="stats-number"><?= $total_skills ?></div>
                    <div class="stats-label">Total Skills</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-layer-group stats-icon"></i>
                    <div class="stats-number"><?= count($categoryMap) ?></div>
                    <div class="stats-label">Categories</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-user-graduate stats-icon"></i>
                    <div class="stats-number">
                        <?php
                        $active_usage = $conn->query("SELECT COUNT(DISTINCT student_id) as count FROM active_student_skills")->fetch_assoc()['count'];
                        echo $active_usage;
                        ?>
                    </div>
                    <div class="stats-label">Active Students Using Skills</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-briefcase stats-icon"></i>
                    <div class="stats-number">
                        <?php
                        $former_usage = $conn->query("SELECT COUNT(DISTINCT student_id) as count FROM former_student_skills")->fetch_assoc()['count'];
                        echo $former_usage;
                        ?>
                    </div>
                    <div class="stats-label">Former Students Using Skills</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Skills Database</h4>
                            <div class="d-flex align-items-center gap-2">
                                <select id="categoryFilter" class="form-select category-filter">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categoryMap as $cat => $tbl): ?>
                                        <option value="<?= $cat ?>"><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                                    <i class="fas fa-plus-circle me-2"></i>Add New Skill
                                </button>
                            </div>
                        </div>

                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <table id="skillsTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Skill Name</th>
                                    <th>Category</th>
                                    <th>Student Usage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $skills_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['skill_name']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="category-badge"><?= htmlspecialchars($row['category']) ?></span>
                                    </td>
                                    <td>
                                        <div class="skill-usage active">
                                            <i class="fas fa-user-graduate me-1"></i>
                                            <?= $row['active_students_count'] ?> Active
                                        </div>
                                        <div class="skill-usage former">
                                            <i class="fas fa-briefcase me-1"></i>
                                            <?= $row['former_students_count'] ?> Former
                                        </div>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-warning btn-action editBtn"
                                            data-id="<?= $row['id'] ?>"
                                            data-skill="<?= htmlspecialchars($row['skill_name']) ?>"
                                            data-category="<?= $row['category'] ?>"
                                            title="Edit Skill">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-action deleteBtn"
                                            data-id="<?= $row['id'] ?>"
                                            data-skill="<?= htmlspecialchars($row['skill_name']) ?>"
                                            data-category="<?= $row['category'] ?>"
                                            data-active-count="<?= $row['active_students_count'] ?>"
                                            data-former-count="<?= $row['former_students_count'] ?>"
                                            title="Delete Skill">
                                            <i class="fas fa-trash"></i>
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

        <!-- Category Statistics -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Category Statistics</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Total Skills</th>
                                        <th>Active Students</th>
                                        <th>Former Students</th>
                                        <th>Total Usage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($stat = $category_stats->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?= $stat['category'] ?></strong></td>
                                        <td><?= $stat['skill_count'] ?></td>
                                        <td><?= $stat['total_active_students'] ?></td>
                                        <td><?= $stat['total_former_students'] ?></td>
                                        <td><strong><?= $stat['total_active_students'] + $stat['total_former_students'] ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
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
                    <div class="mb-3">
                        <label class="form-label">Skill Name</label>
                        <input type="text" name="skill_name" class="form-control" placeholder="Enter skill name" required maxlength="100">
                        <div class="form-text">Enter a descriptive skill name (max 100 characters)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categoryMap as $cat => $tbl): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Skill
                    </button>
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
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="editCategoryDisplay" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skill Name</label>
                        <input type="text" name="skill_name" class="form-control" required maxlength="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Skill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the skill "<strong id="deleteSkillName"></strong>"?</p>
                <div class="alert alert-warning" id="usageWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="warningText"></span>
                </div>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-2"></i>Delete Skill
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0">Processing...</p>
    </div>
</div>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#skillsTable').DataTable({
        responsive: true,
        order: [[2, 'asc'], [1, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-info'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-warning'
            }
        ],
        language: {
            search: "Search skills:",
            lengthMenu: "Show _MENU_ skills per page",
            info: "Showing _START_ to _END_ of _TOTAL_ skills",
            infoEmpty: "No skills available",
            infoFiltered: "(filtered from _MAX_ total skills)"
        }
    });

    // Category filter
    $('#categoryFilter').on('change', function() {
        table.column(2).search(this.value).draw();
    });

    // Show alert message
    function showAlert(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
        
        // Auto hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    }

    // Show loading
    function showLoading() {
        $('#loadingOverlay').show();
    }

    // Hide loading
    function hideLoading() {
        $('#loadingOverlay').hide();
    }

    // Add skill form
    $('#addSkillForm').submit(function(e) {
        e.preventDefault();
        showLoading();
        
        $.ajax({
            url: '',
            type: 'POST',
            data: $(this).serialize() + '&action=add',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#addSkillModal').modal('hide');
                    $('#addSkillForm')[0].reset();
                    showAlert(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showAlert('An error occurred while adding the skill', 'error');
            }
        });
    });

    // Edit skill
    $(document).on('click', '.editBtn', function() {
        const id = $(this).data('id');
        const skill = $(this).data('skill');
        const category = $(this).data('category');
        
        $('#editSkillForm [name="id"]').val(id);
        $('#editSkillForm [name="skill_name"]').val(skill);
        $('#editSkillForm [name="category"]').val(category);
        $('#editCategoryDisplay').val(category);
        
        $('#editSkillModal').modal('show');
    });

    $('#editSkillForm').submit(function(e) {
        e.preventDefault();
        showLoading();
        
        $.ajax({
            url: '',
            type: 'POST',
            data: $(this).serialize() + '&action=edit',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#editSkillModal').modal('hide');
                    showAlert(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showAlert('An error occurred while updating the skill', 'error');
            }
        });
    });

    // Delete skill
    let deleteSkillId, deleteSkillCategory;
    
    $(document).on('click', '.deleteBtn', function() {
        deleteSkillId = $(this).data('id');
        deleteSkillCategory = $(this).data('category');
        const skillName = $(this).data('skill');
        const activeCount = $(this).data('active-count');
        const formerCount = $(this).data('former-count');
        
        $('#deleteSkillName').text(skillName);
        
        // Show usage warning if skill is being used
        if (activeCount > 0 || formerCount > 0) {
            let warningText = 'This skill is currently assigned to ';
            if (activeCount > 0 && formerCount > 0) {
                warningText += `${activeCount} active student(s) and ${formerCount} former student(s).`;
            } else if (activeCount > 0) {
                warningText += `${activeCount} active student(s).`;
            } else {
                warningText += `${formerCount} former student(s).`;
            }
            warningText += ' Deleting this skill will remove it from all student profiles.';
            
            $('#warningText').text(warningText);
            $('#usageWarning').show();
        } else {
            $('#usageWarning').hide();
        }
        
        $('#deleteSkillModal').modal('show');
    });

    $('#confirmDelete').click(function() {
        showLoading();
        $('#deleteSkillModal').modal('hide');
        
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                id: deleteSkillId,
                category: deleteSkillCategory,
                action: 'delete'
            },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showAlert(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showAlert('An error occurred while deleting the skill', 'error');
            }
        });
    });

    // Reset forms when modals are closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0]?.reset();
    });

    console.log('Skills Management loaded with <?= $total_skills ?> skills across <?= count($categoryMap) ?> categories');
});
</script>
</body>
</html>
<?php $conn->close(); ?>