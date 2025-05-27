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

// Get events the student is participating in (replace with actual logic)
$events = [
    [
        'name' => 'Tech Talk 2025',
        'description' => 'A session with industry leaders about AI and future tech.',
        'date' => '2025-06-10',
        'venue' => 'Auditorium A',
        'qr_image' => '../qr_attendance.png'
    ],
    [
        'name' => 'Cyber Security Workshop',
        'description' => 'Learn about ethical hacking and digital defense.',
        'date' => '2025-06-15',
        'venue' => 'Lab 3, Block B',
        'qr_image' => '../qr_attendance.png'

    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Event Attendance - MyPetakom</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
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
                <li><a href="#">Profile</a></li>
                <li><a href="../membership/applyMembership.php">Apply Membership</a></li>
                <li><a href="../membership/viewMembership.php">View Membership</a></li>
                <li><a href="../Attendance/event_register.php">Attendance Registration</a></li>
                <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
        </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>My Events</h1>
        <p>Welcome, <strong><?= htmlspecialchars($name) ?></strong> (<?= htmlspecialchars($student_id) ?>)</p>
    </header>

    <div class="event-list">
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <div class="event-details">
                    <h3><?= htmlspecialchars($event['name']) ?></h3>
                    <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
                    <p><strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
                    <form method="post" action="delete_event.php" onsubmit="return confirm('Are you sure you want to unregister?');">
                        <input type="hidden" name="event_name" value="<?= htmlspecialchars($event['name']) ?>">
                        <button type="submit" class="btn-delete">Unregister</button>
                    </form>
                </div>
                <div class="event-qr">
                    <img src="<?= htmlspecialchars($event['qr_image']) ?>" alt="QR Code" />
                    <p>Scan this QR during event</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>
