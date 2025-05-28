<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['staffRole'] !== 'Petakom Coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}



$staffID = $_SESSION['userID'];

// Update admin profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAdmin'])) {
    $staffName = $_POST['staffName'];
    $staffEmail = $_POST['staffEmail'];
    $staffPassword = password_hash($_POST['staffPassword'], PASSWORD_DEFAULT);

    $query = "UPDATE staff SET staffName=?, staffEmail=?, staffPassword=? WHERE staffID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $staffName, $staffEmail, $staffPassword, $staffID);
    $stmt->execute();
    $stmt->close();

    // Optional: show success message or redirect
    header("Location: adminProfile.php?success=1");
    exit();
}

// Fetch admin profile
$stmt = $conn->prepare("SELECT * FROM staff WHERE staffID=?");
$stmt->bind_param("s", $staffID);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch all students
$students = $conn->query("SELECT * FROM student");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="adminProfile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="profile-container">
        <h2>My Profile (Admin)</h2>

        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">Profile updated successfully!</p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="updateAdmin" value="1">
            
            <label>Name:</label>
            <input type="text" name="staffName" value="<?= htmlspecialchars($admin['staffName']) ?>" required><br>

            <label>Email:</label>
            <input type="email" name="staffEmail" value="<?= htmlspecialchars($admin['staffEmail']) ?>" required><br>

            <label>New Password:</label>
            <input type="password" name="staffPassword" required><br>

            <button type="submit">Update Profile</button>
        </form>

        <hr>
        <h2>Manage Students</h2>
        <table border="1">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Action</th>
            </tr>
            <?php while ($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['studentID']) ?></td>
                    <td><?= htmlspecialchars($row['studentName']) ?></td>
                    <td><?= htmlspecialchars($row['studentEmail']) ?></td>
                    <td>
                        <a href="editStudent.php?id=<?= urlencode($row['studentID']) ?>">Edit</a> | 
                        <a href="deleteStudent.php?id=<?= urlencode($row['studentID']) ?>" onclick="return confirm('Delete this student?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
