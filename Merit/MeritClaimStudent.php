<?php
session_start();
require_once '../DB_mypetakom/db.php'; // Adjust path if needed

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Merit Claim - MyPetakom</title>
    <link rel="stylesheet" href="MeritClaimStudent.css" /> <!-- Your Pretty Savage CSS -->
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
                <li><a href="../Attendance/event_register.php">Attendance Registration</a></li>
                <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                <li><a href="../Merit/meritScore.php">Merit</a></li>
                <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
    </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>MERIT CLAIM</h1><br>
        <p>Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($student_id); ?>)</p>
        <p>Please fill out the form below to submit your merit claim.</p>
    </header>

    <div>
        <form action="" method="post"> <!-- Use POST method and action="" to submit to the same page -->
            <label for="Stdid">Student ID:</label>
            <input type="text" id="Stdid" name="StdID" required><br><br>

            <label for="EID">Event ID:</label>
            <input type="text" id="EID" name="EventID" required><br><br>

            <label for="role">Role:</label>
            <select id="role" name="Role">
                <option value="mainCommittee">Main Committee</option>
                <option value="committee">Committee</option>
                <option value="participant">Participant</option>
            </select><br><br>
            <form>
    <label for="file-upload" class="custom-file-upload">
      Upload Official Letter (PDF):
    </label>
    <input id="file-upload" type="file" accept=".pdf" onchange="updateFileName()" required>
    
  </form>

            <input type="submit" value="Submit"> <!-- Add a submit button -->
        </form>
    </div>
</main>


</body>
</html>
