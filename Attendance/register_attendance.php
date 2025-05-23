<?php
require_once '../DB_mypetakom/db.php';
session_start();

$eventID = isset($_GET['eventID']) ? intval($_GET['eventID']) : 0;
$slotID = isset($_GET['slotID']) ? intval($_GET['slotID']) : 0;
$successMsg = "";
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = $_POST['studentID'];
    $password = $_POST['password'];

    // Verify student credentials
    $stmt = $conn->prepare("SELECT * FROM students WHERE studentID = ? AND password = ?");
    $stmt->bind_param("is", $studentID, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        // Check if student already registered for this slot
        $checkStmt = $conn->prepare("SELECT * FROM event_attendance WHERE studentID = ? AND eventID = ? AND slotID = ?");
        $checkStmt->bind_param("iii", $studentID, $eventID, $slotID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Insert attendance
            $insertStmt = $conn->prepare("INSERT INTO event_attendance (studentID, eventID, slotID, timestamp) VALUES (?, ?, ?, NOW())");
            $insertStmt->bind_param("iii", $studentID, $eventID, $slotID);
            if ($insertStmt->execute()) {
                $successMsg = "✅ Attendance recorded successfully!";
            } else {
                $errorMsg = "⚠️ Failed to record attendance. Please try again.";
            }
        } else {
            $errorMsg = "⚠️ You have already registered attendance for this event slot.";
        }
    } else {
        $errorMsg = "❌ Invalid student ID or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .attendance-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background: #4CAF50;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }

        .msg {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="attendance-box">
    <h2>📌 Register Attendance</h2>
    <p>Event ID: <?= htmlspecialchars($eventID) ?><br>Slot ID: <?= htmlspecialchars($slotID) ?></p>

    <?php if ($successMsg): ?>
        <div class="msg success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
        <div class="msg error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Student ID</label>
        <input type="text" name="studentID" required placeholder="Enter Student ID">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter Password">
        <button type="submit"><a href="../ManageLogin/Logout.php">Logout</a>Confirm Attendance</button>
    </form>
</div>

</body>
</html>
