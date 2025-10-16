<?php
// Start session to access session variables
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Create Company Account - EduWide</title>
    <link rel="icon" href="../assets/images/logos/favicon.png">

    <?php include_once ("../includes/css-links-inc.php"); ?>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
            display: none;
            z-index: 9999;
        }

        .error-popup {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <!-- Displaying the message from the session -->
    <?php if (isset($_SESSION['status'])): ?>
        <div class="popup-message <?php echo ($_SESSION['status'] == 'success') ? '' : 'error-popup'; ?>" id="popup-alert">
            <?php echo $_SESSION['message']; ?>
        </div>

        <script>
            document.getElementById('popup-alert').style.display = 'block';
            setTimeout(function() {
                const popupAlert = document.getElementById('popup-alert');
                if (popupAlert) popupAlert.style.display = 'none';
            }, 1000);
        </script>

        <?php
        unset($_SESSION['status']);
        unset($_SESSION['message']);
        ?>
    <?php endif; ?>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <div class="d-flex justify-content-center py-4">
                            <a href="" class="logo d-flex align-items-center w-auto">
                                <img src="../assets/images/logos/eduwide-logo.png" alt="" style="max-height:130px;">
                            </a>
                        </div>

                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Create Company Account</h5>
                                </div>

                                <form id="signup-form" action="company-register.php" method="POST" class="row g-3 needs-validation" novalidate>

                                    <div class="col-12">
                                        <label for="name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control" id="name" name="username" required>
                                        <div class="invalid-feedback" style="font-size:14px">
                                            Please enter the name
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label">Company Address</label>
                                        <input type="text" class="form-control" id="address" name="adress" required>
                                        <div class="invalid-feedback" style="font-size:14px">
                                            Please enter the address
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">Company Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback" style="font-size:14px">
                                            Please enter the email address
                                        </div>
                                    </div>

                                    <!-- Company Type / Category -->
                                   <div class="col-12">
    <label for="category" class="form-label">Company Type</label>
    <select id="category" name="category" class="form-select w-75" required>
        <option value="" disabled selected>Select or type a category</option>
        <?php
        require_once '../includes/db-conn.php';
        // Select DISTINCT category_name to avoid duplicates
        $query = $conn->query("SELECT DISTINCT category_name FROM hnd_course_categories ORDER BY category_name ASC");
        while ($row = $query->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['category_name']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
        }
        ?>
    </select>
</div>


                                    <div class="col-12">
                                        <label for="mobileNumber" class="form-label">Company Mobile Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+94</span>
                                            <input type="tel" class="form-control" id="mobileNumber" name="mobile" placeholder="712345678" oninput="validateMobile(this)" required>
                                            <div class="invalid-feedback" style="font-size:14px;" id="numberErrorMessage">
                                                Please enter the mobile number
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <span class="input-group-text" id="inputGroupPrepend">
                                                <i class="password-toggle-icon1 bx bxs-show" onclick="togglePasswordVisibility('password', 'password-toggle-icon1')"></i>
                                            </span>
                                            <div class="invalid-feedback" style="font-size:14px;">
                                                Please enter password
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Create Account</button>
                                    </div>

                                    <p class="small mb-0" style="font-size:14px;">Already have Account <a href="../index.php">Login</a></p>

                                </form>
                            </div>
                        </div>

                        <?php include_once ("../includes/footer3.php") ?>

                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Back to top button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Initialize Select2 -->
    <script>
    $(document).ready(function() {
        $('#category').select2({
            placeholder: "Select or type a category",
            tags: true,
            tokenSeparators: [','],
            width: 'resolve'
        });

        // Form submission with AJAX
        $("#signup-form").submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: "register.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    let popupAlert = $("#popup-alert");

                    if (response.status === "success") {
                        popupAlert.removeClass("error-popup").addClass("popup-message").html(response.message);
                    } else {
                        popupAlert.removeClass("popup-message").addClass("error-popup").html(response.message);
                    }

                    popupAlert.show();
                    setTimeout(function() {
                        popupAlert.fadeOut();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    alert("AJAX Error: " + xhr.responseText);
                }
            });
        });
    });
    </script>

    <?php include_once ("../includes/js-links-inc.php") ?>
</body>

</html>
