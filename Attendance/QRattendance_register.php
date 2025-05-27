<?php
session_start();
require_once '../DB_mypetakom/db.php';
require_once '../phpqrcode/qrlib.php';
if (!isset($_GET['slotID'])) {
    die("Error: No slot ID provided.");
}

$slotID = $_GET['slotID'];
$slotData = null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $studentID = $_POST['studentID'];
    $password = $_POST['password'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Authenticate student
    $stmt = $conn->prepare("SELECT studentPassword FROM student WHERE studentID = ?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    if ($stmt->fetch()) {
        if (password_verify($password, $hashedPassword)) {
            // Check if slot exists
            $stmt->close();
            $stmt = $conn->prepare("SELECT s.slotName, s.attendanceDate, s.slotTime, e.eventName, e.latitude, e.longitude 
                                    FROM AttendanceSlot s 
                                    JOIN event e ON s.eventID = e.eventID 
                                    WHERE s.slotID = ?");
            $stmt->bind_param("s", $slotID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $slotData = $result->fetch_assoc();

                // Geolocation matching (within ~50m)
                $distance = haversineDistance($latitude, $longitude, $slotData['latitude'], $slotData['longitude']);
                if ($distance <= 0.05) {
                    // Check if already registered
                    $stmt = $conn->prepare("SELECT * FROM attendance WHERE slotID = ? AND studentID = ?");
                    $stmt->bind_param("ss", $slotID, $studentID);
                    $stmt->execute();
                    $check = $stmt->get_result();
                    if ($check->num_rows > 0) {
                        $success = "✅ You have already registered attendance for this slot.";
                    } else {
                        // Register attendance
                        $stmt = $conn->prepare("INSERT INTO attendance (slotID, studentID) VALUES (?, ?)");
                        $stmt->bind_param("ss", $slotID, $studentID);
                        if ($stmt->execute()) {
                            $success = "🎉 Attendance successfully recorded!";
                        } else {
                            $error = "❌ Failed to register attendance.";
                        }
                    }
                } else {
                    $error = "❌ Your current location does not match the event location.";
                }
            } else {
                $error = "❌ Attendance slot not found.";
            }
        } else {
            $error = "❌ Invalid Student ID or Password.";
        }
    } else {
        $error = "❌ Student not found.";
    }
    $stmt->close();
}

// Helper: Haversine formula to calculate distance in KM
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    $a = sin($dLat/2) * sin($dLat/2) +
         sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Attendance Registration</title>
    <script>
        // Capture geolocation on page load
        function captureLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    document.getElementById("latitude").value = position.coords.latitude;
                    document.getElementById("longitude").value = position.coords.longitude;
                }, () => {
                    alert("Geolocation permission denied.");
                });
            } else {
                alert("Geolocation not supported.");
            }
        }

        window.onload = captureLocation;
    </script>
</head>
<body>
    <h1>📍 Scan Attendance - MyPetakom</h1>

    <?php if (!empty($success)): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php elseif (!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <label>Student ID:</label>
        <input type="text" name="studentID" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="submit_attendance">Check In</button>
    </form>
    <?php endif; ?>

    <?php if ($slotData): ?>
        <div style="margin-top:20px;">
            <h3>📌 Event Details</h3>
            <p><strong>Event:</strong> <?= htmlspecialchars($slotData['eventName']) ?></p>
            <p><strong>Slot:</strong> <?= htmlspecialchars($slotData['slotName']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($slotData['attendanceDate']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($slotData['slotTime']) ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
