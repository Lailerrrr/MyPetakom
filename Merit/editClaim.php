
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("No claim ID provided.");
}

$claimID = $_GET['id'];

// Initialize a success message variable
$successMessage = "";

// Function to fetch claim from DB
function fetchClaim($conn, $claimID) {
    $stmt = $conn->prepare("SELECT * FROM meritClaim WHERE claimID = ?");
    $stmt->bind_param("s", $claimID);
    $stmt->execute();
    $result = $stmt->get_result();
    $claim = null;
    if ($result->num_rows > 0) {
        $claim = $result->fetch_assoc();
    }
    $stmt->close();
    return $claim;
}

// Fetch claim data
$claim = fetchClaim($conn, $claimID);

if (!$claim) {
    die("Claim not found.");
}

// Deny editing if claim is submitted
if ($claim['claimStatus'] === 'Submitted') {
    die("This claim is already submitted and cannot be edited.");
}

// Initialize form variables from claim data BEFORE the form
$eventID = $claim['eventID'];
$claimStatus = $claim['claimStatus'];
$claimLetter = $claim['claimLetter'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventID = $_POST['eventID'];
    $claimStatus = $_POST['claimStatus'] ?? $claimStatus;  // if claimStatus field is not in form, keep old

    // Handle file upload if new file provided
    if (isset($_FILES['claimLetter']) && $_FILES['claimLetter']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $tmpName = $_FILES['claimLetter']['tmp_name'];
        $originalName = basename($_FILES['claimLetter']['name']);
        // Sanitize and make unique filename (timestamp + original)
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "", $originalName);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($tmpName, $targetFile)) {
            $claimLetter = $filename;
        } else {
            echo "<script>alert('Failed to upload new letter.');</script>";
        }
    }

    // Update the claim in database
    $updateStmt = $conn->prepare("UPDATE meritClaim SET eventID = ?, claimLetter = ?, claimStatus = ? WHERE claimID = ?");
    $updateStmt->bind_param("ssss", $eventID, $claimLetter, $claimStatus, $claimID);

    if ($updateStmt->execute()) {
        $successMessage = "Claim updated successfully.";

        // Re-fetch updated claim to update variables and display fresh data
        $claim = fetchClaim($conn, $claimID);
        if ($claim) {
            $eventID = $claim['eventID'];
            $claimStatus = $claim['claimStatus'];
            $claimLetter = $claim['claimLetter'];
        }
    } else {
        echo "<script>alert('Failed to update claim.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Update Merit Claim - MyPetakom</title>
    <link rel="stylesheet" href="editClaim.css" /> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<h1>UPDATE MERIT CLAIM</h1>
<?php if (!empty($successMessage)): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

<p>Welcome, <strong><?= htmlspecialchars($name) ?></strong> (<?= htmlspecialchars($student_id) ?>)</p>
<form action="editClaim.php?id=<?= htmlspecialchars($claimID) ?>" method="POST" enctype="multipart/form-data">

    <label for="eventID">Event ID:</label><br>
    <input type="text" id="eventID" name="eventID" value="<?= htmlspecialchars($eventID) ?>" required >

    <label for="claimLetter">Claim Letter:</label><br />
    <input type="file" id="claimLetter" name="claimLetter" accept=".pdf" />
    <p>Current file: <?= htmlspecialchars($claimLetter) ?></p>

    <br>

    <input type="submit" value="Update Claim" />
</form>

<br>
<a href="MeritClaimStudent.php">Back to Merit Claims</a>

</body>
</html>

