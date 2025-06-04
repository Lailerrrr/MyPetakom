<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$studentID = $_SESSION['userID'];

if (!isset($_GET['slotID'])) {
    echo "Error: Slot ID not specified.";
    exit();
}

$slotID = $_GET['slotID'];
echo "DEBUG: Slot ID received = " . htmlspecialchars($slotID) . "<br>";

// Check if slot exists
$sql_slot = "SELECT s.slotName, s.attendanceDate, s.slotTime, e.eventName 
             FROM AttendanceSlot s 
             JOIN event e ON s.eventID = e.eventID 
             WHERE s.slotID = ?";
$stmt_slot = $conn->prepare($sql_slot);
$stmt_slot->bind_param("s", $slotID);
$stmt_slot->execute();
$result_slot = $stmt_slot->get_result();

if ($result_slot->num_rows === 0) {
    echo "Error: Attendance slot not found.";
    exit();
}

$slotData = $result_slot->fetch_assoc();
$stmt_slot->close();

// Check if student already registered attendance
$sql_check = "SELECT * FROM attendance WHERE slotID = ? AND studentID = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $slotID, $studentID);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $message = "✅ You have already registered attendance for this event.";
} else {
    // Register attendance
    $sql_insert = "INSERT INTO attendance (slotID, studentID) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $slotID, $studentID);

    if ($stmt_insert->execute()) {
        $message = "🎉 Attendance successfully registered!";
    } else {
        $message = "❌ Failed to register attendance. Please try again.";
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Registration</title>
    <link rel="stylesheet" href="../Attendance/advisor_attendance_slot.css"> 
</head>
<body>
    <div class="container">
        <h1>📍 Event Attendance</h1>
        <p><strong>Event:</strong> <?php echo htmlspecialchars($slotData['eventName']); ?></p>
        <p><strong>Slot:</strong> <?php echo htmlspecialchars($slotData['slotName']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($slotData['attendanceDate']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($slotData['slotTime']); ?></p>

        <div class="message-box">
            <p><?php echo $message; ?></p>
        </div>

        <a href="../Home/studentHomepage.php">🔙 Back to Dashboard</a>
    </div>
</body>
</html>
