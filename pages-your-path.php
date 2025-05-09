<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['student_id'];

$sql2 = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); 

$user_id = $_SESSION['student_id'];
$summary = "";

/* Fetch summary from the separate table
$sql = "SELECT summary FROM summaries WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($summary);
$stmt->fetch();
$stmt->close();

// Fetch about_text
$about_text = '';
$sql = "SELECT about_text FROM about WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($about_text);
$stmt->fetch();
$stmt->close();*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Details - EduWide</title>
    <?php include_once("includes/css-links-inc.php"); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script> 
    <style>
        .profile-header {
            background-color: #0073b1;
            color: white;
            padding: 30px;
        }
        .profile-header img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-right: 20px;
        }
        .profile-header h1 {
            margin-top: 20px;
        }
        .section-header {
            font-size: 1.5em;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .list-group-item {
            border: none;
            cursor: grab;
        }
        .card-body p {
            margin: 5px 0;
        }
        .btn-custom {
            background-color: #0073b1;
            color: white;
            border-radius: 20px;
        }
        .btn-custom:hover {
            background-color: #005f8c;
        }
        .experience-section, .education-section, .skills-section, .interests-section {
            margin-top: 30px;
        }
        #work-experience-list .list-group-item {
            user-select: none; 
           
        }
         .summary-card {
          border-radius: 10px;
          background-color: #fff;
        }
        .summary-icon {
          background-color: #e6f0ff;
          border-radius: 8px;
          display: inline-block;
    }
        .badge + .badge {
          margin-left: 0.25rem;
        }

        /* Work Experience Section */
        #experience-list {
            padding: 0;
        }

        .experience-card {
            background-color: #f9f9f9;
            border: 1px solid #e1e4e8;
            width: 60%;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .experience-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .experience-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0073b1;
        }

        .experience-company {
            font-size: 1rem;
            color: #444;
        }

        .experience-details {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #555;
        }

        .experience-location,
        .experience-dates {
            margin-right: 20px;
        }

        .experience-description {
            margin-top: 10px;
            font-size: 1rem;
            color: #333;
        }

        .experience-footer {
            margin-top: 10px;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .experience-type,
        .experience-source {
            color: #888;
        }

        .experience-type {
            font-weight: bold;
        }



        #education-list .education-card {
            border-radius: 8px;
            background-color: #f9f9f9;
            border: 1px solid #dfe3e8;
            width: 60%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #education-list .education-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }


        #education-list .card-title {
            font-size: 1.25rem;
            color: #0073b1;
            font-weight: bold;
        }

        #education-list .card-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        #education-list .education-details {
            font-size: 0.875rem;
            color: #495057;
        }

        #education-list .education-details span {
            margin-right: 1rem;
        }

        #education-list .education-description p {
            font-size: 1rem;
            color: #495057;
            line-height: 1.5;
            margin-top: 0.2rem;
        }




    </style>
