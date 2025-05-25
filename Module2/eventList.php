<?php
session_start();
require_once '../DB_mypetakom/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];
$successMsg = $errorMsg = "";

if (isset($_GET['delete'])) {
    $eventID = $_GET['delete'];

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

// Fetch events for this advisor
$stmt = $conn->prepare("SELECT eventID, eventName, eventDate, venue, status FROM event WHERE advisorID = ? ORDER BY eventDate DESC");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $advisorID);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Event List - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventRegistration.css" />
    <style>
        body {
            background-color: #1a001f;
            color: #f0d9ff;
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
        }
        /* .container {
            display: flex;
            min-height: 100vh;
        } */
        /* main.main-content {
            flex: 1;
            padding: 40px;
        } */
        h2 {
            margin-bottom: 30px;
        }
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
        th {
            background-color: #7209b7;
        }
        tr:nth-child(even) {
            background-color: #4a177a;
        }
        a {
            color: #f9c74f;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .no-events {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #f57c7c;
        }
    </style>
</head>
<body>
<div class="container">
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <h2>📅 My Events</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <!-- <th>Event ID</th> -->
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Merit Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <!-- <td><?php echo htmlspecialchars($event['eventID']); ?></td> -->
                        <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                        <td><?php echo date('d M Y', strtotime($event['eventDate'])); ?></td>
                        <td><?php echo htmlspecialchars($event['venue']); ?></td>
                        <td><?php echo htmlspecialchars($event['status']); ?></td>
                         <td>
                                <?php
                                    // Fetch merit application status for the current event
                                    $eventID = $event['eventID'];
                                    $meritQuery = $conn->prepare("SELECT approvalStatus FROM meritapplication WHERE eventID = ?");
                                    $meritQuery->bind_param("s", $eventID);
                                    $meritQuery->execute();
                                    $meritResult = $meritQuery->get_result();

                                    if ($meritResult->num_rows > 0) {
                                        $meritRow = $meritResult->fetch_assoc();
                                        echo htmlspecialchars($meritRow['approvalStatus']);
                                    } else {
                                        echo "Not Applied";
                                    }

                                    $meritQuery->close();
                                ?>
                        </td>
                        <td>
                            <a href="viewEvent.php?id=<?php echo urlencode($event['eventID']); ?>">View</a> |
                            <a href="editEvent.php?id=<?php echo urlencode($event['eventID']); ?>">Edit</a> |
                            <a href="?delete=<?php echo $row['eventID']; ?>" class="btn-delete" onclick="return confirm('Delete this event?');">Delete</a>
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
