<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$successMsg = $errorMsg = "";

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['meritApplicationID'], $_POST['action'])) {
    $meritApplicationID = $_POST['meritApplicationID'];
    $action = $_POST['action']; // Approve or Reject

    if (in_array($action, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE meritapplication SET approvalStatus = ? WHERE meritApplicationID = ?");
        $stmt->bind_param("ss", $action, $meritApplicationID);

        if ($stmt->execute()) {
            $successMsg = "Application $meritApplicationID has been $action.";
        } else {
            $errorMsg = "Failed to update application: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMsg = "Invalid action.";
    }
}

// Fetch pending applications
$query = "SELECT ma.meritApplicationID, ma.appliedDate, ma.approvalStatus, e.eventName 
          FROM meritapplication ma
          JOIN event e ON ma.eventID = e.eventID
          WHERE ma.approvalStatus = 'Pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Merit Applications - MyPetakom</title>
    <link rel="stylesheet" href="../Home/adminHomePage.css" />
    <style>
        .container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
     }

        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #1a001f;
            color: #f0d9ff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ff80bf;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background-color:#ff4081;
        }
        tr:nth-child(even) {
            background-color: #4a177a;
        }

        form.inline-form {
            display: inline;
        }

        button {
            padding: 6px 12px;
            margin: 2px;
            border-radius: 6px;
            border: none;
            color: white;
            font-weight: bold;
        }

        .approve-btn {
            background-color: #4CAF50;
        }

        .reject-btn {
            background-color: #f44336;
        }

        .success { color: #69f069; }
        .error { color: #f57c7c; }
    </style>
</head>
<body>
   
<div class="container">
   <aside class="sidebar">
    <h2>MyPetakom</h2>
    <p>Administrator</p>
    <nav class="menu">
        <ul>
            <li><a href="#" class="active">Dashboard</a></li>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Membership Applications</a></li>
            <li><a href="../Module2/eventApproval.php">Event Management</a></li>
            <li><a href="#">Attendance Tracking</a></li>
            <li><a href="#">Merit Applications</a></li>
            <li><a href="#">Reports & Analytics</a></li>
            <li><a href="#">System Settings</a></li>
            <li><form method="post" action="../ManageLogin/Logout.php"><button name="logout">Logout</button></form></li>
        </ul>
    </nav>
</aside>

    <main class="main-content">
        <h2>📝 Approve Merit Applications</h2>

        <?php if (!empty($successMsg)): ?>
            <p class="success"><?php echo $successMsg; ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            <p class="error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Merit Application ID</th>
                    <th>Event Name</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['meritApplicationID']; ?></td>
                            <td><?php echo $row['eventName']; ?></td>
                            <td><?php echo $row['appliedDate']; ?></td>
                            <td><?php echo $row['approvalStatus']; ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="meritApplicationID" value="<?php echo $row['meritApplicationID']; ?>">
                                    <button type="submit" name="action" value="Approved" class="approve-btn">Approve</button>
                                    <button type="submit" name="action" value="Rejected" class="reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No pending applications.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
