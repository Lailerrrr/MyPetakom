<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['membershipID'], $_POST['action'])) {
    $membershipID = $_POST['membershipID'];
    $newStatus = $_POST['action'] === "approve" ? "Approved" : "Rejected";

    $stmt = $conn->prepare("UPDATE membership SET status = ? WHERE membershipID = ?");
    $stmt->bind_param("ss", $newStatus, $membershipID);
    $stmt->execute();
    $stmt->close();
}

$results = $conn->query("SELECT m.membershipID, m.studentCard, m.status, s.studentName, s.studentID 
                         FROM membership m 
                         JOIN student s ON m.studentID = s.studentID 
                         WHERE m.status = 'Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Membership</title>
    <link rel="stylesheet" href="verifyMembership.css">
</head>
<body>
    <h2>📋 Pending Membership Applications</h2>
    <?php if ($results->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Student Card</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['studentName']) ?></td>
                    <td><?= htmlspecialchars($row['studentID']) ?></td>
                    <td><a href="../uploads/<?= $row['studentCard'] ?>" target="_blank">View Card</a></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="membershipID" value="<?= $row['membershipID'] ?>">
                            <button type="submit" name="action" value="approve" class="approve">✅ Approve</button>
                            <button type="submit" name="action" value="reject" class="reject">❌ Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending applications found.</p>
    <?php endif; ?>
</body>
</html>

