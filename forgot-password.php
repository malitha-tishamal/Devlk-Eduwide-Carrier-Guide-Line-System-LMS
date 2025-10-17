<?php
session_start();
require_once 'includes/db-conn.php';

// Handle form submission
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    // Check if email exists in any table
    $tables = ['admins', 'lectures', 'former_students', 'students', 'companies'];
    $found = false;
    $user_id = null;
    $user_table = '';

    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $user_table = $table;
            $found = true;
            break;
        }
    }

    if ($found) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Store token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires_at);
        $stmt->execute();

        // Create reset link
        $reset_link = "http://yourdomain.com/reset-password.php?token=$token";

        // (Optional) send email — for now, just show it on screen
        $_SESSION['success_message'] = "Password reset link: <a href='$reset_link' target='_blank'>$reset_link</a><br>Valid for 30 minutes.";
    } else {
        $_SESSION['error_message'] = "No account found with that email.";
    }

    header("Location: forgot-password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Eduwide</title>
  <link rel="icon" href="assets/images/logos/favicon.png">
  <?php include_once("includes/css-links-inc.php"); ?>
  <style>
      .alert-success {background: green; color: white; padding: 5px; border-radius: 5px; text-align: center;}
      .alert-danger {background: red; color: white; padding: 5px; border-radius: 5px; text-align: center;}
  </style>
</head>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="col-lg-4 col-md-6 card p-4">
          <h5 class="text-center mb-3">Forgot Password</h5>

          <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
          <?php elseif (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Send Reset Link</button>
            <p class="small mt-3 text-center"><a href="index.php">Back to Login</a></p>
          </form>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
