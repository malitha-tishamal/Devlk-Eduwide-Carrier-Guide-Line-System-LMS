<?php
// Start session to access session variables
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Create Teacher Account - Eduwide</title>
    <link rel="icon" href="../assets/images/logos/favicon.png">
    <?php include_once("../includes/css-links-inc.php"); ?>

    <style>
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

    <?php if (isset($_SESSION['status'])): ?>
        <div class="popup-message <?php echo ($_SESSION['status'] == 'success') ? '' : 'error-popup'; ?>" id="popup-alert">
            <?php echo $_SESSION['message']; ?>
        </div>

        <script>
            document.getElementById('popup-alert').style.display = 'block';

            setTimeout(function() {
                document.getElementById('popup-alert').style.display = 'none';
            }, 10000);

            <?php if ($_SESSION['status'] == 'success'): ?>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 10000);
            <?php endif; ?>
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
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="../assets/images/logos/eduwide-logo.png" alt="" style="max-height:130px;">
                                </a>
                            </div>

                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Create Teacher Account</h5>
                                    </div>

                                    <form action="register.php" method="POST" class="row g-3 needs-validation" novalidate>

                                        <div class="col-12">
                                            <label for="nicNumber" class="form-label">NIC Number</label>
                                            <input type="text" class="form-control" id="nicNumber" name="nic" placeholder=""
                                                oninput="this.value = this.value.toUpperCase(); generateIdFromNIC(this);" required>
                                            <div class="invalid-feedback" style="font-size:14px;">
                                                Please enter the NIC number
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="username" required>
                                            <div class="invalid-feedback" style="font-size:14px;">
                                                Please enter the name
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="subject" class="form-label">Subject</label>
                                            <select class="form-control text-center" name="course">
                                                <option selected disabled>--Technology Subject--</option>
                                                <option value="">Engineering Technology</option>
                                                <option value="">Science Technology</option>
                                                <option value="">Information Technology</option>
                                                <option selected disabled>--Maths Subject--</option>
                                                <option value="">Maths</option>
                                                <option value="">Physics</option>
                                                <option value="">Chemistry</option>
                                            </select>
                                            <div class="invalid-feedback" style="font-size:14px;">
                                                Please Select Subject
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback" style="font-size:14px;">
                                                Please enter the email address
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+94</span>
                                                <input type="tel" class="form-control" id="mobileNumber" name="mobile" placeholder="712345678" required>
                                                <div class="invalid-feedback" style="font-size:14px;">
                                                    Please enter the mobile number
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="id" class="form-label">Your ID</label>
                                            <input type="text" class="form-control" id="id" name="id" readonly placeholder="Autogenerate Code is Your ID">
                                        </div>

                                        <div class="col-12">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <div class="invalid-feedback" style="font-size:14px;">
                                                    Please enter password
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Create Account</button>
                                        </div>

                                        <div class="col-12">
                                            <p class="small mb-0">Create User account? <a href="../pages-signup.php">Click</a></p>
                                            <p class="small mb-0">Create Admin account? <a href="../admin/pages-signup.php">Click</a></p>
                                            <p class="small mb-0">Already have an account? <a href="../index.php">Log in</a></p>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <?php include_once("../includes/footer3.php") ?>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        function generateIdFromNIC(nicInput) {
            let nic = nicInput.value.trim();
            let idField = document.getElementById("id");

            if (nic.length >= 3) {
                let lastThreeDigits = nic.slice(-3);
                idField.value = "EDU" + lastThreeDigits;
            } else {
                idField.value = "";
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            // On form submit
            $("#signup-form").submit(function(event) {
                event.preventDefault(); // Prevent form submission

                $.ajax({
                    url: "register.php", // Send form data to register.php
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
                        }, 10000);

                        // If success, redirect after message disappears
                        if (response.status === "success") {
                            setTimeout(function() {
                                window.location.href = "index.php"; // Change this to your target redirect URL
                            }, 10000); // Same 10 seconds delay before redirect
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("AJAX Error: " + xhr.responseText); // Handle AJAX error
                    }
                });
            });
        });
    </script>

    <?php include_once("../includes/js-links-inc.php") ?>

</body>
</html>
