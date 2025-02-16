<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Login - MediQ</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <?php include_once ("includes/css-links-inc.php"); ?>

</head>

<body>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="index.html" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/images/logos/mediq-logo.png" alt="" style="max-height:130px;">
                                    <!-- <span class="d-none d-lg-block">MediQ</span> -->
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                        <!-- <p class="text-center small">Enter your username & password to login</p> -->
                                    </div>

                                    <form action="" method="POST" class="row g-3 needs-validation" novalidate>
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Username</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="text" name="username" class="form-control" id="yourUsername" required>
                                                <div class="invalid-feedback">Please enter your username.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                          <label for="yourPassword" class="form-label">Password</label>
                                          <div class="input-group">
                                                <input type="password" class="form-control" id="myPassword" name="password" required>
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="password-toggle-icon1 bx bxs-show" onclick="togglePasswordVisibility('myPassword', 'password-toggle-icon1')"></i>
                                                </span>
                                              <div class="invalid-feedback">Please enter your password!</div>
                                          </div>
                                        </div>

                                        <div class="col-12">
                                          <p class="small mb-0" style="font-size:14px;"><a href="forgotten-password.php">Forgotten password</a>
                                        </div>

                                        <div class="col-12">
                                            <input type="submit" class="btn btn-primary w-100" id="submit" name="submit" value="Login">
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <?php include_once ("includes/footer2.php") ?>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once ("includes/js-links-inc.php") ?>

</body>

</html>