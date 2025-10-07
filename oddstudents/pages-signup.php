<?php
session_start();
include_once("../includes/db-conn.php"); // DB connection
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Former Students Account - EduWide</title>
    <link rel="icon" href="../assets/images/logos/favicon.png">
    <?php include_once("../includes/css-links-inc.php"); ?>

    <style>
        .popup-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 25px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            display: none;
            z-index: 9999;
        }

        .success-popup {
            background-color: #28a745;
        }

        .error-popup {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <!-- ✅ Popup for PHP session messages -->
    <?php if (isset($_SESSION['status'])): ?>
        <div class="popup-message <?php echo ($_SESSION['status'] == 'success') ? 'success-popup' : 'error-popup'; ?>" id="popup-alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <script>
            const popup = document.getElementById('popup-alert');
            if (popup) {
                popup.style.display = 'block';
                setTimeout(() => popup.style.display = 'none', 3000);
            }
        </script>
        <?php
        unset($_SESSION['status']);
        unset($_SESSION['message']);
        ?>
    <?php endif; ?>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-7 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="../assets/images/logos/eduwide-logo.png" alt="EduWide" style="max-height:130px;">
                                </a>
                            </div>

                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="pt-4 pb-2 text-center">
                                        <h5 class="card-title fs-4 mb-0">Create Former Student Account</h5>
                                    </div>

                                    <!-- ✅ Form: normal POST -->
                                    <form id="signup-form" action="register.php" method="POST" class="row g-3 needs-validation" novalidate>

                                        <!-- Full Name -->
                                        <div class="col-12">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="username" required>
                                            <div class="invalid-feedback">Please enter your name.</div>
                                        </div>

                                        <!-- Registration ID -->
                                        <div class="col-12">
                                            <label for="reg_id" class="form-label">Registration ID</label>
                                            <input type="text" class="form-control" id="reg_id" name="reg_id" placeholder="e.g., GAL/IT/20xx/xxxx" required>
                                            <div class="invalid-feedback">Please enter your registration ID.</div>
                                        </div>

                                        <!-- NIC Number -->
                                        <div class="col-12">
                                            <label for="nicNumber" class="form-label">NIC Number</label>
                                            <input type="text" class="form-control" id="nicNumber" name="nic" oninput="this.value = this.value.toUpperCase();" required>
                                            <div class="invalid-feedback">Please enter your NIC number.</div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>

                                        <!-- Mobile -->
                                        <div class="col-12">
                                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+94</span>
                                                <input type="tel" class="form-control" id="mobileNumber" name="mobile" placeholder="712345678" required>
                                            </div>
                                            <div class="invalid-feedback">Please enter your mobile number.</div>
                                        </div>

                                        <!-- Course -->
                                        <div class="col-12">
                                            <label for="course" class="form-label">Select Your Course</label>
                                            <select name="course" id="course" class="form-select" required>
                                                <option value="">-- Select Course --</option>
                                                <?php
                                                $query = "SELECT id, name FROM hnd_courses ORDER BY name ASC";
                                                $result = mysqli_query($conn, $query);
                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="">No courses found</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="invalid-feedback">Please select your course.</div>
                                        </div>

                                        <!-- Batch Year -->
                                        <div class="col-12">
                                            <label for="year" class="form-label">Select Batch Year</label>
                                            <select class="form-select" id="year" name="study_year" required>
                                                <option value="" disabled selected>-- Select Year --</option>
                                            </select>
                                            <div class="invalid-feedback">Please select your batch year.</div>
                                        </div>

                                        <script>
                                            const currentYear = new Date().getFullYear();
                                            const yearSelect = document.getElementById("year");
                                            for (let year = 2000; year <= currentYear + 2; year++) {
                                                const option = document.createElement("option");
                                                option.value = year;
                                                option.textContent = year;
                                                yearSelect.appendChild(option);
                                            }
                                        </script>

                                        <!-- Current Status -->
                                        <div class="col-12">
                                            <label class="form-label">Current Status:</label><br>
                                            <label><input type="radio" name="nowstatus" value="study" required> Still Studying</label>&nbsp;&nbsp;
                                            <label><input type="radio" name="nowstatus" value="work"> Working</label>&nbsp;&nbsp;
                                            <label><input type="radio" name="nowstatus" value="intern"> Intern</label>&nbsp;&nbsp;
                                            <label><input type="radio" name="nowstatus" value="free"> Free</label>
                                            <div class="invalid-feedback d-block">Please select your current status.</div>
                                        </div>

                                        <!-- Password -->
                                        <div class="col-12">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <span class="input-group-text">
                                                    <i class="bx bxs-show" id="togglePassword" style="cursor:pointer;"></i>
                                                </span>
                                            </div>
                                            <div class="invalid-feedback">Please enter your password.</div>
                                        </div>

                                        <script>
                                            document.getElementById('togglePassword').addEventListener('click', function() {
                                                const passwordInput = document.getElementById('password');
                                                passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                                                this.classList.toggle('bxs-show');
                                                this.classList.toggle('bxs-hide');
                                            });
                                        </script>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Create Account</button>
                                        </div>

                                        <div class="col-12 text-center">
                                            <p class="small mb-0">Already have an account? <a href="../index.php">Log in</a></p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <?php include_once("../includes/footer3.php"); ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <?php include_once("../includes/js-links-inc.php"); ?>
</body>
</html>
