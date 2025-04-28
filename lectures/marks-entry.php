<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['lecturer_id'];
$sql = "SELECT * FROM lectures WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get the subject ID from the URL
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    // Fetch the subject details from the database
    $subject_sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($subject_sql);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $subject_result = $stmt->get_result();
    $subject = $subject_result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Marks Entry - <?php echo htmlspecialchars($subject['name']); ?> - EduWide</title>

    <?php include_once("../includes/css-links-inc.php"); ?>
    <style type="text/css">
        #profilePictureDiv {
        margin-top: 15px;
        }

        #profilePictureContainer img {
            border: 1px solid #ddd;
            padding: 5px;
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
</head>

<body>
    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/lectures-sidebar.php") ?>

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

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Marks Entry for <?php echo htmlspecialchars($subject['name']); ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">Semesters</a></li>
                    <li class="breadcrumb-item active">Marks Entry</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="process-marks-entry.php" method="POST">
                                <!-- Batch Year Dropdown -->
                                <div class="form-group mb-3 mt-3">
                                    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
                                    <label for="year">Select Batch Year</label>
                                    <select class="form-select w-25" id="year" name="year" required>
                                        <option value="">Select Batch Year</option>
                                        <?php
                                        // Fetch distinct batch years from students table
                                        $year_sql = "SELECT DISTINCT study_year FROM students ORDER BY study_year DESC";
                                        $year_result = $conn->query($year_sql);

                                        if ($year_result->num_rows > 0) {
                                            while ($row = $year_result->fetch_assoc()) {
                                                echo "<option value='{$row['study_year']}'>{$row['study_year']}</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No Data Available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3" id="profilePictureDiv" style="display: none;">
                                    <label for="profilePicture">Student Picture</label>
                                    <div id="profilePictureContainer">
                                        <!-- Profile Picture will be inserted here -->
                                    </div>
                                </div>

                                <!-- Student ID Dropdown -->
                                <div class="form-group mb-3">
                                    <label for="studentId">Select Student ID</label>
                                    <select class="form-select w-50" id="studentId" name="studentId" required>
                                        <option value="">... Select Student ID...</option>
                                    </select>
                                </div>

                                <!-- Subject (Readonly) -->
                                <div class="form-group mb-3 mt-3">
                                    <label for="subject">Subject</label>
                                    <input type="text" class="form-control w-50" id="subject" name="subject" value="<?php echo htmlspecialchars($subject['name']); ?>" readonly>
                                </div>
                                <div class="form-group mb-3 mt-3">
                                    <label for="subject">Semester</label>
                                    <input type="text" class="form-control w-50" id="semester" name="semestersubject" value="<?php echo htmlspecialchars($subject['semester']); ?>" readonly>
                                </div>

                                <!-- Practical Marks -->
                                <div class="form-group mb-3">
                                    <label for="practicalMarks">Practical Marks</label>
                                    <input type="number" class="form-control w-50" id="practicalMarks" name="practicalMarks" placeholder="Enter Practical Marks" min="0" max="100" >
                                    <div id="practical-error-message" class="text-danger mt-2" style="display: none;">Please enter a value between 0 and 100.</div>
                                </div>

                                <!-- Paper Marks -->
                                <div class="form-group mb-3">
                                    <label for="paperMarks">Paper Marks</label>
                                    <input type="number" class="form-control w-50" id="paperMarks" name="paperMarks" placeholder="Enter Paper Marks" min="0" max="100">
                                    <div id="paper-error-message" class="text-danger mt-2" style="display: none;">Please enter a value between 0 and 100.</div>
                                </div>

                                <!-- Special Notes -->
                                <div class="form-group mb-3">
                                    <label for="specialnotes">Special Notes</label>
                                    <textarea class="form-control w-50" id="specialnotes" name="specialnotes" rows="4" placeholder="Enter special notes"></textarea>
                                </div>

                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const practicalMarksInput = document.getElementById('practicalMarks');
                                    const paperMarksInput = document.getElementById('paperMarks');
                                    
                                    const practicalErrorMessageDiv = document.getElementById('practical-error-message');
                                    const paperErrorMessageDiv = document.getElementById('paper-error-message');
                                    
                                    // Event listener to check the practical marks input
                                    practicalMarksInput.addEventListener('input', function() {
                                        const value = parseInt(practicalMarksInput.value, 10);

                                        // Check if the value is within the valid range
                                        if (value < 0 || value > 100) {
                                            // Show the error message div for practical marks
                                            practicalErrorMessageDiv.style.display = 'block';
                                            // Clear the input field and reset styles
                                            practicalMarksInput.value = '';  
                                            practicalMarksInput.style.backgroundColor = '';  
                                            practicalMarksInput.style.color = '';  
                                        } else {
                                            // Hide the error message div for practical marks
                                            practicalErrorMessageDiv.style.display = 'none';

                                            // Change the background color and font color based on the value
                                            if (value >= 90) {
                                                practicalMarksInput.style.backgroundColor = 'green';
                                                practicalMarksInput.style.color = 'white';
                                            } else if (value >= 75) {
                                                practicalMarksInput.style.backgroundColor = 'lightgreen';
                                                practicalMarksInput.style.color = 'white';
                                            } else if (value >= 65) {
                                                practicalMarksInput.style.backgroundColor = 'yellow';
                                                practicalMarksInput.style.color = 'black';
                                            } else if (value >= 35) {
                                                practicalMarksInput.style.backgroundColor = 'orange';
                                                practicalMarksInput.style.color = 'white';
                                            } else {
                                                practicalMarksInput.style.backgroundColor = 'red';
                                                practicalMarksInput.style.color = 'white';
                                            }
                                        }
                                    });

                                    // Event listener to check the paper marks input
                                    paperMarksInput.addEventListener('input', function() {
                                        const value = parseInt(paperMarksInput.value, 10);

                                        // Check if the value is within the valid range
                                        if (value < 0 || value > 100) {
                                            // Show the error message div for paper marks
                                            paperErrorMessageDiv.style.display = 'block';
                                            // Clear the input field and reset styles
                                            paperMarksInput.value = '';  
                                            paperMarksInput.style.backgroundColor = '';  
                                            paperMarksInput.style.color = '';  
                                        } else {
                                            // Hide the error message div for paper marks
                                            paperErrorMessageDiv.style.display = 'none';

                                            // Change the background color and font color based on the value
                                            if (value >= 90) {
                                                paperMarksInput.style.backgroundColor = 'green';
                                                paperMarksInput.style.color = 'white';
                                            } else if (value >= 75) {
                                                paperMarksInput.style.backgroundColor = 'lightgreen';
                                                paperMarksInput.style.color = 'white';
                                            } else if (value >= 65) {
                                                paperMarksInput.style.backgroundColor = 'yellow';
                                                paperMarksInput.style.color = 'black';
                                            } else if (value >= 35) {
                                                paperMarksInput.style.backgroundColor = 'orange';
                                                paperMarksInput.style.color = 'white';
                                            } else {
                                                paperMarksInput.style.backgroundColor = 'red';
                                                paperMarksInput.style.color = 'white';
                                            }
                                        }
                                    });
                                });
                                </script>

                                <button type="submit" class="btn btn-primary mt-3">Submit Marks</button>
                                <button type="reset" class="btn btn-danger mt-3">Clear</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
           <script>
            document.addEventListener("DOMContentLoaded", function() {
            const yearSelect = document.getElementById('year');
            const studentSelect = document.getElementById('studentId');
            const profilePictureDiv = document.getElementById('profilePictureDiv');
            const profilePictureContainer = document.getElementById('profilePictureContainer');
            
            let students = [];

            yearSelect.addEventListener('change', function() {
                const selectedYear = this.value;

                studentSelect.innerHTML = '<option value="">Select Student...</option>'; // Reset the student dropdown

                if (selectedYear) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'ajax-get-students.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            students = JSON.parse(xhr.responseText); // Save the student data

                            students.forEach(function(student) {
                                const option = document.createElement('option');
                                option.value = student.reg_id;
                                option.textContent = `${student.reg_id} - ${student.username}`;
                                studentSelect.appendChild(option);
                            });
                        }
                    };
                    xhr.send('year=' + selectedYear);
                }
            });

            studentSelect.addEventListener('change', function() {
                const selectedStudentId = this.value;

                profilePictureDiv.style.display = 'none';  // Hide profile picture div by default

                if (selectedStudentId) {
                    // Find the selected student from the students array
                    const selectedStudent = students.find(student => student.reg_id === selectedStudentId);

                    if (selectedStudent) {
                        // Display the profile picture
                        if (selectedStudent.profile_picture) {
                            const profilePicture = document.createElement('img');
                            profilePicture.src = `../${selectedStudent.profile_picture}`;
                            profilePicture.alt = "Profile Picture";
                            profilePicture.style.width = '180px';
                            profilePicture.style.height = '180px';

                            profilePictureContainer.innerHTML = '';  // Clear previous profile picture
                            profilePictureContainer.appendChild(profilePicture);
                            profilePictureDiv.style.display = 'block';  // Show the profile picture div
                        } else {
                            profilePictureContainer.innerHTML = 'No profile picture available.';
                            profilePictureDiv.style.display = 'block';  // Show the profile picture div
                        }
                    }
                }
            });
        });
          </script>

          <script>
            $(document).ready(function() {
                // On form submit
                $("#signup-form").submit(function(event) {
                    event.preventDefault(); // Prevent form submission

                    $.ajax({
                        url: "process.marks-entry.php", // Send form data to register.php
                        type: "POST",
                        data: $(this).serialize(), // Serialize the form data
                        dataType: "json", // Expect JSON response
                        success: function(response) {
                            let popupAlert = $("#popup-alert");

                            // Set the message class and text based on the response status
                            if (response.status === "success") {
                                popupAlert.removeClass("alert-error").addClass("alert-success").html(response.message);
                            } else {
                                popupAlert.removeClass("alert-success").addClass("alert-error").html(response.message);
                            }

                            // Show the alert
                            popupAlert.show();

                            // Hide the alert after 10 seconds
                            setTimeout(function() {
                                popupAlert.fadeOut();
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            alert("AJAX Error: " + xhr.responseText); // Handle AJAX error
                        }
                    });
                });
            });
        </script>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>
    <?php include_once("../includes/js-links-inc.php") ?>


</body>
</html>

