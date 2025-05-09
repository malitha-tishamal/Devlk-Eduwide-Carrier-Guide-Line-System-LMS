<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cert_name = $_POST["cert_name"];
    $issued_by = $_POST["issued_by"];
    $cert_date = $_POST["cert_date"];
    $cert_link = $_POST["cert_link"];
    $cert_description = $_POST["cert_description"];
    
    // File upload
    $image_name = $_FILES["cert_image"]["name"];
    $image_tmp = $_FILES["cert_image"]["tmp_name"];
    $upload_dir = "uploads/";
    $image_path = $upload_dir . basename($image_name);

    if (move_uploaded_file($image_tmp, $image_path)) {
        // Connect to database
        $conn = new mysqli("localhost", "root", "", "your_database_name");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert into database
        $sql = "INSERT INTO certifications (cert_name, issued_by, cert_date, cert_link, cert_description, image_path)
                VALUES ('$cert_name', '$issued_by', '$cert_date', '$cert_link', '$cert_description', '$image_path')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Certification added successfully');</script>";
        } else {
            echo "Error: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "<script>alert('Image upload failed');</script>";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Certification</title>
    <style>
        input, textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
        }
        button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <h2>Add Certification</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Certification Name</label>
        <input type="text" name="cert_name" required>

        <label>Issued By</label>
        <input type="text" name="issued_by" required>

        <label>Date</label>
        <input type="date" name="cert_date" required>

        <label>Link (if applicable)</label>
        <input type="url" name="cert_link">

        <label>Certification Description</label>
        <textarea name="cert_description" rows="4" required></textarea>

        <label>Certification Image</label>
        <input type="file" name="cert_image" required>

        <button type="submit">Submit Certification</button>
    </form>
</body>
</html>
