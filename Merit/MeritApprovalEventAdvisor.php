<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$success = '';
$error = '';

if (isset($_GET['msg'])) {
    $msg = htmlspecialchars($_GET['msg']);
    $success = "Claim has been $msg successfully.";
}

// === Handle form submission (approval/rejection) ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['claimID'], $_POST['action'])) {
    $claimID = $_POST['claimID'];
    $action = $_POST['action'];
    $status = ($action == "approve") ? "Approved" : "Rejected";
    $approval_date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE meritClaim SET claimStatus=?, approval_date=?, approval_by=? WHERE claimID=?");
    $stmt->bind_param("ssss", $status, $approval_date, $staffID, $claimID);

    if ($stmt->execute()) {
        header("Location: MeritApprovalEventAdvisor.php?msg=$status");
        exit();
    } else {
        $error = "Error updating claim: " . $stmt->error;
    }
}

// === Fetch pending claims ===
$result = mysqli_query($conn, "SELECT * FROM meritClaim WHERE claimStatus = 'Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Merit Approval - Advisor</title>
    <link rel="stylesheet" href="MeritApprovalEventAdvisor.css">
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
            <li><a href="../Attendance/advisor_attendance_slot.php">Attendance Slot</a></li>
            <li><a href="../Merit/MeritApprovalEventAdvisor.php" class="active">Merit Approval</a></li>
            <li>
                <form method="post" action="../ManageLogin/Logout.php" style="display:inline;">
                    <button name="logout" class="sidebar-logout-button">Logout</button>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <div class="dashboard-indicator">
        <span class="dashboard-role">🧭 Advisor Dashboard</span>
    </div>

    <?php if ($success): ?>
        <div class="success-message"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Claim ID</th>
                <th>Letter</th>
                <th>Event</th>
                <th>Student</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['claimID']) ?></td>
                    <td><a href="uploads/<?= htmlspecialchars($row['claimLetter']) ?>" target="_blank">View</a></td>
                    <td><?= htmlspecialchars($row['eventID']) ?></td>
                    <td><?= htmlspecialchars($row['studentID']) ?></td>
                    <td>
                        <form method="post" action="MeritApprovalEventAdvisor.php" style="display:inline;">
                            <input type="hidden" name="claimID" value="<?= htmlspecialchars($row['claimID']) ?>">
                            <button type="submit" name="action" value="approve">✅ Approve</button>
                            <button type="submit" name="action" value="reject">❌ Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending merit claims to review.</p>
    <?php endif; ?>
</main>
</body>
</html>

