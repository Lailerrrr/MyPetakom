<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];

// Fetch advisor details
$advisorQuery = $conn->prepare("SELECT staffName, staffEmail, staffPhone, staff_Department FROM staff WHERE staffID = ?");
$advisorQuery->bind_param("s", $staffID);
$advisorQuery->execute();
$advisorQuery->bind_result($name, $email, $phone, $department);
$advisorQuery->fetch();
$advisorQuery->close();

// Fetch advisor's events
$eventQuery = $conn->prepare("SELECT eventName, eventDate, status FROM event WHERE staffID = ? ORDER BY eventDate DESC");
$eventQuery->bind_param("s", $staffID);
$eventQuery->execute();
$eventResult = $eventQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advisor Profile - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="../Advisor/advisorProfile.css">
    <style>
        /* Minimal fallback styles if advisorProfile.css is missing */
        .profile-section {
            background: #3a0f5a;
            padding: 20px;
            border-radius: 8px;
            color: #f0d9ff;
            margin-bottom: 30px;
        }
        .profile-section h2 {
            margin-bottom: 15px;
            border-bottom: 2px solid #7209b7;
            padding-bottom: 5px;
        }
        .profile-details p {
            margin: 8px 0;
            font-size: 1.1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #3a0f5a;
            border-radius: 8px;
            overflow: hidden;
            color: #f0d9ff;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #7209b7;
        }
        tr:nth-child(even) {
            background-color: #4a177a;
        }
        caption {
            font-size: 1.4em;
            margin-bottom: 10px;
            font-weight: bold;
            color: #f9c74f;
        }
        .no-events {
            margin-top: 20px;
            font-style: italic;
            color: #f57c7c;
        }
    </style>
</head>
<body>
<div class="container">
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <section class="profile-section" aria-labelledby="profile-heading">
            <h2 id="profile-heading">👤 Advisor Profile</h2>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
            </div>
        </section>

        <section class="events-section" aria-labelledby="events-heading">
            <h2 id="events-heading">📅 Your Events</h2>

            <?php if ($eventResult->num_rows > 0): ?>
                <table>
                    <caption>Events you are managing</caption>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $eventResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['eventName']); ?></td>
                                <td><?php echo date('d M Y', strtotime($event['eventDate'])); ?></td>
                                <td><?php echo htmlspecialchars($event['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-events">You currently have no events assigned.</p>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>

<?php
$eventQuery->close();
$conn->close();
?>
