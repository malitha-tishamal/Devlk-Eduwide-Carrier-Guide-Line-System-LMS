<?php
session_start();
require_once '../includes/db-conn.php';
if (!isset($_SESSION['former_student_id'])) {
    header("Location: ../index.php");
    exit();
}
$user_id = $_SESSION['former_student_id'];
$sql2 = "SELECT * FROM former_students WHERE id = ?";
$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$about_text = '';

$sql = "SELECT about_text FROM about WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($about_text);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Details - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
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

    </style>
</head>
<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/formers-sidebar.php") ?>
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
                                <div class="about-section">
                                        <h3 class="section-header d-flex justify-content-between align-items-center">
                                            About
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAboutModal">Edit</button>
                                        </h3>
                                        <div id="about-text" class="shadow-sm p-3 mb-5 bg-white rounded w-75">
                                            <?= !empty($about_text) ? nl2br(htmlspecialchars($about_text)) : 'Click edit to add your About section.' ?>
                                        </div>

                                    </div>
                                    <div class="modal fade" id="editAboutModal" tabindex="-1" aria-labelledby="editAboutModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form  class="modal-content" id="edit-about-form">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAboutModalLabel">Edit About</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea id="about-input" class="form-control w-75" rows="5"><?= !empty($about_text) ? nl2br(htmlspecialchars($about_text)) : 'Click edit to add your About section.' ?></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <script>
                                          document.getElementById('edit-about-form').addEventListener('submit', function (e) {
                                            e.preventDefault();
                                            const updatedText = document.getElementById('about-input').value;

                                            fetch('update_about.php', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                body: `about=${encodeURIComponent(updatedText)}`
                                            })
                                            .then(response => {
                                                if (!response.ok) throw new Error('Request failed');
                                                return response.text();
                                            })
                                            .then(data => {
                                                if (data === 'success' || data === '') {
                                                    document.getElementById('about-text').innerText = updatedText;

                                                    const modalEl = document.getElementById('editAboutModal');
                                                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                                                    modalInstance.hide();
                                                    document.body.classList.remove('modal-open');
                                                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                                                } else {
                                                    alert("Error: " + data);
                                                }
                                            })
                                            .catch(error => {
                                                console.error("Error updating About:", error);
                                                alert("Something went wrong while updating.");
                                            });
                                        });
                                    </script>

                                </div>



                                    <div class="experience-section">
                                        <h3 class="section-header">Experience</h3>
                                        <ul class="list-group" id="work-experience-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#experienceModal">Add Work Experience</button>
                                    </div>
                                    <div class="education-section">
                                        <h3 class="section-header">Education</h3>
                                        <ul class="list-group" id="education-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#educationModal">Add Education</button>
                                    </div>
                                    <div class="skills-section">
                                        <h3 class="section-header">Skills</h3>
                                        <ul class="list-group" id="skills-list">
                                        </ul>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#skillsModal">Add Skill</button>
                                    </div>                                
                                </div>
                                    <div class="modal fade" id="experienceModal" tabindex="-1" aria-labelledby="experienceModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="experienceModalLabel">Add Work Experience</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="work-experience-form">
                                                        <div class="mb-3">
                                                            <label for="job-title" class="form-label">Job Title</label>
                                                            <input type="text" class="form-control" id="job-title" placeholder="e.g., Software Engineer">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="company-name" class="form-label">Company</label>
                                                            <input type="text" class="form-control" id="company-name" placeholder="e.g., Google">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="start-date" class="form-label">Start Date</label>
                                                            <input type="date" class="form-control" id="start-date">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="end-date" class="form-label">End Date</label>
                                                            <input type="date" class="form-control" id="end-date">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="job-description" class="form-label">Job Description</label>
                                                            <textarea class="form-control" id="job-description" rows="3" placeholder="Describe your job role"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Add Experience</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        // Fetch work experience data when the page loads
                                        document.addEventListener("DOMContentLoaded", function() {
                                            fetchWorkExperience();  // Fetch work experience data
                                        });

                                        // Function to fetch work experience data from the server
                                        function fetchWorkExperience() {
                                            fetch('fetch_experience.php')  // Send GET request to PHP script
                                                .then(response => response.json())  // Parse the response as JSON
                                                .then(data => {
                                                    if (data.length > 0) {
                                                        const workExperienceList = document.getElementById('work-experience-list');
                                                        workExperienceList.innerHTML = '';  // Clear previous items

                                                        // Loop through each experience and display it
                                                        data.forEach(experience => {
                                                            const startDate = new Date(experience.start_date);
                                                            const endDate = experience.end_date ? new Date(experience.end_date) : null;

                                                            //  Time-based formatting for End Date
                                                            const endDateHTML = endDate 
                                                                ? `<p><strong>End Date:</strong> ${endDate.toLocaleDateString()}</p>` 
                                                                : '';

                                                            //  Duration calculation (highlighted part)
                                                            const duration = calculateDuration(startDate, endDate);

                                                            // HTML for one work experience
                                                            const experienceHTML = `
                                                                <li class="list-group-item">
                                                                    <div>
                                                                        <h5 class="card-title">${experience.job_title} at ${experience.company_name}</h5>
                                                                        <p><strong>Start Date:</strong> ${startDate.toLocaleDateString()}</p>
                                                                        ${endDateHTML}
                                                                        
                                                                        <!-- Show duration like "1y 3m" -->
                                                                        <p><strong>Duration:</strong> ${duration}</p>
                                                                        
                                                                        <p><strong>Description:</strong> ${experience.job_description}</p>
                                                                        <div class="d-flex">
                                                                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                                                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            `;

                                                            // Add experience to list
                                                            workExperienceList.innerHTML += experienceHTML;
                                                        });

                                                        // Re-initialize drag-sort
                                                        initSortable();
                                                    } else {
                                                        console.log("No work experience found.");
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error fetching work experience:', error);
                                                });
                                        }

                                        //  Function to calculate the duration between start and end dates
                                        function calculateDuration(startDate, endDate) {
                                            const now = new Date();  // Today's date
                                            const end = endDate || now;  // Use endDate or current date if null

                                            let years = end.getFullYear() - startDate.getFullYear();
                                            let months = end.getMonth() - startDate.getMonth();

                                            // Adjust if months are negative
                                            if (months < 0) {
                                                years--;
                                                months += 12;
                                            }

                                            return `${years}y ${months}m`;
                                        }

                                        // SortableJS initialization
                                        function initSortable() {
                                            const workExperienceList = document.getElementById('work-experience-list');
                                            new Sortable(workExperienceList, {
                                                animation: 150,
                                                handle: '.card-title',  // Drag handle
                                            });
                                        }
                                    </script>



                                    <script type="text/javascript">
                                        document.getElementById('work-experience-form').addEventListener('submit', function(event) {
                                            event.preventDefault();

                                            const jobTitle = document.getElementById('job-title').value;
                                            const companyName = document.getElementById('company-name').value;
                                            const startDate = new Date(document.getElementById('start-date').value);
                                            const endDateInput = document.getElementById('end-date').value;
                                            const endDate = endDateInput ? new Date(endDateInput) : null;
                                            const jobDescription = document.getElementById('job-description').value;

                                            function calculateDuration(start, end) {
                                                const now = new Date();
                                                const finalEnd = end ? end : now;

                                                let years = finalEnd.getFullYear() - start.getFullYear();
                                                let months = finalEnd.getMonth() - start.getMonth();

                                                if (months < 0) {
                                                    years--;
                                                    months += 12;
                                                }

                                                return `${years}y ${months}m`;
                                            }

                                            const duration = calculateDuration(startDate, endDate);

                                            const formData = new FormData();
                                            formData.append('job-title', jobTitle);
                                            formData.append('company-name', companyName);
                                            formData.append('start-date', startDate.toISOString().split('T')[0]);
                                            formData.append('end-date', endDate ? endDate.toISOString().split('T')[0] : '');
                                            formData.append('job-description', jobDescription);
                                            formData.append('duration', duration);

                                            fetch('work_experience.php', {
                                                method: 'POST',
                                                body: formData,
                                            })
                                            .then(response => response.text())
                                            .then(data => {
                                                console.log(data);
                                                alert('Work experience added successfully!');

                                                const experienceHTML = `
                                                    <li class="list-group-item">
                                                        <div>
                                                            <h5 class="card-title">${jobTitle} at ${companyName}</h5>
                                                            <p><strong>Start Date:</strong> ${startDate.toLocaleDateString()}</p>
                                                            ${endDate ? `<p><strong>End Date:</strong> ${endDate.toLocaleDateString()}</p>` : ''}
                                                            <p><strong>Duration:</strong> ${duration}</p>
                                                            <p><strong>Description:</strong> ${jobDescription}</p>
                                                            <div class="d-flex">
                                                                <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                                                <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                `;

                                                document.getElementById('work-experience-list').innerHTML += experienceHTML;
                                                document.getElementById('work-experience-form').reset();
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                alert('Error adding work experience. Please try again.');
                                            });
                                        });
                                    </script>




                                        <div class="modal fade" id="educationModal" tabindex="-1" aria-labelledby="educationModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="educationModalLabel">Add Education</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="education-form">
                                                        <div class="mb-3">
                                                            <label for="degree" class="form-label">Degree</label>
                                                            <input type="text" class="form-control" id="degree" placeholder="e.g., BSc in Computer Science">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="institution" class="form-label">Institution</label>
                                                            <input type="text" class="form-control" id="institution" placeholder="e.g., University of Colombo">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="start-date" class="form-label">Start Date</label>
                                                            <input type="date" class="form-control" id="start-date2">
                                                            <span id="start-date-error" class="text-danger"></span> 
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="end-date" class="form-label">End Date</label>
                                                            <input type="date" class="form-control" id="end-date2">
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Add Education</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        document.getElementById('education-form').addEventListener('submit', function(event) {
                                        event.preventDefault();
                                        const degree = document.getElementById('degree').value;
                                        const institution = document.getElementById('institution').value;
                                        const startDateInput = new Date(document.getElementById('start-date2').value);  // Start date
                                        const endDateInput = document.getElementById('end-date2').value; //
                                        const errorMessage = document.getElementById('start-date-error');
                                        errorMessage.textContent = "";
                                        console.log("Start Date Input Value: ", startDateInput);
                                        if (!startDateInput) {
                                            errorMessage.textContent = "Please provide a valid start date.";
                                            errorMessage.classList.add('text-danger');
                                            return;
                                        }
                                        const startDate = new Date(startDateInput); // Convert to Date 
                                        console.log("Parsed Start Date: ", startDate);
                                        if (isNaN(startDate.getTime())) {
                                            errorMessage.textContent = "Please provide a valid start date.";
                                            errorMessage.classList.add('text-danger');
                                            return;
                                        }
                                        let endDate, duration, endDateHTML;
                                        if (endDateInput) {
                                            endDate = new Date(endDateInput);
                                            if (isNaN(endDate.getTime())) {
                                                errorMessage.textContent = "Please provide a valid end date.";
                                                errorMessage.classList.add('text-danger');
                                                return;
                                            }
                                            if (endDate < startDate) {
                                                errorMessage.textContent = "End date cannot be earlier than the start date.";
                                                errorMessage.classList.add('text-danger');
                                                return;
                                            }
                                            endDateHTML = `<p><strong>End Date:</strong> ${endDate.toLocaleDateString()}</p>`;
                                            duration = calculateDuration(startDate, endDate); // Calculate 
                                        } else {
                                            endDate = new Date();
                                            endDateHTML = ""; // Don't display End Date if it's ongoing
                                            duration = calculateDuration(startDate, endDate);
                                        }
                                        const educationHTML = `
                                            <li class="list-group-item">
                                                <div class="">
                                                    <h5 class="card-title">${degree} at ${institution}</h5>
                                                    <div class="card-body">
                                                        <p><strong>Start Date:</strong> ${startDate.toLocaleDateString()}</p>
                                                        ${endDateHTML} <!-- Conditionally show the End Date -->
                                                        <p><strong>Duration:</strong> ${duration}</p>
                                                        <div class='d-flex'>
                                                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        `;
                                        document.getElementById('education-list').innerHTML += educationHTML;
                                        document.getElementById('education-form').reset();

                                        const modal = bootstrap.Modal.getInstance(document.getElementById('educationModal'));
                                        modal.hide();
                                    });
                                    function calculateDuration(startDate, endDate) {
                                        const years = endDate.getFullYear() - startDate.getFullYear();
                                        const months = endDate.getMonth() - startDate.getMonth();
                                        let totalYears = years;
                                        let totalMonths = months;
                                        if (months < 0) {
                                            totalYears--;
                                            totalMonths += 12;
                                        }
                                        return `${totalYears}y ${totalMonths} months`;
                                    }
                                    </script>
                                        <div class="modal fade" id="skillsModal" tabindex="-1" aria-labelledby="skillsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="skillsModalLabel">Add Skill</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="skills-form">
                                                            <div class="mb-3">
                                                                <label for="skill" class="form-label">Skill</label>
                                                                <input type="text" class="form-control" id="skill" placeholder="e.g., JavaScript">
                                                            </div>
                                                             <div class="mb-3">
                                                                 <div class="mb-3">
                                                                    <label for="institution" class="form-label">Institution</label>
                                                                    <input type="text" class="form-control" id="institution2" placeholder="e.g., University of Colombo">
                                                            </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Add Skill</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                           document.getElementById('skills-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    const skill = document.getElementById('skill').value;
    const institution = document.getElementById('institution2').value;

    if (!skill || !institution) {
        alert("Please fill in both Skill and Institution fields.");
        return;
    }

    const formData = new FormData();
    formData.append('skill', skill);
    formData.append('institution', institution);

    // Send the data to the backend PHP file
    fetch('add_skill.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse the JSON response from PHP
    .then(data => {
        if (data.success) {
            alert(data.success); // Show success message
            const skillHTML = `
                <li class="list-group-item">
                    <div class="skillsbox">
                        <h4>${skill}</h4>
                        <p>${institution}</p>
                        <div class="d-flex">
                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                        </div>
                    </div>
                </li>
            `;
            document.getElementById('skills-list').innerHTML += skillHTML; // Add new skill to the list
            document.getElementById('skills-form').reset(); // Reset form
            initSortable(); // Reinitialize sortable functionality
            addSkillEventListeners(); // Reinitialize event listeners
        } else {
            alert(data.error); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error); // Log errors in the console
        alert('Error adding skill. Please try again.');
    });
});

                                        </script>

                                        <script type="text/javascript">
                                            function initSortable() {
    const skillList = document.getElementById('skills-list');
    new Sortable(skillList, {
        animation: 150,
        handle: '.skillsbox', // Drag handle
    });
}

function addSkillEventListeners() {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const listItem = button.closest('li');
            const skillName = listItem.querySelector('h4').textContent;
            const institutionName = listItem.querySelector('p').textContent;

            const newSkill = prompt('Edit Skill', skillName);
            const newInstitution = prompt('Edit Institution', institutionName);

            if (newSkill && newInstitution) {
                listItem.querySelector('h4').textContent = newSkill;
                listItem.querySelector('p').textContent = newInstitution;
            }
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const listItem = button.closest('li');
            listItem.remove();
        });
    });
}

                                        </script>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                fetchSkills();
                                            });

                                            function fetchSkills() {
                                                fetch('fetch_skills.php')
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        const skillsList = document.getElementById('skills-list');
                                                        skillsList.innerHTML = ''; // Clear current list

                                                        data.forEach(skill => {
                                                            const skillHTML = `
                                                                <li class="list-group-item">
                                                                    <div class="skillsbox">
                                                                        <h4>${skill.skill_name}</h4>
                                                                        <p>${skill.institution}</p>
                                                                        <div class="d-flex">
                                                                            <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                                                            <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            `;
                                                            skillsList.innerHTML += skillHTML;
                                                        });

                                                        initSortable();
                                                        addSkillEventListeners();
                                                    })
                                                    .catch(error => {
                                                        console.error('Error fetching skills:', error);
                                                    });
                                            }
                                            </script>

                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once("../includes/footer.php") ?>
    <?php include_once ("../includes/js-links-inc.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>
<?php
$conn->close();
?>
