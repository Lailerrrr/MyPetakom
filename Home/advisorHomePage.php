<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];


// Helper function to safely fetch a single value
function getSingleValue($conn, $query, $default = 0) {
    $result = $conn->query($query);
    if (!$result) {
        return $default; // Avoid fatal error and return default value
    }
    $row = $result->fetch_assoc();
    return $row ? $row['total'] : $default;
}

// Total Events
$totalEvents = getSingleValue($conn, "SELECT COUNT(*) as total FROM event WHERE advisorID = '$advisorID'");

// Total Registered Students under advisor's events
$totalStudents = getSingleValue($conn, "
    SELECT COUNT(DISTINCT r.studentID) AS total
    FROM registration r
    JOIN event e ON r.eventID = e.eventID
    WHERE e.advisorID = '$advisorID'
");

// Total Committee Members
$totalCommittees = getSingleValue($conn, "
    SELECT COUNT(*) AS total
    FROM committee c
    JOIN event e ON c.eventID = e.eventID
    WHERE e.advisorID = '$advisorID'
");

// Students per Event Chart
$studentsPerEvent = $conn->query("
    SELECT e.eventName, COUNT(r.studentID) as studentCount
    FROM registration r
    JOIN event e ON r.eventID = e.eventID
    WHERE e.advisorID = '$advisorID'
    GROUP BY r.eventID
");

$eventNames = [];
$studentCounts = [];
if ($studentsPerEvent) {
    while ($row = $studentsPerEvent->fetch_assoc()) {
        $eventNames[] = $row['eventName'];
        $studentCounts[] = $row['studentCount'];
    }
}

// Committee Role Distribution Chart
$roleDist = $conn->query("
    SELECT c.role, COUNT(*) AS count
    FROM committee c
    JOIN event e ON c.eventID = e.eventID
    WHERE e.advisorID = '$advisorID'
    GROUP BY c.role
");

$roles = [];
$roleCounts = [];
if ($roleDist) {
    while ($row = $roleDist->fetch_assoc()) {
        $roles[] = $row['role'];
        $roleCounts[] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Advisor Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="advisorHomePage.css" />
    <!-- <link rel="stylesheet" href="../Module2/MeritApplication.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- <style>
        .container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color:rgb(40, 0, 46);
            color: #333;
        }

        .dashboard-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(72, 5, 91, 0.1);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* canvas {
            background: white;
            border-radius: 12px;
            padding: 20px;
        } */
    </style> -->
</head>
<body>
<div class="container">
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <h1>📊 Advisor Dashboard</h1>
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total Events</h3>
                <p style="font-size: 28px;"><?php echo $totalEvents; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Registered Students</h3>
                <p style="font-size: 28px;"><?php echo $totalStudents; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Committee Members</h3>
                <p style="font-size: 28px;"><?php echo $totalCommittees; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>👨‍🎓 Students Per Event</h3>
                <canvas id="studentsChart"></canvas>
            </div>
            <div class="dashboard-card">
                <h3>🧑‍💼 Committee Role Distribution</h3>
                <canvas id="rolesChart"></canvas>
            </div>
        </div>
    </main>
</div>

<script>
    const eventNames = <?php echo json_encode($eventNames); ?>;
    const studentCounts = <?php echo json_encode($studentCounts); ?>;
    const roles = <?php echo json_encode($roles); ?>;
    const roleCounts = <?php echo json_encode($roleCounts); ?>;

    new Chart(document.getElementById('studentsChart'), {
        type: 'bar',
        data: {
            labels: eventNames,
            datasets: [{
                label: 'Number of Students',
                data: studentCounts,
                backgroundColor: '#c061cb'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('rolesChart'), {
        type: 'pie',
        data: {
            labels: roles,
            datasets: [{
                label: 'Role Distribution',
                data: roleCounts,
                backgroundColor: ['#845EC2', '#D65DB1', '#FF6F91', '#FF9671']
            }]
        }
    });
</script>
</body>
</html>
