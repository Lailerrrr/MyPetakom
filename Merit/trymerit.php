<?php
include 'db_connection.php';
include 'calculateMerit.php'; // if you separate the function

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = $_POST['event_name'];
    $eventLevel = $_POST['event_level'];
    $role = $_POST['role'];
    $studentId = $_POST['student_id']; // assuming student_id comes from form or session

    $merit = calculateMerit($eventLevel, $role);

    // Store to DB
    $sql = "INSERT INTO merit_claims (student_id, event_name, event_level, role, merit_score)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $studentId, $eventName, $eventLevel, $role, $merit);

    if ($stmt->execute()) {
        echo "Merit claim submitted! Merit calculated: $merit points";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
