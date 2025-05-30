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

// 🎯 Committee merit calculation
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

// 🧾 Participant merit calculation
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

// 🔁 Check if merit exists for student
$check = $con->prepare("SELECT * FROM merit WHERE studentID = ?");
$check->bind_param("s", $studentID);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $update = $con->prepare("UPDATE merit SET totalMerit = ? WHERE studentID = ?");
    $update->bind_param("is", $totalMerit, $studentID);
    $update->execute();
    $update->close();
} else {
    $meritID = uniqid("MRT");
    $insert = $con->prepare("INSERT INTO merit (meritID, totalMerit, eventID, studentID) VALUES (?, ?, '', ?)");
    $insert->bind_param("sis", $meritID, $totalMerit, $studentID);
    $insert->execute();
    $insert->close();
}

$con->close();

// ✅ Final JSON Output
$response = [
    "success" => true,
    "studentID" => $studentID,
    "committeeMerit" => $committeeMerit,
    "participantMerit" => $participantMerit,
    "totalMerit" => $totalMerit
];

echo json_encode($response);
?>

