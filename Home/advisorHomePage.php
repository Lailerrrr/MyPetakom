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
    $staffRole = strtolower($_SESSION['staffRole']);


    // Access control: only allow advisors
    if ($staffRole !== 'advisor') {
        echo "Access denied. This section is only for advisors.";
        exit();
    }


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
    $totalEvents = getSingleValue($conn, "SELECT COUNT(*) as total FROM event WHERE staffID = '$staffID'");

    // Total Registered Students under advisor's events
    $totalStudents = getSingleValue($conn, "
        SELECT COUNT(DISTINCT r.studentID) AS total
        FROM registration r
        JOIN event e ON r.eventID = e.eventID
        WHERE e.staffID = '$staffID'
    ");

    // Total Committee Members
    $totalCommittees = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM committee c
        JOIN event e ON c.eventID = e.eventID
        WHERE e.staffID = '$staffID'
    ");

    // Students per Event Chart
    $studentsPerEvent = $conn->query("
        SELECT e.eventName, COUNT(r.studentID) as studentCount
        FROM registration r
        JOIN event e ON r.eventID = e.eventID
        WHERE e.staffID = '$staffID'
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
        WHERE e.staffID = '$staffID'
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
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
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

        <script>
            // This forces re-navigation on back button
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function () {
                window.location.href = "../ManageLogin/logout.php";
            };
        </script>

    </body>
</html>
