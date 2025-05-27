
<?php
    session_start();
    require_once '../DB_mypetakom/db.php';

    // Redirect to login if not logged in
    if (!isset($_SESSION['userID'])) {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    // Fetch membership applications with student info
    
    $query = "
    SELECT m.membershipID, s.studentName, m.studentCard, m.status, m.apply_at
    FROM membership m
    JOIN student s ON m.studentID = s.studentID
    ORDER BY m.apply_at DESC
";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>View Membership Applications</title>
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
        <div class="container">
            <h2>View Membership Applications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Program</th>
                        <th>Student Card</th>
                        <th>Status</th>
                        <th>Applied At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td>
                                <a href="../uploads/<?= htmlspecialchars($row['studentCard']) ?>" target="_blank">View</a>
                            </td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['apply_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
