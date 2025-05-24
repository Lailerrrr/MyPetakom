<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];
$successMsg = $errorMsg = "";

// Helper function to get committee members for an event
function getCommitteeMembers($conn, $eventID) {
    $stmt = $conn->prepare("
        SELECT s.studentID, s.studentName
        FROM committee c
        JOIN student s ON c.studentID = s.studentID
        WHERE c.eventID = ?
    ");
    $stmt->bind_param("s", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    $members = [];

    while ($row = $result->fetch_assoc()) {
        $members[$row['studentID']] = $row['studentName']; // associate ID with name
    }
    
    if (empty($members)) {
        echo "<!-- No committee members found for event $eventID -->";
    }
    
    return $members;
}




// Handle delete action
if (isset($_GET['delete'])) {
    $eventID = $_GET['delete'];

    $getFile = $conn->prepare("SELECT approvalLetter FROM event WHERE eventID = ? AND advisorID = ?");
    $getFile->bind_param("ss", $eventID, $advisorID);
    $getFile->execute();
    $result = $getFile->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!empty($row['approvalLetter']) && file_exists($row['approvalLetter'])) {
            unlink($row['approvalLetter']);
        }

        // Delete related committee members
        $delCommittee = $conn->prepare("DELETE FROM committee WHERE eventID = ?");
        $delCommittee->bind_param("s", $eventID);
        $delCommittee->execute();

        $delete = $conn->prepare("DELETE FROM event WHERE eventID = ? AND advisorID = ?");
        $delete->bind_param("ss", $eventID, $advisorID);
        if ($delete->execute()) {
            $successMsg = "Event deleted successfully.";
        } else {
            $errorMsg = "Failed to delete event.";
        }
    } else {
        $errorMsg = "Event not found or access denied.";
    }
}

// Handle update action
if (isset($_POST['updateEvent'])) {
    $eventID = $_POST['eventID'];
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate'];
    $venue = $_POST['venue'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE event SET eventName = ?, eventDate = ?, venue = ?, status = ? WHERE eventID = ? AND advisorID = ?");
    $stmt->bind_param("ssssss", $eventName, $eventDate, $venue, $status, $eventID, $advisorID);

    if ($stmt->execute()) {
        // Update committee members
        if (isset($_POST['committeeMembers'])) {
            $committeeMembers = $_POST['committeeMembers']; // array of studentIDs

            // Delete all old committee members for this event first
            $delStmt = $conn->prepare("DELETE FROM committee WHERE eventID = ?");
            $delStmt->bind_param("s", $eventID);
            $delStmt->execute();

            // Insert new committee members
            $insStmt = $conn->prepare("INSERT INTO committee (eventID, studentID) VALUES (?, ?)");
            foreach ($committeeMembers as $studentID) {
                $insStmt->bind_param("ss", $eventID, $studentID);
                $insStmt->execute();
            }
        } else {
            // If no committee selected, delete all committee members for event
            $delStmt = $conn->prepare("DELETE FROM committee WHERE eventID = ?");
            $delStmt->bind_param("s", $eventID);
            $delStmt->execute();
        }

        $successMsg = "Event updated successfully.";
    } else {
        $errorMsg = "Failed to update event.";
    }
}

// Fetch advisor's events
$events = $conn->query("SELECT * FROM event WHERE advisorID = '$advisorID'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Events - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventRegistration.css" />
    <style>
        table {
            width: 100%;
            margin-top: 30px;
            background-color: #fff;
            color: #333;
            border-radius: 10px;
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
            border-radius: 10px;
        }

        th {
            background-color: #6c3483;
            color: white;
        }
        td {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #c267e4;
            padding: 10px;
        }

        .btn-delete, .btn-edit, .btn-view {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-edit, .btn-view{
            background-color: #3498db;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        select[multiple] {
            height: auto;
            min-height: 80px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include '../sidebar.php'; ?>

        <main class="main-content">
            <h2>📁 Manage Events</h2>

            <?php if ($successMsg): ?>
                <p style="color: green;"><?php echo htmlspecialchars($successMsg); ?></p>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <p style="color: red;"><?php echo htmlspecialchars($errorMsg); ?></p>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Approval Letter</th>
                        <th>Committee Members</th> <!-- Added committee header -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($events->num_rows > 0): ?>
                        <?php while ($row = $events->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="eventID" value="<?php echo $row['eventID']; ?>" />
                                    <td><input type="text" name="eventName" value="<?php echo htmlspecialchars($row['eventName']); ?>" required /></td>
                                    <td><input type="date" name="eventDate" value="<?php echo $row['eventDate']; ?>" required /></td>
                                    <td><input type="text" name="venue" value="<?php echo htmlspecialchars($row['venue']); ?>" required /></td>
                                    <td>
                                        <select name="status" required>
                                            <option value="Active" <?php if ($row['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                            <option value="Postponed" <?php if ($row['status'] == 'Postponed') echo 'selected'; ?>>Postponed</option>
                                            <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['approvalLetter'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['approvalLetter']); ?>" class="btn-view" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                        <td>
                                            <?php 
                                            // Get selected committee members for this event
                                            $selectedMembers = getCommitteeMembers($conn, $row['eventID']); 
                                            ?>
                                            <select name="committeeMembers[]" multiple>
                                                <?php foreach ($students as $student): ?>
                                                    <option value="<?php echo htmlspecialchars($student['studentID']); ?>"
                                                        <?php echo array_key_exists($student['studentID'], $selectedMembers) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($student['studentName']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    <td>
                                        <button type="submit" name="updateEvent" class="btn-edit">Save</button>
                                        <a href="?delete=<?php echo $row['eventID']; ?>" class="btn-delete" onclick="return confirm('Delete this event?');">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No events found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
