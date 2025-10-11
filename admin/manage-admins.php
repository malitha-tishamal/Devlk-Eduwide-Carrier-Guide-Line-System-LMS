<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch logged-in admin
$user_id = $_SESSION['admin_id'];
$sql = "SELECT username, email, nic, mobile, profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch all admins
$sql = "SELECT * FROM admins";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Manage Admins - EduWide</title>
  <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>
  <?php include_once("../includes/header.php"); ?>
  <?php include_once("../includes/sadmin-sidebar.php"); ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Manage Admins</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Pages</li>
          <li class="breadcrumb-item active">Manage Admins</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Admins Management</h5>
              <p>Manage Admins here.</p>

              <table class="table datatable align-middle text-center">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>NIC</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Approve</th>
                    <th>Disable</th>
                    <th>Delete</th>
                    <th>Edit</th>
                    <th>Reset</th>
                    <th>Profile</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $is_primary = ($row['id'] == 1);
                          $status = strtolower($row['status']);

                          // Disable/opacity rules
                          $approve_disabled = ($status === 'active' || $status === 'approved') ? 'disabled' : '';
                          $approve_style = ($approve_disabled) ? 'style="opacity:0.6;pointer-events:none;"' : '';

                          $disable_disabled = ($status === 'disabled') ? 'disabled' : '';
                          $disable_style = ($disable_disabled) ? 'style="opacity:0.6;pointer-events:none;"' : '';

                          echo "<tr>";
                          echo "<td>{$row['id']}</td>";
                          echo "<td><img src='" . htmlspecialchars($row["profile_picture"]) . "' alt='Profile' width='100' height='100' style='object-fit:cover;border-radius:10px;'></td>";
                          echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['nic']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";

                          // Status column
                          echo "<td>";
                          if ($status === 'active' || $status === 'approved') {
                              echo "<span class='btn btn-success btn-sm w-100'>Approved</span>";
                          } elseif ($status === 'disabled') {
                              echo "<span class='btn btn-danger btn-sm w-100'>Disabled</span>";
                          } elseif ($status === 'pending') {
                              echo "<span class='btn btn-warning btn-sm w-100'>Pending</span>";
                          } else {
                              echo "<span class='btn btn-secondary btn-sm w-100'>" . ucfirst($row['status']) . "</span>";
                          }
                          echo "</td>";

                          if ($is_primary) {
                              // Primary admin (ID 1) — disable all
                              echo "<td><button class='btn btn-success btn-sm w-100' disabled style='opacity:0.5;'>Approve</button></td>";
                              echo "<td><button class='btn btn-warning btn-sm w-100' disabled style='opacity:0.5;'>Disable</button></td>";
                              echo "<td><button class='btn btn-danger btn-sm w-100' disabled style='opacity:0.5;'>Delete</button></td>";
                              echo "<td><button class='btn btn-primary btn-sm w-100' disabled style='opacity:0.5;'>Edit</button></td>";
                              echo "<td><button class='btn btn-secondary btn-sm w-100' disabled style='opacity:0.5;'>Reset</button></td>";
                              echo "<td><a href='admin-profile.php?admin_id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary btn-sm w-100'>Profile</a></td>";
                          } else {
                              echo "<td><button class='btn btn-success btn-sm w-100 approve-btn' data-id='{$row['id']}' $approve_disabled $approve_style>Approve</button></td>";
                              echo "<td><button class='btn btn-warning btn-sm w-100 disable-btn' data-id='{$row['id']}' $disable_disabled $disable_style>Disable</button></td>";
                              echo "<td><button class='btn btn-danger btn-sm w-100 delete-btn' data-id='{$row['id']}'>Delete</button></td>";
                              echo "<td><a href='edit-admin.php?id={$row['id']}' class='btn btn-primary btn-sm w-100'>Edit</a></td>";
                              echo "<td><button class='btn btn-outline-secondary btn-sm w-100 reset-btn' data-id='{$row['id']}'>Reset</button></td>";
                              echo "<td><a href='admin-profile.php?admin_id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary btn-sm w-100'>Profile</a></td>";
                          }

                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='12' class='text-center'>No admins found.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once("../includes/footer.php"); ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <?php include_once("../includes/js-links-inc.php"); ?>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const confirmAction = (msg, url) => {
      if (confirm(msg)) window.location.href = url;
    };

    document.querySelectorAll('.approve-btn').forEach(btn =>
      btn.addEventListener('click', () => {
        confirmAction("Approve this admin?", `process-admins.php?approve_id=${btn.dataset.id}`);
      })
    );

    document.querySelectorAll('.disable-btn').forEach(btn =>
      btn.addEventListener('click', () => {
        confirmAction("Disable this admin?", `process-admins.php?disable_id=${btn.dataset.id}`);
      })
    );

    document.querySelectorAll('.delete-btn').forEach(btn =>
      btn.addEventListener('click', () => {
        confirmAction("Delete this admin permanently?", `process-admins.php?delete_id=${btn.dataset.id}`);
      })
    );

    document.querySelectorAll('.reset-btn').forEach(btn =>
      btn.addEventListener('click', () => {
        confirmAction("Reset this admin’s password to default (00000000)?", `process-admins.php?reset_id=${btn.dataset.id}`);
      })
    );
  });
  </script>
</body>
</html>

<?php $conn->close(); ?>
