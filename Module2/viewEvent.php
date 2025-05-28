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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Event - <?= htmlspecialchars($event['eventName']) ?></title>
    <link rel="stylesheet" href="../sidebar.css" />
    <style>
        body {
            margin: 0;
            background-color: #1a001f;
            color: #f0d9ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 40px 30px;
            background-color: #2a0033;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(200, 100, 255, 0.2);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #f9c74f;
            border-bottom: 2px solid #f9c74f;
            padding-bottom: 10px;
        }

        h3 {
            margin-top: 40px;
            color: #a29bfe;
            font-size: 22px;
        }

        .event-detail {
            margin: 10px 0;
            padding: 12px;
            background-color: #3a0f5a;
            border-radius: 8px;
            border-left: 5px solid #7209b7;
        }

        .event-detail strong {
            display: inline-block;
            width: 160px;
        }

        .committee-member {
            background-color: #4e1a68;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #9d4edd;
            transition: transform 0.2s ease;
        }

        .committee-member:hover {
            transform: scale(1.02);
            background-color: #5e2a78;
        }

        a {
            color: #ffb3ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #8e44ad;
            color: white;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .back-link:hover {
            background-color: #a65fd2;
        }
        
    </style>
</head>
<body>
<div class="container">
    <h2>📄 <?= htmlspecialchars($event['eventName']) ?></h2>

    <div class="event-detail"><strong>Event ID:</strong> <?= htmlspecialchars($event['eventID']) ?></div>
    <div class="event-detail"><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['eventDescription'])) ?></div>
    <div class="event-detail"><strong>Date:</strong> <?= htmlspecialchars($event['eventDate']) ?></div>
    <div class="event-detail"><strong>Venue:</strong> <a href="<?= htmlspecialchars($event['venue']) ?>" target="_blank"><?= htmlspecialchars($event['venue']) ?></a></div>
    <div class="event-detail"><strong>Approval Date:</strong> <?= htmlspecialchars($event['approvalDate']) ?></div>
    <div class="event-detail"><strong>Event Level:</strong> <?= htmlspecialchars($event['eventLevel']) ?></div>
    <div class="event-detail"><strong>Status:</strong> <?= htmlspecialchars($event['status']) ?></div>
    <div class="event-detail"><strong>Merit Status:</strong> <?= htmlspecialchars($meritStatus) ?></div>
    <div class="event-detail"><strong>Approval Letter:</strong> 
        <?php if (!empty($event['approvalLetter'])): ?>
            <a href="<?= htmlspecialchars($event['approvalLetter']) ?>" target="_blank">📄 View PDF</a>
        <?php else: ?>
            Not uploaded
        <?php endif; ?>
    </div>

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

    <a href="eventManage.php" class="back-link">⬅️ Back to Event List</a>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
