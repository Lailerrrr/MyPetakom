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

    $adminID = $_SESSION['userID'];
    $adminName = "";
    $adminEmail = "";

    // Fetch admin details
    $sql = "SELECT staffName, staffEmail, staffRole FROM staff WHERE staffID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $staffID);
    $stmt->execute();
    $stmt->bind_result($staffName, $staffEmail, $staffRole);
    $stmt->fetch();
    $stmt->close();

    // Placeholder stats
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
    <link rel="stylesheet" href="adminHomePage.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }

        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background-color: #222;
            color: white;
            padding: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            flex: 1 1 200px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .chart-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            margin-top: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2>MyPetakom</h2>
    <p>Administrator</p>
    <nav class="menu">
        <ul>
            <li><a href="#" class="active">Dashboard</a></li>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Membership Applications</a></li>
            <li><a href="../Module2/eventApproval.php">Event Management</a></li>
            <li><a href="#">Attendance Tracking</a></li>
            <li><a href="#">Merit Applications</a></li>
            <li><a href="#">Reports & Analytics</a></li>
            <li><a href="#">System Settings</a></li>
            <li><form method="post" action="../ManageLogin/Logout.php"><button name="logout">Logout</button></form></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1>

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
</script>

<script>
// This forces re-navigation on back button
window.history.pushState(null, "", window.location.href);
window.onpopstate = function () {
    window.location.href = "../ManageLogin/logout.php";
};
</script>


</body>
</html>
