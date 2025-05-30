<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$email = $_SESSION['email'];
$name = "";
$student_id = "";

// Get student info
$sql = "SELECT studentName, studentID FROM student WHERE studentEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($name, $student_id);
$stmt->fetch();
$stmt->close();

// Get attendance slots the student can register for
$sql_events = "SELECT e.eventName, e.eventDescription, e.eventDate, e.venue, e.qrCode, s.slotID, s.slotName, s.attendanceDate, s.slotTime 
               FROM AttendanceSlot s
               JOIN event e ON s.eventID = e.eventID";
$result_events = $conn->query($sql_events);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance Registration - MyPetakom</title>
    <link rel="stylesheet" href="style.css">
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
                <li><a href="../User/Profiles.php">Profile</a></li>
                <li><a href="../membership/applyMembership.php">Apply Membership</a></li>
                <li><a href="../membership/viewMembership.php" >View Membership</a></li>
                <li><a href="../Attendance/event_register.php" class="active">Event Attendance</a></li>
                <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
        </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>Event Attendance Slots</h1>
        <p>Welcome, <strong><?= htmlspecialchars($name) ?></strong> (<?= htmlspecialchars($student_id) ?>)</p>
    </header>

    <div class="event-list">
        <?php if ($result_events->num_rows > 0): ?>
            <?php while ($row = $result_events->fetch_assoc()): ?>
                <div class="event-card">
                    <div class="event-details">
                        <h3><?= htmlspecialchars($row['eventName']) ?> (<?= htmlspecialchars($row['slotName']) ?>)</h3>
                        <p><strong>Description:</strong> <?= htmlspecialchars($row['eventDescription']) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($row['attendanceDate']) ?></p>
                        <p><strong>Time:</strong> <?= htmlspecialchars($row['slotTime']) ?></p>
                        <p><strong>Venue:</strong> <?= htmlspecialchars($row['venue']) ?></p>
                    </div>
                    <div class="event-qr">
                        <img src="<?= htmlspecialchars($row['qrCode']) ?>" alt="QR Code" width="150">
                        <p>Scan to Register Attendance</p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No event slots available.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
