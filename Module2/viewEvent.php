<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Event ID is missing.";
    exit();
}

$eventID = $_GET['id'];
$staffID = $_SESSION['userID'];

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM event WHERE eventID = ? AND staffID = ?");
$stmt->bind_param("ss", $eventID, $staffID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found or access denied.";
    exit();
}

$event = $result->fetch_assoc();

// Fetch merit status
$meritStatus = "Not Applied";
$meritStmt = $conn->prepare("SELECT approvalStatus FROM meritapplication WHERE eventID = ?");
$meritStmt->bind_param("s", $eventID);
$meritStmt->execute();
$meritResult = $meritStmt->get_result();
if ($meritResult->num_rows > 0) {
    $meritStatus = $meritResult->fetch_assoc()['approvalStatus'];
}
$meritStmt->close();

// Fetch committee members
$committee = [];
$committeeStmt = $conn->prepare("
    SELECT s.studentName, c.role 
    FROM committee c 
    JOIN student s ON c.studentID = s.studentID 
    WHERE c.eventID = ?
");
$committeeStmt->bind_param("s", $eventID);
$committeeStmt->execute();
$committeeResult = $committeeStmt->get_result();
while ($row = $committeeResult->fetch_assoc()) {
    $committee[] = $row;
}
$committeeStmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Event</title>
    <link rel="stylesheet" href="../Module2/viewEvent.css">
    <link rel="stylesheet" href="../Module2/eventRegistration.css">
    <style>
        .container {
            padding: 30px;
            background-color: #1a001f;
            color: #f0d9ff;
            font-family: Arial, sans-serif;
        }
        h2, h3 {
            color: #f9c74f;
        }
        p {
            font-size: 16px;
            margin: 5px 0;
        }
        .committee-member {
            background-color: #3a0f5a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #7209b7;
        }
        .committee-member p {
            margin: 3px 0;
        }
        a {
            color: #f0d9ff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📄 View Event Details</h2>

    <p><strong>Event ID:</strong> <?= htmlspecialchars($event['eventID']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($event['eventName']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['eventDescription'])) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($event['eventDate']) ?></p>
    <p><strong>Venue:</strong> <a href="<?= htmlspecialchars($event['venue']) ?>" target="_blank"><?= htmlspecialchars($event['venue']) ?></a></p>
    <p><strong>Approval Date:</strong> <?= htmlspecialchars($event['approvalDate']) ?></p>
    <p><strong>Event Level:</strong> <?= htmlspecialchars($event['eventLevel']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($event['status']) ?></p>
    <p><strong>Merit Status:</strong> <?= htmlspecialchars($meritStatus) ?></p>
    <p><strong>Approval Letter:</strong> 
        <?php if (!empty($event['approvalLetter'])): ?>
            <a href="<?= htmlspecialchars($event['approvalLetter']) ?>" target="_blank">📄 View PDF</a>
        <?php else: ?>
            Not uploaded
        <?php endif; ?>
    </p>

    <h3>👥 Committee Members</h3>
    <?php if (count($committee) > 0): ?>
        <?php foreach ($committee as $member): ?>
            <div class="committee-member">
                <p><strong>Student Name:</strong> <?= htmlspecialchars($member['studentName']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($member['role']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No committee members assigned yet.</p>
    <?php endif; ?>

    <br>
    <a href="eventList.php">⬅️ Back to Event List</a>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
