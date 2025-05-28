<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$eventID = $_GET['id'] ?? null;

if (!$eventID) {
    echo "Event ID missing.";
    exit();
}

$message = "";

// === EVENT UPDATE ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['updateEvent'])) {
    $eventName = $_POST['eventName'];
    $eventDescription = $_POST['eventDescription'];
    $eventDate = $_POST['eventDate'];
    $venue = $_POST['venue'];
    $approvalDate = $_POST['approvalDate'];
    $status = $_POST['status'];
    $eventLevel = $_POST['eventLevel'];

    $approvalLetter = null;
    if (isset($_FILES['approvalLetter']) && $_FILES['approvalLetter']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["approvalLetter"]["name"]));
        $targetFilePath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES["approvalLetter"]["tmp_name"], $targetFilePath)) {
            $approvalLetter = $targetFilePath;
        } else {
            $message = "❌ Failed to upload approval letter.";
        }
    }

    if ($approvalLetter) {
        $update = $conn->prepare("UPDATE event SET eventName=?, eventDescription=?, eventDate=?, venue=?, approvalLetter=?, approvalDate=?, status=?, eventLevel=? WHERE eventID=? AND staffID=?");
        $update->bind_param("ssssssssss", $eventName, $eventDescription, $eventDate, $venue, $approvalLetter, $approvalDate, $status, $eventLevel, $eventID, $staffID);
    } else {
        $update = $conn->prepare("UPDATE event SET eventName=?, eventDescription=?, eventDate=?, venue=?, approvalDate=?, status=?, eventLevel=? WHERE eventID=? AND staffID=?");
        $update->bind_param("sssssssss", $eventName, $eventDescription, $eventDate, $venue, $approvalDate, $status, $eventLevel, $eventID, $staffID);
    }

    $message = $update->execute() ? "✅ Event updated successfully." : "❌ Failed to update event: " . $update->error;
}

// === ADD COMMITTEE ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['addCommittee'])) {
    $studentID = $_POST['studentID'] ?? null;
    $role = $_POST['role'] ?? null;

    if ($studentID && $role) {
        $check = $conn->prepare("SELECT * FROM committee WHERE eventID = ? AND studentID = ?");
        $check->bind_param("ss", $eventID, $studentID);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            $committeeID = uniqid("CMT");
            $insert = $conn->prepare("INSERT INTO committee (committeeID, eventID, studentID, role) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $committeeID, $eventID, $studentID, $role);
            $message = $insert->execute() ? "✅ Committee member added." : "❌ Failed to add committee member: " . $insert->error;
            $insert->close();
        } else {
            $message = "❌ Student already assigned to this event committee.";
        }
        $check->close();
    } else {
        $message = "❌ Please select student and role to add.";
    }
}

// === REMOVE COMMITTEE ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['removeCommitteeForm'])) {
    $removeID = $_POST['removeCommitteeID'] ?? null;
    if ($removeID) {
        $del = $conn->prepare("DELETE FROM committee WHERE committeeID = ? AND eventID = ?");
        $del->bind_param("ss", $removeID, $eventID);
        $message = $del->execute() ? "✅ Committee member removed." : "❌ Failed to remove committee member.";
        $del->close();
    } else {
        $message = "❌ Please select a committee member to remove.";
    }
}

// === FETCH EVENT & COMMITTEE DATA ===
$stmt = $conn->prepare("SELECT * FROM event WHERE eventID = ? AND staffID = ?");
$stmt->bind_param("ss", $eventID, $staffID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Event not found or access denied.";
    exit();
}
$event = $result->fetch_assoc();

