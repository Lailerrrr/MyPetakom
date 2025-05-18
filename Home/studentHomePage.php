<?php
    session_start();

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    $email = $_SESSION['email'];
    
?>


<!DOCTYPE html>
<html lang="en" >

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
                    <li><a href="#">Event Registration</a></li>
                    <li><a href="#">Attandance Registration</a></li>
                    <li><a href="../ManageLogin/Logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">

            <!-- DASHBOARD INDICATOR -->
            <div class="dashboard-indicator" style="margin-bottom: 25px; width: 100%; max-width: 600px; display: flex; justify-content: space-between; color: #ffd1e8; font-weight: 600;">
            <span class="dashboard-role">🎓 Student Dashboard</span>
            <span class="dashboard-user">Logged in as: <strong><?php echo htmlspecialchars($email); ?></strong></span>
            </div>


            <header class="main-header">
                <h1>Welcome back, <span class="username"></span><?php echo htmlspecialchars($email); ?></span>!</h1>
                <p>Your activity summary & updates</p>
            </header>

            <section class="dashboard-cards">
            <div class="card">
                <h3>Upcoming Events</h3>
                <p>3 events registered</p>
            </div>

            <div class="card">
                <h3>Membership Status</h3>
                <p>Active until: <strong>Dec 2025</strong></p>
            </div>

            <div class="card">
                <h3>Attended Events</h3>
                <p>5 events attended this year</p>
            </div>

            <div class="card">
                <h3>Notifications</h3>
                <p>2 new messages from coordinators</p>
            </div>
            </section>



        </main>

      

    </body>
</html>
