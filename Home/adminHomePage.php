<?php
session_start();

// 🔒 Prevent page from being cached after logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$staffName = "";
$staffEmail = "";
$staffRole = "";

// Fetch staff details
$sql = "SELECT staffName, staffEmail, staffRole FROM staff WHERE staffID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffID);
$stmt->execute();
$stmt->bind_result($staffName, $staffEmail, $staffRole);
$stmt->fetch();
$stmt->close();

// Sample dashboard stats
$totalUsers = 120;
$pendingEvents = 5;
$meritRequests = 8;
$uptime = "99.9%";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="adminHomePage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
            <div class="sidebar-text">
                <h2>MyPetakom</h2>
                <p class="role-label">🧑‍💼 PETAKOM Coordinator</p>
            </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="../Home/adminHomePage.php" class="active">Dashboard</a></li>
                <li><a href="../User/adminUserManagement.php">Profile</a></li>
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                <li><a href="#">Attendance Tracking</a></li>
                <li><a href="#">Merit Applications</a></li>
                <li><a href="#">Reports & Analytics</a></li>
                <li><a href="#">System Settings</a></li>
                <li>
                <li>
                    <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                        <button type="submit" name="logout" class="sidebar-button">Logout</button>
                    </form>
                </li>


            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="main-header">
            <h1>Welcome, <?= htmlspecialchars($staffName); ?>!</h1>
            <p>Your role: <span class="username"><?= htmlspecialchars($staffRole); ?></span></p>
        </div>

        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Users</h3>
                <p><?= $totalUsers; ?>+ Registered</p>
            </div>
            <div class="card">
                <h3>Pending Events</h3>
                <p><?= $pendingEvents; ?> Waiting Approval</p>
            </div>
            <div class="card">
                <h3>Merit Requests</h3>
                <p><?= $meritRequests; ?> Submitted Today</p>
            </div>
            <div class="card">
                <h3>System Uptime</h3>
                <p><?= $uptime; ?> This Month</p>
            </div>
        </section>

        <section class="chart-section">
            <h2>📊 Event Statistics</h2>
            <canvas id="eventChart"></canvas>

            <h2>📈 Registration Trends</h2>
            <canvas id="registrationChart"></canvas>

            <h2>🏅 Merit Distribution</h2>
            <canvas id="meritChart"></canvas>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const eventChart = new Chart(document.getElementById('eventChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Workshop', 'Talk', 'Seminar', 'Webinar'],
                datasets: [{
                    label: 'Events by Type',
                    data: [5, 3, 4, 2],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            }
        });

        const registrationChart = new Chart(document.getElementById('registrationChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Registrations',
                    data: [10, 20, 15, 25, 18],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: true
                }]
            }
        });

        const meritChart = new Chart(document.getElementById('meritChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    label: 'Merit Status',
                    data: [50, 20, 10],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ]
                }]
            }
        });

        // Prevent back navigation to cached page after logout
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function () {
            window.location.href = "../ManageLogin/logout.php";
        };
    </script>
</body>
</html>
