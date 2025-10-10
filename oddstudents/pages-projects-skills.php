<?php
session_start();
require_once '../includes/db-conn.php';

// Enable detailed error reporting (remove on production)
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Check login
if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['former_student_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT * FROM former_students WHERE id=?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) die("Student not found!");

// --- SKILLS SECTION ---
$skills_sql = "
    SELECT id, skill_name, 'IT' AS category FROM it_student_skills
    UNION ALL
    SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
    ORDER BY category, skill_name
";
$skills_result = $conn->query($skills_sql);

// Fetch already added skills with IDs for removal
$added_skills_stmt = $conn->prepare("
    SELECT s.id, s.skill_name, s.category 
    FROM former_student_skills fs 
    JOIN (
        SELECT id, skill_name, 'IT' AS category FROM it_student_skills
        UNION ALL
        SELECT id, skill_name, 'Engineering' AS category FROM engineering_skills
    ) s ON fs.skill_id = s.id
    WHERE fs.student_id=?
");
$added_skills_stmt->bind_param("i", $student_id);
$added_skills_stmt->execute();
$added_skills_result = $added_skills_stmt->get_result();
$added_skills = $added_skills_result->fetch_all(MYSQLI_ASSOC);
$added_skills_stmt->close();

$skills_success = $skills_error = '';
if (isset($_POST['submit_skills'])) {
    $selected_skills = $_POST['skills'] ?? [];
    if (empty($selected_skills)) {
        $skills_error = "Please select at least one skill.";
    } elseif (count($selected_skills) > 20) {
        $skills_error = "You can select a maximum of 20 skills!";
    } else {
        foreach ($selected_skills as $skill_id) {
            $check = $conn->prepare("SELECT id FROM former_student_skills WHERE student_id=? AND skill_id=?");
            $check->bind_param("ii", $student_id, $skill_id);
            $check->execute();
            $check_result = $check->get_result();
            if ($check_result->num_rows == 0) {
                $stmt = $conn->prepare("INSERT INTO former_student_skills (student_id, skill_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $student_id, $skill_id);
                $stmt->execute();
                $stmt->close();
            }
            $check->close();
        }
        $skills_success = "Skills added successfully!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle skill removal
if (isset($_POST['remove_skill'])) {
    $skill_id_to_remove = $_POST['skill_id'];
    $stmt = $conn->prepare("DELETE FROM former_student_skills WHERE student_id=? AND skill_id=?");
    $stmt->bind_param("ii", $student_id, $skill_id_to_remove);
    if ($stmt->execute()) {
        $skills_success = "Skill removed successfully!";
    } else {
        $skills_error = "Failed to remove skill!";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- PROJECTS SECTION ---
$project_success = $project_error = '';
if (isset($_POST['submit_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);

    if (empty($title) || empty($description)) {
        $project_error = "Title and description are required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO former_student_projects (student_id, title, description, project_link, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $student_id, $title, $description, $link);
        if ($stmt->execute()) {
            $project_id = $stmt->insert_id;
            $upload_dir = "../uploads/projects/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            if (!empty($_FILES['project_images']['name'][0])) {
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $total_files = min(count($_FILES['project_images']['name']), 5);

                for ($i = 0; $i < $total_files; $i++) {
                    $filename = $_FILES['project_images']['name'][$i];
                    $tmp = $_FILES['project_images']['tmp_name'][$i];
                    $size = $_FILES['project_images']['size'][$i];
                    $error = $_FILES['project_images']['error'][$i];
                    
                    // Skip if upload error
                    if ($error !== UPLOAD_ERR_OK) continue;
                    
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed_ext)) continue;
                    if ($size > 5 * 1024 * 1024) continue;

                    $new_name = uniqid("project_") . ".$ext";
                    if (move_uploaded_file($tmp, $upload_dir . $new_name)) {
                        $img_stmt = $conn->prepare("INSERT INTO former_student_project_photos (project_id, image_path) VALUES (?, ?)");
                        $path = "uploads/projects/" . $new_name;
                        $img_stmt->bind_param("is", $project_id, $path);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }

            $project_success = "Project added successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $project_error = "Failed to add project!";
        }
        $stmt->close();
    }
}

// Handle project deletion
if (isset($_POST['delete_project'])) {
    $project_id_to_delete = $_POST['project_id'];
    
    // First delete project photos
    $img_stmt = $conn->prepare("SELECT image_path FROM former_student_project_photos WHERE project_id=?");
    $img_stmt->bind_param("i", $project_id_to_delete);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    
    while ($img = $img_result->fetch_assoc()) {
        if (file_exists('../' . $img['image_path'])) {
            unlink('../' . $img['image_path']);
        }
    }
    $img_stmt->close();
    
    // Delete photo records
    $delete_img_stmt = $conn->prepare("DELETE FROM former_student_project_photos WHERE project_id=?");
    $delete_img_stmt->bind_param("i", $project_id_to_delete);
    $delete_img_stmt->execute();
    $delete_img_stmt->close();
    
    // Delete project
    $delete_stmt = $conn->prepare("DELETE FROM former_student_projects WHERE id=? AND student_id=?");
    $delete_stmt->bind_param("ii", $project_id_to_delete, $student_id);
    if ($delete_stmt->execute()) {
        $project_success = "Project deleted successfully!";
    } else {
        $project_error = "Failed to delete project!";
    }
    $delete_stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch student's projects with image count
$projects_stmt = $conn->prepare("
    SELECT p.*, COUNT(pp.id) as image_count 
    FROM former_student_projects p 
    LEFT JOIN former_student_project_photos pp ON p.id = pp.project_id 
    WHERE p.student_id=? 
    GROUP BY p.id 
    ORDER BY p.created_at DESC
");
$projects_stmt->bind_param("i", $student_id);
$projects_stmt->execute();
$projects_result = $projects_stmt->get_result();
$projects_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($student['username']) ?> - Skills & Projects</title>
<?php include_once("../includes/css-links-inc.php"); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --success-color: #4cc9f0;
    --dark-color: #1d3557;
    --light-color: #f8f9fa;
}

.skill-container { 
    max-height: 400px; 
    overflow-y: auto; 
    border: 1px solid #e0e0e0; 
    padding: 15px; 
    border-radius: 10px; 
    background: #fafafa;
}
.skill-checkbox { 
    display: block; 
    padding: 8px 12px;
    margin-bottom: 5px; 
    border-radius: 6px;
    transition: all 0.2s;
    cursor: pointer;
}
.skill-checkbox:hover {
    background: #e9ecef;
}
.skill-badge { 
    margin-right:8px; 
    margin-bottom:8px; 
    display:inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
}
.category-badge {
    font-size: 0.7rem;
    background: rgba(0,0,0,0.1);
    padding: 2px 6px;
    border-radius: 10px;
}
.project-card { 
    border: 1px solid #e0e0e0; 
    padding: 20px; 
    margin-bottom: 20px; 
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.project-photo { 
    width: 90px; 
    height: 90px; 
    object-fit: cover; 
    margin: 5px; 
    border-radius: 8px; 
    cursor: pointer; 
    transition: 0.3s; 
    border: 1px solid #ddd;
}
.project-photo:hover { 
    transform: scale(1.05); 
}
.skill-category {
    background: var(--primary-color);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 10px 0 5px 0;
    font-weight: bold;
    font-size: 0.9rem;
}
.upload-area {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    background: #fafafa;
    transition: all 0.3s;
    cursor: pointer;
}
.upload-area:hover, .upload-area.dragover {
    border-color: var(--primary-color);
    background: #f0f4ff;
}
.stats-card {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}
.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0;
}
.image-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}
.preview-item {
    position: relative;
    width: 80px;
    height: 80px;
}
.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}
.preview-item .remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-img {
    width: 100%;
    border-radius: 8px;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #dee2e6;
}
.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}
.project-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    font-size: 0.85rem;
}
</style>
</head>
<body>
<?php include_once("../includes/header.php"); ?>
<?php include_once("../includes/formers-sidebar.php"); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Your Skills & Projects</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Skills & Projects</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Skills</h6>
                        <p class="stats-number"><?= count($added_skills) ?></p>
                    </div>
                    <i class="fas fa-code fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Projects</h6>
                        <p class="stats-number"><?= $projects_result->num_rows ?></p>
                    </div>
                    <i class="fas fa-project-diagram fa-2x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- SKILLS SECTION -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Skills Management</h4>
                            <span class="badge bg-primary">Max 20</span>
                        </div>
                        
                        <?php if($skills_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?= $skills_success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if($skills_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $skills_error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Current Skills -->
                        <?php if(!empty($added_skills)): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Your Current Skills:</label>
                                <div class="d-flex flex-wrap">
                                    <?php foreach($added_skills as $s): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="skill_id" value="<?= $s['id'] ?>">
                                            <input type="hidden" name="remove_skill" value="1">
                                            <span class="skill-badge bg-success text-white">
                                                <?= htmlspecialchars($s['skill_name']) ?>
                                                <span class="category-badge"><?= $s['category'] ?></span>
                                                <button type="submit" class="btn-close btn-close-white" style="font-size: 0.7rem;" onclick="return confirm('Remove this skill?')"></button>
                                            </span>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No skills added yet. Start by adding some skills below.
                            </div>
                        <?php endif; ?>

                        <!-- Add Skills Form -->
                        <form method="post">
                            <input type="hidden" name="submit_skills" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Add New Skills:</label>
                                <input type="text" id="skillFilter" class="form-control" placeholder="Type to filter skills...">
                            </div>
                            <div class="skill-container" id="skillsList">
                                <?php
                                $current_category = '';
                                $skills_result->data_seek(0);
                                while($skill = $skills_result->fetch_assoc()): 
                                    if ($current_category != $skill['category']):
                                        $current_category = $skill['category'];
                                ?>
                                        <div class="skill-category"><?= $skill['category'] ?> Skills</div>
                                <?php endif; ?>
                                    <label class="skill-checkbox">
                                        <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>" 
                                            <?= in_array($skill['id'], array_column($added_skills, 'id')) ? 'disabled' : '' ?>>
                                        <?= htmlspecialchars($skill['skill_name']) ?>
                                        <?php if(in_array($skill['id'], array_column($added_skills, 'id'))): ?>
                                            <small class="text-success ms-2"><i class="fas fa-check"></i> Added</small>
                                        <?php endif; ?>
                                    </label>
                                <?php endwhile; ?>
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <small class="text-muted" id="selectedCount">0 skills selected</small>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Selected Skills
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PROJECTS SECTION -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Add New Project</h4>
                        <?php if($project_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?= $project_success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if($project_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $project_error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" id="projectForm">
                            <input type="hidden" name="submit_project" value="1">
                            <div class="mb-3">
                                <label class="form-label">Project Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="3" required maxlength="500" placeholder="Describe your project..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Link (optional)</label>
                                <input type="url" name="link" class="form-control" placeholder="https://...">
                            </div>
                            
                            <!-- Enhanced File Upload -->
                            <div class="mb-3">
                                <label class="form-label">Project Images (Max 5)</label>
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1">Drag & drop images here or click to browse</p>
                                    <small class="text-muted">Supports JPG, PNG, GIF, WEBP (Max 5MB each)</small>
                                    <input type="file" name="project_images[]" id="projectImages" class="d-none" multiple accept="image/*">
                                </div>
                                <div class="image-preview" id="imagePreview"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-rocket me-2"></i>Add Project
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="mt-4">
                    <h4 class="mb-3">My Projects (<?= $projects_result->num_rows ?>)</h4>
                    
                    <?php if($projects_result->num_rows > 0): ?>
                        <?php while($project = $projects_result->fetch_assoc()): ?>
                            <div class="project-card">
                                <div class="project-header">
                                    <h5 class="mb-0"><?= htmlspecialchars($project['title']) ?></h5>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                        <input type="hidden" name="delete_project" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <p class="text-muted mb-2"><?= htmlspecialchars($project['description']) ?></p>
                                
                                <?php if($project['project_link']): ?>
                                    <div class="mb-2">
                                        <a href="<?= htmlspecialchars($project['project_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>View Project
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="project-meta">
                                    <small><i class="far fa-calendar me-1"></i><?= date('M j, Y', strtotime($project['created_at'])) ?></small>
                                    <small><i class="far fa-image me-1"></i><?= $project['image_count'] ?> images</small>
                                </div>

                                <!-- Project Images -->
                                <?php
                                $photos_stmt = $conn->prepare("SELECT * FROM former_student_project_photos WHERE project_id=?");
                                $photos_stmt->bind_param("i", $project['id']);
                                $photos_stmt->execute();
                                $photos_result = $photos_stmt->get_result();
                                ?>
                                <?php if($photos_result->num_rows > 0): ?>
                                    <div class="mt-3">
                                        <?php while($photo = $photos_result->fetch_assoc()): ?>
                                            <img src="../<?= htmlspecialchars($photo['image_path']) ?>" 
                                                 class="project-photo" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 data-img="../<?= htmlspecialchars($photo['image_path']) ?>"
                                                 title="Click to view larger">
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>
                                <?php $photos_stmt->close(); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h5>No Projects Yet</h5>
                            <p class="text-muted">Start by adding your first project above!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Project Image" class="modal-img">
            </div>
        </div>
    </div>
</div>

<?php include_once("../includes/footer.php"); ?>
<?php include_once("../includes/js-links-inc.php"); ?>

<script>
document.addEventListener("DOMContentLoaded", function(){
    // Skill filter
    const filterInput = document.getElementById('skillFilter');
    const skillsList = document.getElementById('skillsList');
    const selectedCount = document.getElementById('selectedCount');
    
    filterInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        let currentCategory = '';
        let categoryVisible = false;
        
        skillsList.querySelectorAll('.skill-checkbox, .skill-category').forEach(function(element){
            if(element.classList.contains('skill-category')) {
                currentCategory = element;
                categoryVisible = false;
            } else {
                const show = element.textContent.toLowerCase().includes(filter);
                element.style.display = show ? 'block' : 'none';
                if(show) categoryVisible = true;
            }
        });
        
        // Show/hide categories based on visible skills
        skillsList.querySelectorAll('.skill-category').forEach(function(category){
            let hasVisibleSkills = false;
            let nextElement = category.nextElementSibling;
            
            while(nextElement && !nextElement.classList.contains('skill-category')) {
                if(nextElement.style.display !== 'none') {
                    hasVisibleSkills = true;
                    break;
                }
                nextElement = nextElement.nextElementSibling;
            }
            
            category.style.display = hasVisibleSkills ? 'block' : 'none';
        });
    });

    // Skill selection counter
    function updateSelectedCount() {
        const checkedCount = skillsList.querySelectorAll('input[type="checkbox"]:checked:not(:disabled)').length;
        selectedCount.textContent = `${checkedCount} skills selected`;
        
        if(checkedCount >= 20) {
            selectedCount.className = 'text-danger';
        } else if(checkedCount > 15) {
            selectedCount.className = 'text-warning';
        } else {
            selectedCount.className = 'text-muted';
        }
    }
    
    skillsList.addEventListener('change', function(e) {
        if(e.target.type === 'checkbox') {
            const checkedCount = skillsList.querySelectorAll('input[type="checkbox"]:checked:not(:disabled)').length;
            if(checkedCount > 20){
                alert("You can select maximum 20 skills!");
                e.target.checked = false;
            }
            updateSelectedCount();
        }
    });
    
    updateSelectedCount();

    // Enhanced file upload functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('projectImages');
    const imagePreview = document.getElementById('imagePreview');
    const maxFiles = 5;
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if(e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(fileInput.files);
        }
    });
    
    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });
    
    function handleFiles(files) {
        const currentFiles = imagePreview.children.length;
        const availableSlots = maxFiles - currentFiles;
        
        if(files.length > availableSlots) {
            alert(`You can only upload ${availableSlots} more image(s). Maximum ${maxFiles} images allowed.`);
        }
        
        for(let i = 0; i < Math.min(files.length, availableSlots); i++) {
            const file = files[i];
            if(!file.type.match('image.*')) continue;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn">&times;</button>
                `;
                imagePreview.appendChild(previewItem);
                
                previewItem.querySelector('.remove-btn').addEventListener('click', () => {
                    previewItem.remove();
                    updateFileInput();
                });
            };
            reader.readAsDataURL(file);
        }
        updateFileInput();
    }
    
    function updateFileInput() {
        // This would require more complex handling to update the actual file input
        // For now, we'll just show visual feedback
        const previewCount = imagePreview.children.length;
        if(previewCount >= maxFiles) {
            uploadArea.style.display = 'none';
        } else {
            uploadArea.style.display = 'block';
        }
    }

    // Image modal functionality
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    const modalImage = document.getElementById('modalImage');
    
    document.querySelectorAll('.project-photo').forEach(img => {
        img.addEventListener('click', () => {
            modalImage.src = img.getAttribute('data-img');
        });
    });

    // Form validation
    const projectForm = document.getElementById('projectForm');
    projectForm.addEventListener('submit', function(e) {
        const title = this.querySelector('input[name="title"]').value.trim();
        const description = this.querySelector('textarea[name="description"]').value.trim();
        
        if(!title || !description) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }
        
        const previewCount = imagePreview.children.length;
        if(previewCount === 0) {
            if(!confirm('You haven\'t added any images. Continue without images?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>