$committeeMembers = $conn->prepare("SELECT c.committeeID, s.studentID, s.studentName, c.role 
                                    FROM committee c JOIN student s ON c.studentID = s.studentID 
                                    WHERE c.eventID = ?");
$committeeMembers->bind_param("s", $eventID);
$committeeMembers->execute();
$committeeResult = $committeeMembers->get_result();
$committeeOptions = [];
while ($row = $committeeResult->fetch_assoc()) {
    $committeeOptions[] = $row;
}
$students = $conn->query("SELECT studentID, studentName FROM student ORDER BY studentName ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event & Committee</title>
    <style>
        body {
            background: linear-gradient(to bottom right, #240046, #5a189a);
            color: #f0e7ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            background: #2c003e;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.1);
        }

        h2, h3 {
            color: #ffb3ff;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #e0aaff;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #c084fc;
            background-color: #ffffff0d;
            color: white;
        }

        input[type="file"] {
            border: none;
            background: transparent;
        }

        button {
            margin-top: 20px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            background-color: #9d4edd;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b185f6;
        }

        .action-link {
            display: inline-block;
            padding: 10px 18px;
            background-color: #6a00f4;
            color: #fff;
            border-radius: 8px;
            margin-top: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .action-link:hover {
            background-color: #9d4edd;
        }

        .message {
            margin: 15px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-left: 5px solid;
            border-radius: 8px;
        }

        .success {
            border-color: #4caf50;
            color: #4caf50;
        }

        .error {
            border-color: #f44336;
            color: #f44336;
        }

        .committee-section {
            margin-top: 40px;
            background-color: #39015f;
            padding: 20px;
            border-radius: 16px;
        }

        .committee-member {
            background: linear-gradient(to right, #7b2cbf, #9d4edd);
            padding: 12px 20px;
            margin-bottom: 12px;
            border-radius: 10px;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Event</h2>

    <?php if ($message): ?>
        <div class="message <?= str_contains($message, '❌') ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Event Name:</label>
        <input type="text" name="eventName" value="<?= htmlspecialchars($event['eventName']) ?>" required>

        <label>Description:</label>
        <textarea name="eventDescription" rows="4" required><?= htmlspecialchars($event['eventDescription']) ?></textarea>

        <label>Date:</label>
        <input type="date" name="eventDate" value="<?= htmlspecialchars($event['eventDate']) ?>" required>

        <label>Venue:</label>
        <input type="text" name="venue" value="<?= htmlspecialchars($event['venue']) ?>" required>

        <label>Approval Date:</label>
        <input type="date" name="approvalDate" value="<?= htmlspecialchars($event['approvalDate']) ?>">

        <label>Approval Letter:</label>
        <?php if (!empty($event['approvalLetter'])): ?>
            <a href="<?= htmlspecialchars($event['approvalLetter']) ?>" target="_blank" class="action-link">📄 View Current</a>
        <?php else: ?>
            <span>No letter uploaded</span>
        <?php endif; ?>
        <input type="file" name="approvalLetter" accept=".pdf">

        <label>Event Level:</label>
        <select name="eventLevel">
            <?php foreach (["International", "National", "State", "District", "UMPSA"] as $lvl): ?>
                <option value="<?= $lvl ?>" <?= $event['eventLevel'] === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
            <?php endforeach; ?>
        </select>

        <label>Status:</label>
        <select name="status">
            <?php foreach (["Active", "Postponed", "Cancelled"] as $stat): ?>
                <option value="<?= $stat ?>" <?= $event['status'] === $stat ? 'selected' : '' ?>><?= $stat ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="updateEvent">Update Event</button>
        <a href="eventManage.php" class="action-link">⬅️ Back to Manage Events</a>
    </form>

    <div class="committee-section">
        <h3>👥 Committee Members</h3>

        <?php foreach ($committeeOptions as $member): ?>
            <div class="committee-member">
                <?= htmlspecialchars($member['studentName']) ?> (<?= htmlspecialchars($member['studentID']) ?>) — <?= htmlspecialchars($member['role']) ?>
            </div>
        <?php endforeach; ?>

        <form method="post">
            <label>Remove Committee Member:</label>
            <select name="removeCommitteeID" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($committeeOptions as $member): ?>
                    <option value="<?= $member['committeeID'] ?>"><?= $member['studentName'] ?> (<?= $member['studentID'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="removeCommitteeForm">Remove Member</button>
        </form>

        <form method="post">
            <label>Add Committee Member:</label>
            <select name="studentID" required>
                <option value="">-- Select Student --</option>
                <?php while ($stu = $students->fetch_assoc()): ?>
                    <option value="<?= $stu['studentID'] ?>"><?= $stu['studentName'] ?> (<?= $stu['studentID'] ?>)</option>
                <?php endwhile; ?>
            </select>

            <label>Role:</label>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="Chairperson">Chairperson</option>
                <option value="Secretary">Secretary</option>
                <option value="Member">Member</option>
                <option value="Treasurer">Treasurer</option>
            </select>

            <button type="submit" name="addCommittee">Add Member</button>
        </form>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$committeeMembers->close();
$conn->close();
?>
