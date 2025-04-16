<?php 
session_start(); 
require_once '../includes/db-conn.php';  

// Redirect if not logged in 
if (!isset($_SESSION['admin_id'])) {     
    header("Location: ../index.php");     
    exit(); 
}  

// Fetch user details 
$user_id = $_SESSION['admin_id']; 
$sql = "SELECT * FROM admins WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $user_id); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$user = $result->fetch_assoc(); 
$stmt->close();  

// Fetch all admins
$query = "SELECT id, username, nic, email, mobile, linkedin, blog, github, facebook, profile_picture FROM admins"; 
$result = mysqli_query($conn, $query); 
$admins = mysqli_fetch_all($result, MYSQLI_ASSOC); 
?>  

<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="utf-8">     
    <meta content="width=device-width, initial-scale=1.0" name="viewport">     
    <title>Home - EduWide</title>  

    <?php include_once("../includes/css-links-inc.php"); ?>     
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">  

    <style type="text/css">  
        .card.lecturer-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: #fff;
            transition: transform 0.3s ease;
        }

        .card.lecturer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .card-img-wrapper {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .card-img-top {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(13, 110, 253, 1);
            object-fit: cover;
        }

        .card-body {
            text-align: left;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
        }

        .social-links a {
            margin: 0 10px;
            font-size: 1.5rem;
            color: #555;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #007bff;
        }

        .social-links i {
            transition: transform 0.2s ease;
        }

        .social-links a:hover i {
            transform: scale(1.2);
        }

        ul.list-unstyled li {
            font-size: 1rem;
            color: #333;
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        ul.list-unstyled li i {
            margin-right: 8px;
            color: #007bff;
        }

        .mini-card {
            border-radius: 12px;
            transition: transform 0.2s;
            background-color: #f8f9fa;
        }

        .mini-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
</head> 

<body>     
    <main id="main" class="main">         
        <div class="pagetitle">             
            <h1>Admin Panel</h1>             
            <nav>                 
                <ol class="breadcrumb">                     
                    <li class="breadcrumb-item"><a href="">Home</a></li>                 
                </ol>             
            </nav>         
        </div>  

        <section class="section">             
            <div class="row">                 
                <div class="col-lg-12">                     
                    <div class="card">                         
                        <div class="card-body">                             
                            <?php include_once("../includes/header.php") ?>                             
                            <?php include_once("../includes/sadmin-sidebar.php") ?>  

                            <div class="container mt-4">                                 
                                <div class="row">                                     
                                    <?php foreach ($admins as $admin): ?>                                         
                                        <div class="col-md-4 mb-4">                                             
                                            <div class="card lecturer-card shadow-lg rounded">                                                 
                                                <div class="text-center mt-3 mb-3">                                                     
                                                    <img src="<?php echo $admin['profile_picture']; ?>" class="card-img-top" alt="Profile Picture" onerror="this.onerror=null;this.src='uploads/profile_pictures/default.jpg';">                                                 
                                                </div>                                                 
                                                <div class="card-body" style="height: 220px;">                                                     
                                                    <h4 class="text-primary text-center"><?php echo $admin['username']; ?></h4>                                                     
                                                    <div class="card-text mt-1"><strong>Email:</strong> <?php echo $admin['email']; ?></div>                                                     
                                                    <div class="card-text mt-1"><strong>Mobile:</strong> <?php echo $admin['mobile']; ?></div>                                                     
                                                    <div class="social-links mt-2">                                                         
                                                        <strong>Social Links:</strong><br>                                                         
                                                        <?php if ($admin['linkedin']) echo '<a href="'.$admin['linkedin'].'" target="_blank"><i class="fab fa-linkedin" style="color: #0077B5;"></i></a>'; ?>                                                         
                                                        <?php if ($admin['blog']) echo '<a href="'.$admin['blog'].'" target="_blank"><i class="fas fa-blog" style="color: #fc4f08;"></i></a>'; ?>                                                         
                                                        <?php if ($admin['github']) echo '<a href="'.$admin['github'].'" target="_blank"><i class="fab fa-github" style="color: #171515;"></i></a>'; ?>                                                         
                                                        <?php if ($admin['facebook']) echo '<a href="'.$admin['facebook'].'" target="_blank"><i class="fab fa-facebook" style="color: #1877F2;"></i></a>'; ?>                                                     
                                                    </div>                                                 
                                                </div>                                             
                                            </div>                                         
                                        </div>                                     
                                    <?php endforeach; ?>                                 
                                </div>                             
                            </div>  

                            <!-- Recently Logged-In Admins Section -->
                            <?php 
                                $recent_admins_query = "SELECT * FROM admins ORDER BY last_login DESC LIMIT 5";
                                $recent_admins_result = $conn->query($recent_admins_query);
                            ?>
                            <div class="container mt-4">                                 
                                <h4 class="mb-3">Recently Logged-In Admins</h4>                                 
                                <div class="row">                                     
                                    <?php while ($admin = $recent_admins_result->fetch_assoc()): ?>                                         
                                        <div class="col-md-4 col-lg-3 mb-3">                                             
                                            <div class="card mini-card shadow-lg">                                                 
                                                <div class="d-flex align-items-center p-2">                                                     
                                                    <img src="<?php echo $admin['profile_picture']; ?>" alt="Profile Picture"
                                                         class="rounded-circle me-2"
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         onerror="this.onerror=null;this.src='uploads/profile_pictures/default.jpg';">                                                     
                                                    <div>                                                         
                                                        <h6 class="mb-0"><?php echo $admin['username']; ?></h6>                                                         
                                                        <small class="text-muted">
                                                            <?php 
                                                                if (!empty($admin['last_login'])) {
                                                                    echo "Last login: " . date("M d, Y h:i A", strtotime($admin['last_login']));
                                                                } else {
                                                                    echo "Last login: N/A";
                                                                }
                                                            ?>
                                                        </small>                                                     
                                                    </div>                                                 
                                                </div>                                             
                                            </div>                                         
                                        </div>                                     
                                    <?php endwhile; ?>                                 
                                </div>                             
                            </div>                         

                        </div>                     
                    </div>                 
                </div>             
            </div>         
        </section>     
    </main>  

    <?php include_once("../includes/footer.php") ?>     
    <?php include_once("../includes/js-links-inc.php") ?> 
</body> 
</html>
