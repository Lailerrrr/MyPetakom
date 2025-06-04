<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_GET['slotID'])) {
    die("User  not logged in.");
}

$slotID = $_GET['slotID'];
$error = '';
$success = '';

// Fetch attendance slot and get eventID
$stmt = $conn->prepare("SELECT eventID, slotName, attendanceDate, slotTime FROM AttendanceSlot WHERE slotID = ?");
$stmt->bind_param("s", $slotID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid Slot ID.");
}

$slot = $result->fetch_assoc();
$stmt->close();

// Fetch venue from event table
$stmt = $conn->prepare("SELECT venue FROM event WHERE eventID = ?");
$stmt->bind_param("s", $slot['eventID']);
$stmt->execute();
$venueResult = $stmt->get_result();

if ($venueResult->num_rows === 0) {
    die("Invalid Event ID.");
}

$venue = $venueResult->fetch_assoc()['venue'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = trim($_POST['studentID']);
    $password = trim($_POST['password']);
    $location = $venue; // Set location to the venue

    // Verify student credentials
    $stmt = $conn->prepare("SELECT studentPassword FROM student WHERE studentID = ?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $error = "Invalid Student ID.";
    } else {
        $row = $res->fetch_assoc();
        $hashedPassword = $row['studentPassword'];

        if (password_verify($password, $hashedPassword)) {
            // Verify if student is assigned to event as committee member
            $stmt = $conn->prepare("SELECT * FROM committee WHERE eventID = ? AND studentID = ?");
            $stmt->bind_param("ss", $slot['eventID'], $studentID);
            $stmt->execute();
            $commResult = $stmt->get_result();

            if ($commResult->num_rows === 0) {
                $error = "You are not assigned as committee member for this event. Attendance denied.";
            } else {
                // Check if attendance already recorded for this student and slot to prevent duplicates
                $stmt = $conn->prepare("SELECT * FROM attendance WHERE slotID = ? AND studentID = ?");
                $stmt->bind_param("ss", $slotID, $studentID);
                $stmt->execute();
                $attResult = $stmt->get_result();

                if ($attResult->num_rows > 0) {
                    $error = "Attendance already registered.";
                } else {
                    // Record attendance
                    $attendanceID = uniqid('att_');
                    $checkInTime = date('H:i:s');
                    $checkInDate = date('Y-m-d');

                    $stmt = $conn->prepare("INSERT INTO attendance (attendanceID, checkInTime, checkInDate, location, slotID, studentID) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $attendanceID, $checkInTime, $checkInDate, $location, $slotID, $studentID);
                    if ($stmt->execute()) {
                        $success = "Attendance recorded successfully!";
                    } else {
                        $error = "Failed to record attendance: " . $stmt->error;
                    }
                }
            }
        } else {
            $error = "Invalid password.";
        }
    }

    // Close the statement if it is still open
    if ($stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Attendance Registration - <?= htmlspecialchars($slot['slotName']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Attendance Registration: <?= htmlspecialchars($slot['slotName']); ?></h2>
    <p>Date: <?= htmlspecialchars($slot['attendanceDate']); ?>, Time: <?= htmlspecialchars($slot['slotTime']); ?></p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" onsubmit="return confirm('Confirm attendance check-in?');">
        <div class="mb-3">
            <label for="studentID" class="form-label">Student ID</label>
            <input type="text" id="studentID" name="studentID" class="form-control" required autocomplete="off" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="off" />
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location (Venue)</label>
            <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($venue); ?>" readonly required />
        </div>

        <button type="submit" class="btn btn-primary">Submit Attendance</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
