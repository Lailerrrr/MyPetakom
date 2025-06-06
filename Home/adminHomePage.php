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
 
// Get total number of students
$result = $conn->query("SELECT COUNT(*) AS total FROM student");
$totalUsers = ($result) ? $result->fetch_assoc()['total'] : 0;

// Pending Events
$sql = "SELECT COUNT(*) FROM event WHERE status = 'Pending'";
$result = $conn->query($sql);
$pendingEvents = $result->fetch_row()[0];

// Merit Requests Today
$sql = "SELECT COUNT(*) FROM meritapplication WHERE DATE(appliedDate) = CURDATE()";
$result = $conn->query($sql);
$meritRequests = $result->fetch_row()[0];

// Fetch event level statistics
$eventLevelLabels = [];
$eventLevelCounts = [];

$sql = "SELECT eventLevel, COUNT(*) AS levelCount FROM event GROUP BY eventLevel";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $eventLevelLabels[] = $row['eventLevel'];
    $eventLevelCounts[] = $row['levelCount'];
}
 

// Registration Trends: student attendance count per event
$trendEventNames = [];
$trendStudentCounts = [];
$sql = "
    SELECT e.eventName, COUNT(a.attendanceID) AS studentCount
    FROM attendance a
    JOIN attendanceslot s ON a.slotID = s.slotID
    JOIN event e ON s.eventID = e.eventID
    GROUP BY e.eventName
    ORDER BY studentCount DESC
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $trendEventNames[] = $row['eventName'];
    $trendStudentCounts[] = $row['studentCount'];
}

// Merit claim distribution by status
$claimStatusLabels = [];
$claimStatusCounts = [];

$sql = "SELECT claimStatus, COUNT(*) AS count FROM meritclaim GROUP BY claimStatus";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $claimStatusLabels[] = $row['claimStatus'];
    $claimStatusCounts[] = $row['count'];
}


 $uptime = "99.9%"; // Placeholder
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
                <li><a href="/MyPetakom/User/manageProfile.php">Profile</a></li>
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                <li><a href="../Attendance/admin_track_attendance.php">Attendance Tracking</a></li>
                <li><a href="#">Merit Applications</a></li>
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
        

        <h2>Reports & Analytics</h2><br>
        <section class="chart-section">
            <div class="card">
                <h2>📊 Event Statistics</h2>
                <canvas id="eventLevelChart"></canvas>
            </div>

            <div class="card">
                <h2>📈 Registration Trends</h2>
                <canvas id="registrationChart"></canvas>
            </div>

            <div class="card">
                <h2>🏅 Merit Distribution</h2>
                <canvas id="meritChart"></canvas>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const eventLevelChart = new Chart(document.getElementById('eventLevelChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($eventLevelLabels); ?>,
        datasets: [{
            label: 'Number of Events',
            data: <?= json_encode($eventLevelCounts); ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.6)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Events' }
            },
            x: {
                title: { display: true, text: 'Event Level' }
            }
        }
    }
});


        const registrationChart = new Chart(document.getElementById('registrationChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($trendEventNames); ?>,
                datasets: [{
                    label: 'Number of Students Attended',
                    data: <?= json_encode($trendStudentCounts); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Students' }
                    },
                    x: {
                        title: { display: true, text: 'Event Name' }
                    }
                }
            }
        });

       const meritChart = new Chart(document.getElementById('meritChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($claimStatusLabels); ?>,
        datasets: [{
            label: 'Merit Claim Status',
            data: <?= json_encode($claimStatusCounts); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 205, 86, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(54, 162, 235, 0.6)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
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
