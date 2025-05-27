<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$feedback = "";

// Delete logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membershipID'])) {
    $membershipID = $_POST['membershipID'];

    $stmt = $conn->prepare("DELETE FROM membership WHERE membershipID = ?");
    $stmt->bind_param("s", $membershipID);

    if ($stmt->execute()) {
        $feedback = "✅ Membership deleted successfully.";
    } else {
        $feedback = "❌ Failed to delete membership.";
    }

    $stmt->close();
}

// Get all memberships
$sql = "SELECT m.membershipID, m.studentID, m.status, m.studentCard, s.studentName 
        FROM membership m
        JOIN student s ON m.studentID = s.studentID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Membership - MyPetakom</title>
    <link rel="stylesheet" href="../Home/adminHomePage.css">
    <link rel="stylesheet" href="deleteMembership.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
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
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../membership/deleteMembership.php" class="active">Delete Membership</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <h1>🗑 Delete Membership</h1>

        <?php if ($feedback): ?>
            <p class="delete-msg" style="color: <?= str_contains($feedback, '✅') ? 'lightgreen' : 'salmon'; ?>">
                <?= $feedback ?>
            </p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Membership ID</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Student Card</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['membershipID']); ?></td>
                        <td><?= htmlspecialchars($row['studentID']); ?></td>
                        <td><?= htmlspecialchars($row['studentName']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td><a href="<?= '../uploads/' . htmlspecialchars($row['studentCard']); ?>" target="_blank">View</a></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this membership?');">
                                <input type="hidden" name="membershipID" value="<?= $row['membershipID']; ?>">
                                <button class="delete-btn" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No membership applications found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
