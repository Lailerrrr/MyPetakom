<?php
    session_start();
    require_once '../DB_mypetakom/db.php'; // adjust path if needed

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    $email = $_SESSION['email'];
    $name = "";

    // Get student name from database
    $sql = "SELECT studentName, studentID FROM student WHERE studentEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($name, $student_id);
    $stmt->fetch();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="studentHomePage.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
            <div class="sidebar-text">
                <h2>MyPetakom</h2>
                <p class="role-label">🎓 Student</p>
            </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="#" class="active">Profile</a></li>
                <li><a href="#">Manage Membership</a></li>
                <li><a href="../attendance/event_register.php">Attendance Registration</a></li>
                <li><a href="#">Merit Claim</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">

        <div class="dashboard-indicator" style="margin-bottom: 25px; width: 100%; max-width: 600px; display: flex; justify-content: space-between; color: #ffd1e8; font-weight: 600;">
            <span class="dashboard-role">🎓 Student Dashboard</span>
            <span class="dashboard-user">Logged in as: <strong><?php echo htmlspecialchars($email); ?></strong></span>
        </div>

        <header class="main-header">
            <h1>Welcome back, <span class="username"><?php echo htmlspecialchars($name); ?></span>!</h1>
            <p>Your activity summary & updates</p>
            <div style="margin-top: 10px; font-size: 16px; color: #fdd;">
                <strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?>
            </div>
        </header>

        <div class="slideshow-container">
            <div class="mySlides fade">
                <div class="numbertext">1 / 5</div>
                    <img src="/MyPetakom/p1.jpg" style="width:100%">
            </div>

            <div class="mySlides fade">
                <div class="numbertext">2 / 5</div>
                <img src="/MyPetakom/p2.jpg" style="width:100%">
            </div>

             <div class="mySlides fade">
                <div class="numbertext">3 / 5</div>
                <img src="/MyPetakom/p3.jpg" style="width:100%">
            </div>

            <div class="mySlides fade">
                <div class="numbertext">4 / 5</div>
                  <img src="/MyPetakom/p4.jpg" style="width:100%">
            </div>

            <div class="mySlides fade">
                <div class="numbertext">5 / 5</div>
                <img src="/MyPetakom/p5.jpg" style="width:100%">
            </div>

            <! Next and prev button>
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a> 
  </div>
  <br>
<!-- The dots/circles -->
                <div style="text-align:center">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                    <span class="dot" onclick="currentSlide(4)"></span>
                    <span class="dot" onclick="currentSlide(5)"></span>
</div>


    </main>


<script src="studentHomePage.js"></script>
</body>
</html>



