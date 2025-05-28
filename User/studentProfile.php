<?php
session_start();

require_once '../DB_mypetakom/db.php';


 if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'student') {
        header("Location: ../ManageLogin/login.php");
        exit;
    }

$studentID = $_SESSION['userID'];

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = $_POST['studentName'];
    $studentEmail = $_POST['studentEmail'];

    // If password is empty, do not update it
    if (!empty($_POST['studentPassword'])) {
        $studentPassword = password_hash($_POST['studentPassword'], PASSWORD_DEFAULT);

        $query = "UPDATE student SET studentName=?, studentEmail=?, studentPassword=? WHERE studentID=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $studentName, $studentEmail, $studentPassword, $studentID);
    } else {
        // Update without changing password
        $query = "UPDATE student SET studentName=?, studentEmail=? WHERE studentID=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $studentName, $studentEmail, $studentID);
    }

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}

// Fetch current profile data
$query = "SELECT * FROM student WHERE studentID=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <link rel="stylesheet" href="studentProfile.css"> <!-- Ensure this path is correct -->
    <link rel="stylesheet" href="../Home/studentHomePage.css">
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
                    <li><a href="../User/studentProfile.php">Profile</a></li>
                    <li><a href="../membership/applyMembership.php" class="active">Apply Membership</a></li>
                    <li><a href="../membership/viewMembership.php"  >View Membership</a></li>
                    <li><a href="../Attendance/event_register.php">Attendance Registration</a></li>
                    <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                    <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                    <li><a href="../ManageLogin/Logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
    <div class="profile-container">
        <h2>My Profile</h2>



        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <label>Name:</label>
            <input type="text" name="studentName" value="<?= htmlspecialchars($row['studentName']) ?>" required><br>

            <label>Email:</label>
            <input type="email" name="studentEmail" value="<?= htmlspecialchars($row['studentEmail']) ?>" required><br>

            <label>Password (leave blank to keep current):</label>
            <input type="password" name="studentPassword"><br>

            <button type="submit">Update Profile</button>
        </form>

        <form method="POST" action="deleteStudent.php" onsubmit="return confirm('Are you sure you want to delete your account?');">
            <input type="hidden" name="studentID" value="<?= $studentID ?>">
            <button type="submit" class="delete-btn">Delete Account</button>
        </form>
    </div>
</body>
</html>

