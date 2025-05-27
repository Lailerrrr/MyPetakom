<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$studentID = $_SESSION['userID'];

$query = "
    SELECT m.membershipID, s.studentName, m.studentCard, m.status, m.apply_at
    FROM membership m
    JOIN student s ON m.studentID = s.studentID
    WHERE s.studentID = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$membership = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View PETAKOM Membership</title>
    <link rel="stylesheet" href="../Home/studentHomePage.css">
    <link rel="stylesheet" href="applyMembership.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
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
                <li><a href="../Merit/meritScore.php">Merit</a></li>
                <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
            <h2>📝 View Details for PETAKOM Membership</h2>

    <div class="container">

        <?php if ($membership): ?>
        
        <form class="form-box" style="max-width: 600px;">
            <div class="input-group">
                <label>Name:</label>
                <input type="text" value="<?= htmlspecialchars($membership['studentName']) ?>" disabled>
            </div>
            <div class="input-group">
                <label>Status:</label>
                <input type="text" value="<?= htmlspecialchars($membership['status']) ?>" disabled>
            </div>
            <div class="input-group">
                <label>Applied At:</label>
                <input type="text" value="<?= htmlspecialchars($membership['apply_at']) ?>" disabled>
            </div>
            <div class="input-group">
                <label>Uploaded Student Card:</label>
                <img src="../uploads/<?= htmlspecialchars($membership['studentCard']) ?>" alt="Student Card" style="width: 100%; max-width: 400px; border-radius: 10px; margin-top: 10px;">
            </div>
        </form>
        <?php else: ?>
        <p style="color: white;">You have not applied for PETAKOM membership yet.</p>
        <?php endif; ?>
    </div>
        </main>
</body>
</html>
