<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Users Profile - MediQ</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <?php include_once ("includes/css-links-inc.php"); ?>

</head>

<body>

    <?php include_once ("includes/header.php") ?>

    <?php include_once ("includes/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Profile</h1>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Home</a></li>
                <!-- <li class="breadcrumb-item">Users</li> -->
                <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
                <div class="">
                    <div class="card">
                        <div class="card-body pt-3">
                            <ul class="nav nav-tabs nav-tabs-bordered">
                                <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                                </li>

                                <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active profile-overview pt-3" id="profile-overview">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                        <div class="col-lg-9 col-md-8">Kevin Anderson</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8">k.anderson@example.com</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">ID</div>
                                        <div class="col-lg-9 col-md-8">Data</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Mobile Number</div>
                                        <div class="col-lg-9 col-md-8">07xxxxxxxx</div>
                                    </div>
                                </div>

                                <div class="tab-pane fade pt-2" id="profile-change-password" >
                                
                                    <form action="" method="POST" class="needs-validation" novalidate>
                                        <div class="row mb-3">
                                            <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="myPassword" name="current_password" required>
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="password-toggle-icon1 bx bxs-show" onclick="togglePasswordVisibility('myPassword', 'password-toggle-icon1')"></i>
                                                    </span>
                                                    <div class="invalid-feedback" style="font-size:14px" id="">
                                                        Enter your current password
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="password-toggle-icon2 bx bxs-show" onclick="togglePasswordVisibility('newPassword', 'password-toggle-icon2')"></i>
                                                    </span>
                                                    <div class="invalid-feedback" style="font-size:14px">
                                                        Enter your new password
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                                            <div class="col-md-8 col-lg-9">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="confirmPassword" name="renew_password" required>
                                                    <span class="input-group-text" id="inputGroupPrepend">
                                                        <i class="password-toggle-icon3 bx bxs-show" onclick="togglePasswordVisibility('confirmPassword', 'password-toggle-icon3')"></i>
                                                    </span>
                                                    <div class="invalid-feedback" style="font-size:14px" id="">
                                                        Re - enter your new password
                                                    </div>
                                                </div>
                                                <div style="color:red; font-size:14px;" id="confirmNewPasswordErrorMessage"></div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <input type="submit" class="btn btn-primary" name="submit" value="Change Password">
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <?php include_once ("includes/footer.php") ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include_once ("includes/js-links-inc.php") ?>

</body>

</html>