<?php
session_start();
require_once '../includes/db-conn.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$PRIMARY_ADMIN_ID = 1; // Main Admin Protected

function redirect($msg = '') {
    header("Location: manage-admins.php" . ($msg ? "?msg=" . urlencode($msg) : ""));
    exit();
}

// Approve Admin
if (isset($_GET['approve_id'])) {
    $id = (int)$_GET['approve_id'];
    if ($id === $PRIMARY_ADMIN_ID) redirect("Cannot modify primary admin.");
    $stmt = $conn->prepare("UPDATE admins SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    redirect("Admin approved successfully.");
}

// Disable Admin
if (isset($_GET['disable_id'])) {
    $id = (int)$_GET['disable_id'];
    if ($id === $PRIMARY_ADMIN_ID) redirect("Cannot disable primary admin.");
    $stmt = $conn->prepare("UPDATE admins SET status = 'disabled' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    redirect("Admin disabled successfully.");
}

// Delete Admin
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if ($id === $PRIMARY_ADMIN_ID) redirect("Cannot delete primary admin.");
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    redirect("Admin deleted successfully.");
}

// Reset Admin Password
if (isset($_GET['reset_id'])) {
    $id = (int)$_GET['reset_id'];
    if ($id === $PRIMARY_ADMIN_ID) redirect("Cannot reset primary admin password here.");
    $newPassword = password_hash('00000000', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $id);
    $stmt->execute();
    redirect("Password reset to default (00000000).");
}

// Default redirect
redirect();
?>
