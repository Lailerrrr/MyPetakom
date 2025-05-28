<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Petakom Coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../User/adminProfile.php");
    exit();
}

$studentID = $_GET['id'];

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = $_POST['studentName'];
    $studentEmail = $_POST['studentEmail'];
    $studentPassword = password_hash($_POST['studentPassword'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE student SET studentName=?, studentEmail=?, studentPassword=? WHERE studentID=?");
    $stmt->bind_param("ssss", $studentName, $studentEmail, $studentPassword, $studentID);
    
    if ($stmt->execute()) {
        header("Location: ../User/adminProfile.php");
        exit();
    } else {
        $error = "Failed to update student.";
    }
}

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM student WHERE studentID=?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="adminProfile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="profile-container">
        <h2>Edit Student Profile</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="studentName" value="<?= htmlspecialchars($student['studentName']) ?>" required><br>

            <label>Email:</label>
            <input type="email" name="studentEmail" value="<?= htmlspecialchars($student['studentEmail']) ?>" required><br>

            <label>New Password:</label>
            <input type="password" name="studentPassword" required><br>

            <button type="submit">Update</button>
            <a href="../User/adminProfile.php">Cancel</a>
        </form>
    </div>
</body>
</html>
