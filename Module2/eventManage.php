<?php
session_start();
require_once '../DB_mypetakom/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$successMsg = $errorMsg = "";

// Handle delete event
if (isset($_GET['delete'])) {
    $eventID = $_GET['delete'];

    $delete = $conn->prepare("DELETE FROM event WHERE eventID = ? AND staffID = ?");
    $delete->bind_param("ss", $eventID, $staffID);
    if ($delete->execute()) {
        $successMsg = "✅ Event deleted successfully.";
    } else {
        $errorMsg = "❌ Failed to delete event.";
    }
}

// Fetch events for this advisor
$stmt = $conn->prepare("SELECT eventID, eventName, eventDate, venue, status FROM event WHERE staffID = ? ORDER BY eventDate DESC");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Event Name - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventRegistration.css" />
    <style>
        body {
            background-color: #1a001f;
            color: #f0d9ff;
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
        }
        h2 { margin-bottom: 30px; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #3a0f5a;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
        }
        th { background-color: #7209b7; }
        tr:nth-child(even) { background-color: #4a177a; }
        a {
            color: #f9c74f;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover { text-decoration: underline; }
        .no-events {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #f57c7c;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .success { background-color: #4caf50; color: white; }
        .error { background-color: #f44336; color: white; }
    </style>
</head>
<body>
<div class="container">
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <h2>Manage Event</h2>

        <?php if ($successMsg): ?>
            <div class="message success"><?php echo $successMsg; ?></div>
        <?php elseif ($errorMsg): ?>
            <div class="message error"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Merit Status</th>
                        <th>Committee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                        <td><?php echo date('d M Y', strtotime($event['eventDate'])); ?></td>
                        <td><?php echo htmlspecialchars($event['venue']); ?></td>
                        <td><?php echo htmlspecialchars($event['status']); ?></td>
                        <td>
                            <?php
                                $eventID = $event['eventID'];
                                $meritQuery = $conn->prepare("SELECT approvalStatus FROM meritapplication WHERE eventID = ?");
                                $meritQuery->bind_param("s", $eventID);
                                $meritQuery->execute();
                                $meritResult = $meritQuery->get_result();

                                echo ($meritResult->num_rows > 0)
                                    ? htmlspecialchars($meritResult->fetch_assoc()['approvalStatus'])
                                    : "Not Applied";

                                $meritQuery->close();
                            ?>
                        </td>
                        <td>
                            <?php
                            $committeeQuery = $conn->prepare("
                                SELECT c.role, s.studentName 
                                FROM committee c 
                                LEFT JOIN student s ON c.studentID = s.studentID 
                                WHERE c.eventID = ?
                            ");
                            $committeeQuery->bind_param("s", $event['eventID']);
                            $committeeQuery->execute();
                            $committeeResult = $committeeQuery->get_result();

                            $committeeDisplay = [];
                            while ($committeeRow = $committeeResult->fetch_assoc()) {
                                $committeeDisplay[] = htmlspecialchars($committeeRow['studentName']) . " (" . htmlspecialchars($committeeRow['role']) . ")";
                            }
                            $committeeQuery->close();

                            echo count($committeeDisplay) ? implode(", ", $committeeDisplay) : "No committee assigned";
                            ?>
                        </td>
                        <td>
                            <a href="viewEvent.php?id=<?php echo urlencode($event['eventID']); ?>">View</a> |
                            <a href="editEvent.php?id=<?php echo urlencode($event['eventID']); ?>">Edit</a> |
                            <a href="?delete=<?php echo urlencode($event['eventID']); ?>"
                               onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-events">You have no events registered yet.</p>
        <?php endif; ?>
    </main>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
