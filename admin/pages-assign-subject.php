<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}
// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic,mobile,profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch lecturers
$lecturers_result = $conn->query("SELECT * FROM lectures WHERE status='approved'");

// Fetch subjects
$subjects_result = $conn->query("SELECT * FROM subjects");

// Fetch courses for dropdown
$courses_result = $conn->query("SELECT * FROM hnd_courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Subject - EduWide</title>
<?php include_once("../includes/css-links-inc.php"); ?>
<style>
    #lecturer-profile {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-left: 15px;
        display: none;
        object-fit: cover;
        border: 2px solid #333;
    }
</style>
     <style>
        /* Styling for the popup */
        .popup-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            display: none; /* Hidden by default */
            z-index: 9999;
        }

        .error-popup {
            background-color: #dc3545;
        }
    </style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    
    <?php if (isset($_SESSION['status'])): ?>
        <div class="popup-message <?php echo ($_SESSION['status'] == 'success') ? '' : 'error-popup'; ?>" id="popup-alert">
            <?php echo $_SESSION['message']; ?>
        </div>

        <script>
            // Display the popup message
            document.getElementById('popup-alert').style.display = 'block';

            // Automatically hide the popup after 10 seconds
            setTimeout(function() {
                const popupAlert = document.getElementById('popup-alert');
                if (popupAlert) {
                    popupAlert.style.display = 'none';
                }
            }, 1000);
        </script>

        <?php
        // Clear session variables after showing the message
        unset($_SESSION['status']);
        unset($_SESSION['message']);
        ?>
    <?php endif; ?>
    

<?php include_once("../includes/header.php") ?>
<?php include_once("../includes/sadmin-sidebar.php") ?>

<main class="main">
<div class="pagetitle">
    <h1>Assign Subjects to Lecturer</h1>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="container mt-2 mb-2">
                        <h2 class="card-title">Assign Subjects to Lecturer</h2>
                        <form action="assign_subject_process.php" method="POST">
                            <!-- Lecturer Selection -->
                            <div class="form-group d-flex align-items-center mb-3">
                                <label for="lecturer">Lecturer:</label>
                                <select class="form-control w-50" id="lecturer" name="lecturer_id" style="margin-left:10px;">
                                    <option value="">Select Lecturer</option>
                                    <?php
                                    $lecturers_result = $conn->query("SELECT * FROM lectures WHERE status='approved'");
                                    while($lec = $lecturers_result->fetch_assoc()) { ?>
                                        <option value="<?= $lec['id'] ?>" data-profile="<?= '../lectures/' . $lec['profile_picture'] ?>">
    <?= htmlspecialchars($lec['username']) ?>
</option>

                                            <?= htmlspecialchars($lec['username']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <img id="lecturer-profile" src="" alt="Profile Picture" style="width:200px; height:200px; border-radius:10%; margin-left:15px; display:none; object-fit:cover; ">
                            </div>

                           <!-- Course Selection -->
<div class="form-group mb-3">
    <label for="course">Course:</label>
    <select class="form-control w-50" id="course" name="course_id">
        <option value="">Select Course</option>
        <?php
        // Fetch unique courses from the subjects table
        $courses_result = $conn->query("SELECT DISTINCT course FROM subjects WHERE course != '' ORDER BY course ASC");
        while($course = $courses_result->fetch_assoc()){
            echo "<option value='{$course['course']}'>{$course['course']}</option>";
        }
        ?>
    </select>
</div>

                            <!-- Subjects -->
                            <div id="subjects-container" class="mb-3">
                                <label>Subjects:</label><br>
                                <?php
                                $subjects_result = $conn->query("SELECT * FROM subjects");
                                while ($sub = $subjects_result->fetch_assoc()) {
                                    echo "<div class='form-check'>
                                            <input class='form-check-input subject-checkbox' type='checkbox' name='subject_ids[]' value='{$sub['id']}' data-course='{$sub['course']}'>
                                            <label class='form-check-label'>{$sub['code']} - {$sub['name']}</label>
                                          </div>";
                                }
                                ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Assign Subjects</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function(){
    // Show lecturer profile picture
    $("#lecturer").change(function(){
        let profile = $("#lecturer option:selected").data("profile");
        if(profile){
            $("#lecturer-profile").attr("src", "../" + profile).show();
        } else {
            $("#lecturer-profile").hide();
        }
    });

    // Filter subjects based on selected course
    $("#course").change(function(){
        let selectedCourse = $(this).val();
        $(".subject-checkbox").each(function(){
            let course = $(this).data("course");
            if(selectedCourse === "" || course === selectedCourse){
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });
});
</script>

       <?php include_once("../includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once("../includes/js-links-inc.php") ?>
</body>
</html>

<?php $conn->close(); ?>
