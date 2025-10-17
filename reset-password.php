<?php
session_start();
require_once 'includes/db-conn.php';

if (!isset($_GET['token'])) {
    die("Invalid token.");
}

$token = $_GET['token'];

// ✅ Validate token
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("This reset link is invalid or expired.");
}

$reset = $result->fetch_assoc();
$email = $reset['email'];
$stmt->close();

if (isset($_POST['reset'])) {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Update password in correct table (if user exists)
    $tables = ['admins', 'lectures', 'former_students', 'students', 'companies'];
    $updated = false;

    foreach ($tables as $table) {
        $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $new_password, $email);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $updated = true;
            }
            $stmt->close();
        }
    }

    if ($updated) {
        // ✅ Delete token safely
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['success_message'] = "Password reset successful! Please login.";
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('No matching user found for this email.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Eduwide</title>
  <link rel="icon" href="assets/images/logos/favicon.png">
  <?php include_once("includes/css-links-inc.php"); ?>
</head>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="col-lg-4 col-md-6 card p-4">
          <h5 class="text-center mb-3">Reset Password</h5>

          <form method="POST">
            <div class="mb-3">
              <label>New Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="reset" class="btn btn-success w-100">Reset Password</button>
          </form>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
