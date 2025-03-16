<?php
require_once '../includes/db-conn.php'; //db conn

if (isset($_POST['year'])) {
    $year = $_POST['year'];

    // Fetch students for the selected year, including their profile picture
    $sql = "SELECT reg_id, username, profile_picture FROM students WHERE study_year = ? ORDER BY username ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
    echo json_encode($students);
}
?>
