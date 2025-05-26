<?php
session_start();

// Connect to database
$con = mysqli_connect("localhost", "root", "", "mypetakom_db");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$studentID = $_POST['studentID'];
$eventID = $_POST['eventID'];
$commitRole = $_POST['commitRole'];
$semester = $_POST['semester'];
$academicYear = $_POST['academicYear'];

// Step 1: Get event level from event table
$eventQuery = mysqli_query($con, "SELECT eventLevel FROM event WHERE eventID = '$eventID'");
if (mysqli_num_rows($eventQuery) === 0) {
    die("Event not found.");
}
$eventRow = mysqli_fetch_assoc($eventQuery);
$eventLevel = $eventRow['eventLevel'];

// Step 2: Get score from merit_score table
$scoreQuery = mysqli_query($con, "SELECT score FROM merit_score WHERE event_level = '$eventLevel' AND commitRole = '$commitRole'");
if (mysqli_num_rows($scoreQuery) === 0) {
    die("Score not found for role and event level.");
}
$scoreRow = mysqli_fetch_assoc($scoreQuery);
$totalMerit = $scoreRow['score'];

// Step 3: Generate meritID (e.g., M0001, M0002)
$latestIDQuery = mysqli_query($con, "SELECT meritID FROM merit ORDER BY meritID DESC LIMIT 1");
if (mysqli_num_rows($latestIDQuery) > 0) {
    $lastID = mysqli_fetch_assoc($latestIDQuery)['meritID'];
    $num = intval(substr($lastID, 1)) + 1;
    $newMeritID = 'M' . str_pad($num, 4, '0', STR_PAD_LEFT);
} else {
    $newMeritID = 'M0001';
}

// Step 4: Insert into merit table
$insert = mysqli_query($con, "INSERT INTO merit (meritID, semester, academicYear, totalMerit, eventID, studentID)
    VALUES ('$newMeritID', '$semester', '$academicYear', $totalMerit, '$eventID', '$studentID')");

if ($insert) {
    echo "<script>
        alert('Merit successfully recorded!');
        window.location.href = 'studentDashboard.php'; // or your actual dashboard file
    </script>";
} else {
    echo "Failed to insert merit: " . mysqli_error($con);
}

mysqli_close($con);
?>
