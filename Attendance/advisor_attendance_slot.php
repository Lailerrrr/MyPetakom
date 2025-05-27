<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$slots = [];

// Fetch slots created by the advisor
$sql = "SELECT s.slotID, s.slotName, s.qrCodePath, s.attendanceDate, s.slotTime, e.eventName
        FROM AttendanceSlot s
        JOIN event e ON s.eventID = e.eventID
        WHERE e.staffID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Slots - Advisor</title>
    <link rel="stylesheet" href="../Attendance/advisor_attendance_slot.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
                
                <li><a href="../Home/advisorHomepage.php">User Dashboard</a></li>
                <li><a href="../Advisor/advisorProfile.php">Profile</a></li>
                <li><a href="../Module2/eventList.php">Event List</a></li>
                <li><a href="../Module2/eventRegistration.php">Event Registration</a></li>
                <li><a href="../Module2/manageEvent.php">Manage Events</a></li>
                <li><a href="../Module2/eventCommittee.php">Committee Management</a></li>
                <li><a href="../Module2/eventMerit.php">Merit Applications</a></li>
                <li><a href="../Attendance/advisor_attendance_slot.php" class="active">Attendance Slot</a></li>

                <li>
                    <form method="post" action="../ManageLogin/Logout.php" style="display:inline;">
                        <button name="logout"  class="sidebar-logout-button">Logout</button>
                    </form>
                </li>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="dashboard-indicator">
            <span class="dashboard-role">📅 Attendance Slot Management</span>

        </div>

        <header class="main-header">
            <h1>Manage Your Attendance Slots</h1>
            <p>View all attendance slots you've created for events.</p>
        </header>

        <section class="dashboard-cards">
            <?php if (count($slots) > 0): ?>
                <?php foreach ($slots as $slot): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($slot['slotName']); ?></h3>
                        <p><strong>Event:</strong> <?php echo htmlspecialchars($slot['eventName']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($slot['attendanceDate']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($slot['slotTime']); ?></p>
                        <p><strong>QR Code:</strong><br>
                            <img src="../QR/<?php echo htmlspecialchars($slot['qrCodePath']); ?>" alt="QR Code" width="120">
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #fcdfff;">No attendance slots found.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
