<?php

    session_start();
    require_once '../DB_mypetakom/db.php'; // Adjust path if your db.php is elsewhere

    if (!isset($_SESSION['userID'])) {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    $advisorID = $_SESSION['userID'];
    $advisorName = "";
    $advisorEmail = "";

    // Fetch advisor info
    $sql = "SELECT advisorName, advisorEmail FROM advisor WHERE advisorID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $advisorID);
    $stmt->execute();
    $stmt->bind_result($advisorName, $advisorEmail);
    $stmt->fetch();
    $stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Advisor Dashboard - MyPetakom</title>
        <link rel="stylesheet" href="MeritApprovalEventAdvisor.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    </head>

    <body>

    <aside class="sidebar">
        <div class="sidebar-header">
        <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="sidebar-logo" />
        <div>
            <h2>MyPetakom</h2>
            <p class="role-label">🧭 Advisor</p>
        </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="#" >Profile</a></li>
                <li><a href="#">Membership</a></li>
                <li><a href="#">Merit Overview</a></li>
                <li><a href="#">Event Registration</a></li>
                <li><a href="../attendance/advisor_attendance_slot.php">Attendance Slot</a></li>
                <li><a href="../Merit/MeritApprovalEventAdvisor.php" class="active">Merit Approval</a></li>
                <li><a href="#">Manage Events</a></li>
                <li><a href="#">Committee Management</a></li>
                <li><a href="#">Merit Applications</a></li>
                <li><a href="#">Generate QR Code</a></li>
                <li><a href="#">User Dashboard</a></li>
                <li>
                    <form method="post" action="../ManageLogin/Logout.php" style="display:inline;">
                        <button name="logout"  class="sidebar-logout-button">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">

        <!-- DASHBOARD INDICATOR -->
        <div class="dashboard-indicator">
            <span class="dashboard-role">🧭 Advisor Dashboard</span>
            <span class="dashboard-user">Logged in as: <strong><?php echo htmlspecialchars($advisorEmail); ?></strong></span>
        </div>

        <header class="main-header">
            <h1>Welcome, <span class="username"><?php echo htmlspecialchars($advisorName); ?></span>!</h1>
            <p>Here’s your PETAKOM advisor control center.</p>
        </header>






    </body>
</html>
