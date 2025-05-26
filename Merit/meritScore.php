<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "mypetakom_db");

if (!$con){
    die("Connection failed: " . mysqli_connect_error());
}

$events = mysqli_query($con, "SELECT eventID, eventName FROM event");
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
    <title>Add Merit Record - MyPetakom</title>
    <link rel="stylesheet" href="meritScore.css" /> <!-- Your Pretty Savage CSS -->
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
            <li><a href="#"class="active">Merit</a></li>
            <li><a href="..Merit/ScanQR.php">Scan QR</a></li>
            <li><a href="../ManageLogin/Logout.php">Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>Submit Event Participation</h1><br>
        <p>Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($student_id); ?>)</p>
    </header>

    <form method="POST" action="processMeritRecord.php">
        <input type="hidden" name="studentID" value="<?php echo $studentID; ?>">
         <label for="eventID">Select Event:</label>
        <select name="eventID" required>
            <option value="">-- Select Event --</option>
            <?php while($row = mysqli_fetch_assoc($events)) { ?>
                <option value="<?php echo $row['eventID']; ?>">
                    <?php echo $row['eventName']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label for="commitRole">Your Role:</label>
        <select name="commitRole" required>
            <option value="">-- Select Role --</option>
            <option value="Main Committee">Main Committee</option>
            <option value="Committee">Committee</option>
            <option value="Participant">Participant</option>
        </select><br><br>

        <label for="semester">Semester:</label>
        <input type="text" name="semester" required><br><br>

        <label for="academicYear">Academic Year:</label>
        <input type="text" name="academicYear" required><br><br>

        <button type="submit">Submit</button>
    </form>

    <form action="processMeritRecord.php" method="post">
    <button type="submit" class="btn btn-primary">Calculate My Merit</button>
</form>


</body>
</html>
        