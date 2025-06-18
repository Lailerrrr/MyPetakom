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

// Fetch attendance records for the student (to identify already registered slotIDs)
$registered_events = [];
$sql_attendance_ids = "SELECT slotID FROM Attendance WHERE studentID = ?";
$stmt_ids = $conn->prepare($sql_attendance_ids);
$stmt_ids->bind_param("s", $student_id);
$stmt_ids->execute();
$result_ids = $stmt_ids->get_result();

while ($row = $result_ids->fetch_assoc()) {
    $registered_events[] = $row['slotID'];
}
$stmt_ids->close();

// Get attendance slots + event info
$sql_events = "SELECT e.eventName, e.eventDescription, e.eventDate, e.venue, 
                      s.slotID, s.slotName, s.attendanceDate, s.slotTime, s.qrCodePath 
               FROM AttendanceSlot s
               JOIN event e ON s.eventID = e.eventID";
$result_events = $conn->query($sql_events);

// Fetch detailed attendance records for display
$sql_attendance = "
    SELECT a.attendanceID, a.checkInTime, a.checkInDate, a.location, a.slotID, s.slotName
    FROM Attendance a
    JOIN AttendanceSlot s ON a.slotID = s.slotID
    WHERE a.studentID = ?";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("s", $student_id);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();
$stmt_attendance->close();
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
<div class="layout">
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
            <li><a href="../membership/viewMembership.php">View Membership</a></li>
            <li><a href="../Attendance/event_register.php" class="active">Event Attendance</a></li>
            <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
            <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
            <li><a href="../Module2/StudentEvent.php">Event Detail</a></li>
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
                <?php if (!in_array($row['slotID'], $registered_events)): ?>
                    <div class="event-card">
                        <div class="event-details">
                            <h3><?= htmlspecialchars($row['eventName']) ?> (<?= htmlspecialchars($row['slotName']) ?>)</h3>
                            <p><strong>Description:</strong> <?= htmlspecialchars($row['eventDescription']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($row['attendanceDate']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($row['slotTime']) ?></p>
                            <p><strong>Venue:</strong> <?= htmlspecialchars($row['venue']) ?></p>
                        </div>
                        <div class="event-qr">
                            <img src="<?= '../QR/' . htmlspecialchars($row['qrCodePath']) ?>" alt="QR Code" width="150">
                            <p>Scan to Register Attendance</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No event slots available.</p>
        <?php endif; ?>
    </div>

    <br><br>

    <!-- Display Attendance Records -->
    <div class="table-wrapper">
        <h2>Your Attendance Records</h2>
        <section class="form-section">
        <table class="table">
            <thead>
                <tr>
                    <th>Attendance ID</th>
                    <th>Check-In Time</th>
                    <th>Check-In Date</th>
                    <th>Location</th>
                    <th>Slot ID</th>
                    <th>Slot Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_attendance->num_rows > 0): ?>
                    <?php while ($attendance = $result_attendance->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($attendance['attendanceID']) ?></td>
                            <td><?= htmlspecialchars($attendance['checkInTime']) ?></td>
                            <td><?= htmlspecialchars($attendance['checkInDate']) ?></td>
                            <td><?= htmlspecialchars($attendance['location']) ?></td>
                            <td><?= htmlspecialchars($attendance['slotID']) ?></td>
                            <td><?= htmlspecialchars($attendance['slotName']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </section>
    </div>
</main>
</div>
</body>
</html>
