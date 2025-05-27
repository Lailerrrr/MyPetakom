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

// Handle event update
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
        $filename = basename($_FILES["approvalLetter"]["name"]);
        $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $filename);
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

    if ($update->execute()) {
        $message = "✅ Event updated successfully.";
    } else {
        $message = "❌ Failed to update event: " . $update->error;
    }
}

// Handle committee add
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
            if ($insert->execute()) {
                $message = "✅ Committee member added.";
            } else {
                $message = "❌ Failed to add committee member: " . $insert->error;
            }
            $insert->close();
        } else {
            $message = "❌ Student already assigned to this event committee.";
        }
        $check->close();
    } else {
        $message = "❌ Please select student and role to add.";
    }
}

// Handle committee removal via form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['removeCommitteeForm'])) {
    $removeID = $_POST['removeCommitteeID'] ?? null;
    if ($removeID) {
        $del = $conn->prepare("DELETE FROM committee WHERE committeeID = ? AND eventID = ?");
        $del->bind_param("ss", $removeID, $eventID);
        if ($del->execute()) {
            $message = "✅ Committee member removed.";
        } else {
            $message = "❌ Failed to remove committee member.";
        }
        $del->close();
    } else {
        $message = "❌ Please select a committee member to remove.";
    }
}

// Fetch event data
$stmt = $conn->prepare("SELECT * FROM event WHERE eventID = ? AND staffID = ?");
$stmt->bind_param("ss", $eventID, $staffID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event not found or access denied.";
    exit();
}

$event = $result->fetch_assoc();

// Fetch committee members
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

// Fetch students
$students = $conn->query("SELECT studentID, studentName FROM student ORDER BY studentName ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event and Committee</title>
    <link rel="stylesheet" href="../Module2/eventRegistration.css">
    <style>
        .committee-section {
            margin-top: 40px;
            background: #220036;
            padding: 20px;
            border-radius: 16px;
        }
        .committee-list {
            margin-bottom: 20px;
        }
        .committee-member {
            background: linear-gradient(135deg, #3b0050, #620072);
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #f0d9ff;
        }
        .committee-member p {
            margin: 0;
        }
        .add-committee-form label, 
        .add-committee-form select, 
        .add-committee-form button {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            font-size: 1rem;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #d59de7;
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .add-committee-form button {
            background-color: #c061cb;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .add-committee-form button:hover {
            background-color: #d883e6;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Event</h2>
    <?php if ($message): ?>
        <p style="color: <?= strpos($message, '❌') === false ? '#69f069' : '#f57c7c' ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Event Name:</label>
        <input type="text" name="eventName" value="<?= htmlspecialchars($event['eventName']) ?>" required>

        <label>Event Description:</label>
        <textarea name="eventDescription" rows="4" required><?= htmlspecialchars($event['eventDescription']) ?></textarea>

        <label>Event Date:</label>
        <input type="date" name="eventDate" value="<?= htmlspecialchars($event['eventDate']) ?>" required>

        <label>Venue:</label>
        <input type="text" name="venue" value="<?= htmlspecialchars($event['venue']) ?>" required>

        <label>Approval Date:</label>
        <input type="date" name="approvalDate" value="<?= htmlspecialchars($event['approvalDate']) ?>">

        <label>Approval Letter:
            <?php if (!empty($event['approvalLetter'])): ?>
                <a href="<?= htmlspecialchars($event['approvalLetter']) ?>" target="_blank">View Current Letter</a>
            <?php else: ?>
                <span>No letter uploaded</span>
            <?php endif; ?>
        </label>
        <input type="file" name="approvalLetter" accept=".pdf">

        <label>Event Level:</label>
        <select name="eventLevel" required>
            <?php 
            $levels = ["International", "National", "State", "District", "UMPSA"];
            foreach ($levels as $level): ?>
                <option value="<?= $level ?>" <?= $event['eventLevel'] === $level ? 'selected' : '' ?>><?= $level ?></option>
            <?php endforeach; ?>
        </select>

        <label>Status:</label>
        <select name="status" required>
            <?php 
            $statuses = ["Active", "Postponed", "Cancelled"];
            foreach ($statuses as $stat): ?>
                <option value="<?= $stat ?>" <?= $event['status'] === $stat ? 'selected' : '' ?>><?= $stat ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="updateEvent">Update Event</button>
        <a href="eventList.php" style="margin-left: 15px;">Cancel</a>
    </form>

    <div class="committee-section">
        <h3>Committee Members</h3>

        <div class="committee-list">
            <?php if (!empty($committeeOptions)): ?>
                <?php foreach ($committeeOptions as $row): ?>
                    <div class="committee-member">
                        <p><strong><?= htmlspecialchars($row['studentName']) ?></strong> (ID: <?= htmlspecialchars($row['studentID']) ?>) — Role: <?= htmlspecialchars($row['role']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No committee members assigned.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($committeeOptions)): ?>
        <form method="post" class="add-committee-form">
            <label>Remove Committee Member:</label>
            <select name="removeCommitteeID" required>
                <option value="">-- Select Committee Member to Remove --</option>
                <?php foreach ($committeeOptions as $member): ?>
                    <option value="<?= htmlspecialchars($member['committeeID']) ?>">
                        <?= htmlspecialchars($member['studentName']) ?> (<?= htmlspecialchars($member['studentID']) ?>) - <?= htmlspecialchars($member['role']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="removeCommitteeForm">Remove Selected Member</button>
        </form>
        <?php endif; ?>

        <form method="post" class="add-committee-form">
            <label>Add Committee Member:</label>
            <select name="studentID" required>
                <option value="">-- Select Student --</option>
                <?php while ($stu = $students->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($stu['studentID']) ?>"><?= htmlspecialchars($stu['studentName']) ?> (<?= htmlspecialchars($stu['studentID']) ?>)</option>
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
