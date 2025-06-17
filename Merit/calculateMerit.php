<?php
session_start();
$studentID = $_SESSION['userID'] ?? '';

if (!$studentID) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$con = new mysqli("localhost", "root", "", "mypetakom_db");

if ($con->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed"]));
}

// 🧠 Semester & Academic Year
function getSemesterAndAcademicYear($studentId) {
    $intakeYearSuffix = substr($studentId, 2, 2); // '22' from 'CB22001'
    $intakeYear = 2000 + (int)$intakeYearSuffix;

    $now = new DateTime();
    $start = new DateTime("$intakeYear-02-01"); // Assume intake Feb
    $months = ($now->diff($start)->y * 12) + $now->diff($start)->m;
    $semester = min(floor($months / 6) + 1, 8);

    $currentYear = (int)date("Y");
    $currentMonth = (int)date("m");
    $academicYear = $currentMonth >= 9 
        ? "$currentYear/$currentYear" 
        : ($currentYear - 1) . "/$currentYear";

    return [$semester, $academicYear];
}

[$semester, $academicYear] = getSemesterAndAcademicYear($studentID);

// 🎯 Committee Merit
$committeeQuery = "
    SELECT SUM(m.score) AS totalCommitteeMerit
    FROM committee c
    JOIN event e ON c.eventID = e.eventID
    JOIN meritscore m
        ON e.eventLevel = m.event_level
        AND m.commitRole = (
            CASE
                WHEN c.role IN ('Chairperson', 'Secretary', 'Treasurer') THEN 'Main Committee'
                WHEN c.role = 'Member' THEN 'Committee'
                ELSE NULL
            END
        )
    WHERE c.studentID = ?
";
$stmt1 = $con->prepare($committeeQuery);
$stmt1->bind_param("s", $studentID);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = $result1->fetch_assoc();
$committeeMerit = (int)($row1['totalCommitteeMerit'] ?? 0);
$stmt1->close();

// 🧾 Participant Merit
$participantQuery = "
    SELECT SUM(m.score) AS totalParticipantMerit
    FROM registration r
    JOIN event e ON r.eventID = e.eventID
    JOIN meritscore m 
        ON e.eventLevel = m.event_level
        AND m.commitRole = 'Participant'
    WHERE r.studentID = ?
";
$stmt2 = $con->prepare($participantQuery);
$stmt2->bind_param("s", $studentID);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();
$participantMerit = (int)($row2['totalParticipantMerit'] ?? 0);
$stmt2->close();

$totalMerit = $committeeMerit + $participantMerit;

// 🚨 Always create a NEW merit record per semester
$meritID = uniqid("MRT");

// Check if same semester+year exists — if yes, delete old record
$check = $con->prepare("SELECT meritID FROM merit WHERE studentID = ? AND semester = ? AND academicYear = ?");
$check->bind_param("sis", $studentID, $semester, $academicYear);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $old = $res->fetch_assoc();
    $delete = $con->prepare("DELETE FROM merit WHERE meritID = ?");
    $delete->bind_param("s", $old['meritID']);
    $delete->execute();
    $delete->close();
}

$insert = $con->prepare("INSERT INTO merit (meritID, totalMerit, eventID, studentID, semester, academicYear) VALUES (?, ?, NULL, ?, ?, ?)");
$insert->bind_param("sisis", $meritID, $totalMerit, $studentID, $semester, $academicYear);
$insert->execute();
$insert->close();

$con->close();

// ✅ Final Response
echo json_encode([
    "success" => true,
    "studentID" => $studentID,
    "semester" => $semester,
    "academicYear" => $academicYear,
    "committeeMerit" => $committeeMerit,
    "participantMerit" => $participantMerit,
    "totalMerit" => $totalMerit
]);
?>



