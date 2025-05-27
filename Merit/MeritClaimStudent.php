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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['StdID'] ?? '';
    $event_id = $_POST['EventID'] ?? '';
    $document = $_FILES['document']['name'] ?? '';

    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($document);

        if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
            $claimStatus = "Pending";
            $approvalDate = null;
            $approvalBy = null;

            $sql = "INSERT INTO meritclaim (claimID, claimStatus, claimLetter, approval_date, approval_by, eventID, studentID)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $claimID = uniqid("CLM"); // generate unique claim ID
            $stmt->bind_param("sssssss", $claimID, $claimStatus, $document, $approvalDate, $approvalBy, $event_id, $student_id);

            if ($stmt->execute()) {
                echo "<script>alert('Claim submitted successfully.');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "❌ Sorry, there was an error uploading your file.";
        }
    } else {
        echo "❌ No file uploaded or file error.";
    }

    $conn->close();
}

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
        <form action="" method="post" enctype="multipart/form-data">
    <label for="Stdid">Student ID:</label>
    <input type="text" id="Stdid" name="StdID" required><br><br>

    <label for="EID">Event ID:</label>
    <input type="text" id="EID" name="EventID" required><br><br>

    <label for="file-upload">Upload Official Letter (PDF): </label> 
    <input id="file-upload" type="file" name="document" accept=".pdf" required><br><br>

    <input type="submit" value="Submit"> 
</form>

 </div>
</main>


</body>
</html>
