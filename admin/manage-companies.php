<?php
session_start();
require_once '../includes/db-conn.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch admin details
$user_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch companies
$sql = "SELECT * FROM companies";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Companies Manage - EduWide</title>
    <?php include_once("../includes/css-links-inc.php"); ?>
</head>

<body>

    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Manage Companies</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Manage Companies</li>
                </ol>
            </nav>
        </div>

        <!-- Flash message -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'success' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Companies Management</h5>
                            <p>Manage Companies here.</p>

                            <table class="table datatable table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile Picture</th>
                                        <th>Company Name</th>
                                        <th>Address</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Approve</th>
                                        <th>Disable</th>
                                        <th>Delete</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()):
                                            $status = strtolower($row['status']);
                                            $isApproved = ($status === 'active' || $status === 'approved');
                                            $isDisabled = ($status === 'disabled');
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td>
                                                <img src="../companies/<?= htmlspecialchars($row['profile_picture'] ?: 'default.png') ?>" 
                                                     width="120" height="120" onerror="this.src='../companies/default.png'">
                                            </td>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['address']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['mobile']) ?></td>
                                            <td><?= htmlspecialchars($row['category']) ?></td>
                                            <td>
                                                <?php
                                                    if ($isApproved) echo "<span class='btn btn-success btn-sm w-100'>Approved</span>";
                                                    elseif ($isDisabled) echo "<span class='btn btn-danger btn-sm w-100'>Disabled</span>";
                                                    elseif ($status === 'pending') echo "<span class='btn btn-warning btn-sm w-100'>Pending</span>";
                                                    else echo "<span class='btn btn-secondary btn-sm w-100'>" . ucfirst(htmlspecialchars($row['status'])) . "</span>";
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm w-100 approve-btn" 
                                                        data-id="<?= htmlspecialchars($row['id']) ?>" 
                                                        <?= $isApproved ? 'disabled style="opacity:0.5;"' : '' ?>>
                                                    Approve
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm w-100 disable-btn" 
                                                        data-id="<?= htmlspecialchars($row['id']) ?>" 
                                                        <?= $isDisabled ? 'disabled style="opacity:0.5;"' : '' ?>>
                                                    Disable
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-sm w-100 delete-btn" 
                                                        data-id="<?= htmlspecialchars($row['id']) ?>">
                                                    Delete
                                                </button>
                                            </td>
                                            <td>
                                                <a href="edit-company.php?id=<?= htmlspecialchars($row['id']) ?>" 
                                                   class="btn btn-primary btn-sm w-100">Edit</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center">No companies found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <?php include_once("../includes/footer.php") ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <?php include_once("../includes/js-links-inc.php") ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const approveButtons = document.querySelectorAll('.approve-btn');
            const disableButtons = document.querySelectorAll('.disable-btn');
            const deleteButtons = document.querySelectorAll('.delete-btn');

            approveButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    window.location.href = `process-companies.php?approve_id=${id}`;
                });
            });

            disableButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    window.location.href = `process-companies.php?disable_id=${id}`;
                });
            });

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    if (confirm('Are you sure you want to delete this company?')) {
                        window.location.href = `process-companies.php?delete_id=${id}`;
                    }
                });
            });
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
