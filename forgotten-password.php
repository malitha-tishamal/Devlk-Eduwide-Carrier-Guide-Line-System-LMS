<?php
require_once 'includes/db-conn.php';
session_start();

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    // ✅ Check if email exists in any table
    $tables = ['admins', 'lectures', 'former_students', 'students', 'companies'];
    $emailExists = false;

    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT email FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $emailExists = true;
            $stmt->close();
            break;
        }
        $stmt->close();
    }

    if ($emailExists) {
        // ✅ Create token and expiration time
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // ✅ Store in password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires_at);
        $stmt->execute();
        $stmt->close();

        // ✅ Create reset link
        $reset_link = "https://eduwide.42web.io/reset-password.php?token=" . $token;

        // ✅ Send email (works only if mail() is supported)
        $subject = "Eduwide Password Reset Link";
        $message = "Hello,\n\nClick the link below to reset your password:\n$reset_link\n\nThis link expires in 1 hour.\n\n- Eduwide Team";
        $headers = "From: no-reply@eduwide.42web.io";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['success_message'] = "Password reset link sent to your email!";
        } else {
            $_SESSION['error_message'] = "Failed to send email. (Free hosting may block mail())";
        }
    } else {
        $_SESSION['error_message'] = "Email not found in the system.";
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
</head>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="col-lg-4 col-md-6 card p-4">
          <h5 class="text-center mb-3">Forgot Password</h5>

          <?php if (isset($_SESSION['success_message'])): ?>
              <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
          <?php elseif (isset($_SESSION['error_message'])): ?>
              <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label>Enter Your Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Send Reset Link</button>
          </form>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
