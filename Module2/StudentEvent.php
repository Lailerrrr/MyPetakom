<?php
session_start();
require_once '../DB_mypetakom/db.php';

// Fetch events with QR code paths
$sql = "SELECT eventName, eventID, qrCode FROM event ORDER BY eventDate DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="../Module2/StudentEvent.css" />

    <title>Event QR Codes</title>
    <style>
        
.logo {
    height: 100px;
    object-fit: contain;
  
}
        .sidebar {
background: linear-gradient(180deg, #ff2a9e, #4a004e);
width: 250px;
display: flex;
flex-direction: column;
padding: 20px 15px 20px 20px;
box-shadow: 3px 0 10px rgba(255, 20, 147, 0.5);
}

.sidebar-header {
display: flex;
flex-direction: column;
align-items: flex-start;


padding-left: 5px;
}


.sidebar-text h2 {
color: #fff;
font-weight: 700;
font-size: 22px;
text-shadow: 1px 1px #3a003a;
margin-bottom: 0;
}

.role-label {
color: #f8bbd0;
font-weight: 600;
font-size: 14px;
margin-top: 0;
}

/* Menu */
.menu ul {
list-style: none;
display: flex;
flex-direction: column;
gap: 18px;
padding-left: 5px;
}

.container {
  display: flex;
  height: 100vh;
  width: 100%;
}
.menu ul li a {
color: #f8bbd0;
text-decoration: none;
font-weight: 600;
font-size: 17px;
padding: 8px 16px;
border-radius: 12px;
display: block;
transition: background 0.3s, color 0.3s;
box-shadow: 0 0 5px transparent;
}

.menu ul li a:hover,
.menu ul li a.active {
background: #ff69b4;
color: #fff;
box-shadow: 0 0 12px #ff69b4;
}
        </style>
</head>
<body>
      <div class="container">

    <aside class="sidebar">
    <div class="sidebar-header">
        <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
        <div class="sidebar-text">
            <h2>MyPetakom</h2>
            <p class="role-label">🎓 Student</p>
        </div>
    </div>
    <nav class="menu">
        <ul>
            <li><a href="/MyPetakom/User/Profiles.php">Profile</a></li>
            <li><a href="/MyPetakom/membership/applyMembership.php">Apply Membership</a></li>
            <li><a href="/MyPetakom/membership/viewMembership.php">View Membership</a></li>
            <li><a href="/MyPetakom/Attendance/event_register.php">Event Attendance</a></li>
            <li><a href="/MyPetakom/Merit/MeritClaimStudent.php">Merit Claim</a></li>
            <li><a href="/MyPetakom/Merit/ScanQR.php">Scan QR</a></li>
             <li><a href="../Module2/StudentEvent.php" class = "active" >Event Detail</a></li>
            <li><a href="/MyPetakom/ManageLogin/Logout.php">Logout</a></li>
        </ul>
    </nav>
</aside>
<main class="main-content">
    <h2>Scan to View Event Details</h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Event Name</th>
                <th>QR Code</th>
            </tr>
        </thead>
        <tbody>
           <?php while ($row = $result->fetch_assoc()): 
                $eventName = htmlspecialchars($row['eventName']);
                    $qrPath = "../" . $row['qrCode']; // Prepend '../' to get correct relative path
                ?>
                    <tr>
                        <td><?= $eventName ?></td>
                        <td>
                            <?php if (!empty($row['qrCode']) && file_exists($qrPath)): ?>
                                <img src="<?= $qrPath ?>" alt="QR Code" class="qr">
                            <?php else: ?>
                                <span style="color: #f57c7c;">QR not found</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-events">No events found with QR codes.</div>
    <?php endif; ?>
      </div>
</main>
</body>
</html>
