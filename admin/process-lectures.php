<?php
require_once "../includes/db-conn.php";

// Approve user
if (isset($_GET['approve_id'])) {
    $user_id = intval($_GET['approve_id']);
    $sql = "UPDATE lectures SET status = 'approved' WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            header("Location: manage-lectures.php?message=User approved successfully!&msg_type=success");
        } else {
            header("Location: manage-lectures.php?message=Error approving user.&msg_type=danger");
        }
        $stmt->close();
    }
    exit();
}

// Disable user
if (isset($_GET['disable_id'])) {
    $user_id = intval($_GET['disable_id']);
    $sql = "UPDATE lectures SET status = 'disabled' WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            header("Location: manage-lectures.php?message=User disabled successfully!&msg_type=success");
        } else {
            header("Location: manage-lectures.php?message=Error disabling user.&msg_type=danger");
        }
        $stmt->close();
    }
    exit();
}

// Delete user
if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM lectures WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            header("Location: manage-lectures.php?message=User deleted successfully!&msg_type=success");
        } else {
            header("Location: manage-lectures.php?message=Error deleting user.&msg_type=danger");
        }
        $stmt->close();
    }
    exit();
}

// Reset user password
if (isset($_GET['reset_id'])) {
    $user_id = intval($_GET['reset_id']);
    $newPassword = password_hash('00000000', PASSWORD_DEFAULT);
    $sql = "UPDATE lectures SET password = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $newPassword, $user_id);
        if ($stmt->execute()) {
            header("Location: manage-lectures.php?message=Password reset to 00000000 successfully!&msg_type=info");
        } else {
            header("Location: manage-lectures.php?message=Error resetting password.&msg_type=danger");
        }
        $stmt->close();
    }
    exit();
}

$conn->close();
?>