</head>
<body>

    <?php include_once("includes/header.php") ?>
    <?php include_once("includes/students-sidebar.php") ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Home</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item"><a href="">Details</a></li>
                    <li class="breadcrumb-item"><a href="">Your Path</a></li>
                </ol>
            </nav>
        </div>
                <section class="section">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                
                                        <div class="education-section">
                                            <h3 class="section-header">Education</h3>
                                            <ul class="list-group" id="education-list">
                                            </ul>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#educationModal">Add Education</button>
                                        </div>

                                            <div>
                                                <ul class="list-group" id="education-list">
                                                    <?php
                                                    // Fetch education data from the database
                                                    $query = "SELECT * FROM students_education WHERE user_id = ?";
                                                    $stmt = $conn->prepare($query);
                                                    $stmt->bind_param('i', $user_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo '
                                                            <li class="list-group-item border-0">
                                                                <div class="education-card card mb-4 shadow-sm">
                                                                    <div class="card-body p-4">
                                                                        <h5 class="card-title mb-2 font-weight-bold">' .htmlspecialchars($row['degree']) . ' <span class="">&emsp; From &emsp; </span> <font color="red">' . htmlspecialchars($row['school']) . '</font></h5>
                                                                        <h6 class="card-subtitle mb-3 text-muted">' . htmlspecialchars($row['field_of_study']) . '</h6>
                                                                        <div class="education-details mb-3">
                                                                            <span class="text-muted">' . htmlspecialchars($row['start_month']) . ' ' . $row['start_year'] . ' - ' . htmlspecialchars($row['end_month']) . ' ' . $row['end_year'] . '</span>
                                                                            <span class="ml-3 text-muted">Grade: ' . htmlspecialchars($row['grade']) . '</span>
                                                                        </div>
                                                                        <p class="card-text mb-2"><strong>Activities & Societies:</strong> ' . htmlspecialchars($row['activities']) . '</p>
                                                                        <div class="education-description">
                                                                            <p>' . nl2br(htmlspecialchars($row['description'])) . '</p>
                                                                        </div>

                                                                        <!-- Edit and Delete buttons -->
                                                                        <div class="education-actions mt-3">
                                                                            <a href="edit_education.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                                                            <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['id'] . '">Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>';
                                                        }
                                                    } else {
                                                        echo '<p>No education information added yet.</p>';
                                                    }
                                                    ?>

                                                </ul>
                                            </div>

                                            <script>
                                                document.querySelectorAll('.delete-btn').forEach(function (button) {
                                                    button.addEventListener('click', function () {
                                                        var educationId = this.getAttribute('data-id');

                                                        if (confirm('Are you sure you want to delete this education record?')) {
                                                            var xhr = new XMLHttpRequest();
                                                            xhr.open('POST', 'delete_education.php', true);
                                                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                                            xhr.onload = function () {
                                                                if (xhr.status === 200) {
                                                                    var response = JSON.parse(xhr.responseText);
                                                                    if (response.status === 'success') {
                                                                        alert(response.message);
                                                                        // Remove the card or section from the DOM
                                                                        document.getElementById('education-' + educationId).remove();
                                                                        location.reload();
                                                                    } else {
                                                                        alert(response.message);
                                                                    }
                                                                } else {
                                                                    alert('An error occurred while deleting the record.');
                                                                }
                                                            };
                                                            xhr.send('education_id=' + educationId); 
                                                        }
                                                    });
                                                });
                                                </script>

                                            

 

                                        <div class="modal fade" id="educationModal" tabindex="-1" aria-labelledby="educationModalLabel" aria-hidden="true">
                                              <div class="modal-dialog modal-lg">
                                                <form class="modal-content" id="education-form" method="POST" action="save_education.php">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title" id="educationModalLabel">Add Education</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>

                                                  <div class="modal-body row g-3">
                                                    <!-- Basic Education Inputs -->
                                                    <div class="col-md-6">
                                                      <label for="school" class="form-label">School*</label>
                                                      <input type="text" class="form-control" id="school" name="school" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="degree" class="form-label">Degree</label>
                                                      <input type="text" class="form-control" id="degree" name="degree">
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="field" class="form-label">Field of Study</label>
                                                      <input type="text" class="form-control" id="field" name="field">
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="start-month" class="form-label">Start Month</label>
                                                      <select class="form-select" id="start-month" name="start_month">
                                                        <option value="">--Month--</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                      </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="start-year" class="form-label">Start Year</label>
                                                      <input type="number" class="form-control" id="start-year" name="start_year">
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="end-month" class="form-label">End Month</label>
                                                      <select class="form-select" id="end-month" name="end_month">
                                                        <option value="">--Month--</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <option value="December">December</option>
                                                      </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <label for="end-year" class="form-label">End Year</label>
                                                      <input type="number" class="form-control" id="end-year" name="end_year">
                                                    </div>

                                                    <div class="col-md-6">
                                                      <label for="grade" class="form-label">Grade</label>
                                                      <input type="text" class="form-control" id="grade" name="grade">
                                                    </div>
                                                    <div class="col-md-6">
                                                      <label for="activities" class="form-label">Activities and Societies</label>
                                                      <input type="text" class="form-control" id="activities" name="activities" maxlength="500">
                                                    </div>
                                                    <div class="col-12">
                                                      <label for="edu-description" class="form-label">Description</label>
                                                      <textarea class="form-control" id="edu-description" name="description" rows="3" maxlength="1000"></textarea>
                                                    </div>

                                                  <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                  </div>
                                                </form>
                                              </div>
                                            </div>
                                            <div>

                                    </div>

                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once("includes/footer.php") ?>
    <?php include_once ("includes/js-links-inc.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>
<?php
$conn->close();
?>
