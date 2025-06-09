<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || strtolower($_SESSION['staffRole']) !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: manageProfile.php");
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];

if (!in_array($type, ['student', 'advisor', 'staff'])) {
    header("Location: manageProfile.php");
    exit();
}

if ($type === 'student') {
    $table = 'student';
    $idCol = 'studentID';
    $nameCol = 'studentName';
    $emailCol = 'studentEmail';
    $passwordCol = 'studentPassword';
} else {
    $table = 'staff';
    $idCol = 'staffID';
    $nameCol = 'staffName';
    $emailCol = 'staffEmail';
    $passwordCol = 'staffPassword';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        $error = "Name and Email cannot be empty.";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE $table SET $nameCol = ?, $emailCol = ?, $passwordCol = ? WHERE $idCol = ?");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $id);
        } else {
            $stmt = $conn->prepare("UPDATE $table SET $nameCol = ?, $emailCol = ? WHERE $idCol = ?");
            $stmt->bind_param("sss", $name, $email, $id);
        }

        if ($stmt->execute()) {
            header("Location: manageProfile.php?success=Profile updated successfully");
            exit();
        } else {
            $error = "Failed to update profile: " . $stmt->error;
        }
    }
}

$stmt = $conn->prepare("SELECT $nameCol, $emailCol FROM $table WHERE $idCol = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No user found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit <?= htmlspecialchars(ucfirst($type)) ?> Profile</title>
    <link rel="stylesheet" href="../User/profile.css">
    <link rel="stylesheet" href="../Home/adminHomePage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
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
                <li><a href="/MyPetakom/User/manageProfile.php" class="active">Profile</a></li>
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                <li><a href="#">Attendance Tracking</a></li>
                <li><a href="#">Merit Applications</a></li>
                <li>
                    <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                        <button type="submit" name="logout" class="sidebar-button">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="main-content">
        <h2>✏️ Edit <?= htmlspecialchars(ucfirst($type)) ?> Profile</h2>

        <?php if (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="profile-form" style="max-width:600px;">
            <div class="form-group" style="grid-column: span 2;">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user[$nameCol]) ?>" required />
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user[$emailCol]) ?>" required />
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Password: <small>(Leave blank to keep current password)</small></label>
                <input type="password" name="password" placeholder="Enter new password" />
            </div>
            <div class="form-actions">
                <button type="submit" class="edit-btn">Update Profile</button>
            </div>
        </form>

        <p style="margin-top: 1rem;"><a href="manageProfile.php" style="color: #4FC3F7;">← Back to Manage Profiles</a></p>
    </div>
</body>
</html>
