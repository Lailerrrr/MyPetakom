<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_attendance'])) {
    $studentID = $_POST['studentID'];
    $slotID = $_POST['slotID'];
    $deleteSql = "DELETE FROM Attendance WHERE studentID = ? AND slotID = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ss", $studentID, $slotID);
    $stmt->execute();
    $stmt->close();
}

// Grouped attendance query
$sql = "
    SELECT 
        e.eventID,
        e.eventName, 
        e.eventDate, 
        s.slotID,
        s.slotName, 
        s.attendanceDate, 
        s.slotTime, 
        st.studentName, 
        st.studentID, 
        a.checkInDate, 
        a.checkInTime, 
        a.location
    FROM Attendance a
    JOIN AttendanceSlot s ON a.slotID = s.slotID
    JOIN event e ON s.eventID = e.eventID
    JOIN student st ON a.studentID = st.studentID
    ORDER BY e.eventName, s.attendanceDate, st.studentName ASC
";

$result = $conn->query($sql);

// Organize by event
$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $eventKey = $row['eventID'] . ' - ' . $row['eventName'];
    $groupedData[$eventKey][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracking - PETAKOM Admin</title>
    <link rel="stylesheet" href="../Attendance/track.css">
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
                <li><a href="../Home/adminHomePage.php">Dashboard</a></li>
                <li><a href="/MyPetakom/User/manageProfile.php">Profile</a></li>
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                <li><a href="#" class="active">Attendance Tracking</a></li>
                <li><a href="#">Merit Applications</a></li>
                <li><a href="#">Reports & Analytics</a></li>
                <li><a href="#">System Settings</a></li>
                <li>
                    <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                        <button type="submit" name="logout" class="sidebar-button">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="main-header">
            <h1>Student Attendance Tracking</h1>
            <p>Each event has a separate attendance table. You can remove students from events.</p>
        </header>

        <?php if (!empty($groupedData)): ?>
            <?php foreach ($groupedData as $eventLabel => $records): ?>
                
                    <h2><?= htmlspecialchars($eventLabel) ?></h2>
                    <div style="overflow-x: auto;">
                        <section class="form-section">
                        <table class="table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Slot Name</th>
                                    <th>Slot Date</th>
                                    <th>Slot Time</th>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Check-In Date</th>
                                    <th>Check-In Time</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['slotName']) ?></td>
                                        <td><?= htmlspecialchars($row['attendanceDate']) ?></td>
                                        <td><?= htmlspecialchars($row['slotTime']) ?></td>
                                        <td><?= htmlspecialchars($row['studentName']) ?></td>
                                        <td><?= htmlspecialchars($row['studentID']) ?></td>
                                        <td><?= htmlspecialchars($row['checkInDate']) ?></td>
                                        <td><?= htmlspecialchars($row['checkInTime']) ?></td>
                                        <td><?= htmlspecialchars($row['location']) ?></td>
                                        <td>
                                            <form method="post" onsubmit="return confirm('Are you sure you want to remove this student?');">
                                                <input type="hidden" name="studentID" value="<?= htmlspecialchars($row['studentID']) ?>">
                                                <input type="hidden" name="slotID" value="<?= htmlspecialchars($row['slotID']) ?>">
                                                <button type="submit" name="delete_attendance" class="delete-button">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </section>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <section class="form-section">
                <p>No attendance records found.</p>
            
        <?php endif; ?>
    </main>
</body>
</html>
