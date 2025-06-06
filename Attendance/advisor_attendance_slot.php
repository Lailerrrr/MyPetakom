<?php
session_start();
require_once '../DB_mypetakom/db.php';
require_once '../phpqrcode/qrlib.php';




if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}


$staffID = $_SESSION['userID'];
$slots = [];
$error = '';


// Create new slot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_slot'])) {
    $eventID = $_POST['eventID'];
    $slotTime = $_POST['slotTime'];

    // Get event info
    $stmt = $conn->prepare("SELECT eventName, eventDate FROM event WHERE eventID = ? AND staffID = ?");
    $stmt->bind_param("ss", $eventID, $staffID);
    $stmt->execute();
    $eventResult = $stmt->get_result();
    $event = $eventResult->fetch_assoc();
    $stmt->close();

    if ($event) {
        $slotName = $event['eventName'];
        $attendanceDate = $event['eventDate'];

        // Generate unique slotID
        $result = $conn->query("SELECT slotID FROM AttendanceSlot ORDER BY slotID DESC LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            $lastID = $row['slotID'];
            $num = (int)substr($lastID, 1);
            $newNum = $num + 1;
            $slotID = "S" . str_pad($newNum, 3, '0', STR_PAD_LEFT);
        } else {
            $slotID = "S001";
        }

        // Insert slot
        $stmt = $conn->prepare("INSERT INTO AttendanceSlot (slotID, eventID, slotName, attendanceDate, slotTime) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $slotID, $eventID, $slotName, $attendanceDate, $slotTime);
        
        $stmt->execute();
        $stmt->close();

        // Generate QR Code
        $url = "http://192.168.0.181/MyPetakom/Attendance/QRattendance_register.php?slotID=$slotID";
        $qrFileName = "slot_$slotID.png";
        $qrPath = "../QR/$qrFileName";
        QRcode::png($url, $qrPath, QR_ECLEVEL_L, 4);

        // Update QR path
        $stmt = $conn->prepare("UPDATE AttendanceSlot SET qrCodePath = ? WHERE slotID = ?");
        $stmt->bind_param("ss", $qrFileName, $slotID);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Attendance slot created successfully.";
        header("Location: advisor_attendance_slot.php");  // redirect to same page
        exit();

    } else {
        $error = "Invalid Event ID.";
    }
}



// Delete slot
if (isset($_POST['delete_slot'])) {
    $slotID = $_POST['slotID'];

    $stmt = $conn->prepare("SELECT qrCodePath FROM AttendanceSlot WHERE slotID = ?");
    $stmt->bind_param("s", $slotID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $qrPath = "../QR/" . $row['qrCodePath'];
        if (file_exists($qrPath)) {
            unlink($qrPath);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM AttendanceSlot WHERE slotID = ?");
    $stmt->bind_param("s", $slotID);
    $stmt->execute();
    $stmt->close();
}

// Load all advisor slots
$sql = "SELECT s.slotID, s.slotName, s.qrCodePath, s.attendanceDate, s.slotTime, e.eventName
        FROM AttendanceSlot s
        JOIN event e ON s.eventID = e.eventID
        WHERE e.staffID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

$stmt->close();
?>


<!-- Bootstrap 5 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../Attendance/advisor_attendance_slot.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Sidebar -->
    
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="sidebar-logo" />
                <div class="sidebar-text">
                    <h2>MyPetakom</h2>
                    <p class="role-label">🧭 Advisor</p>
                </div>
            </div>

            <nav class="menu">
                <ul>
                    <li><a href="../Home/advisorHomepage.php">User  Dashboard</a></li>
                    <li><a href="../User /Profiles.php">Profile</a></li>
                    <li><a href="../Module2/eventList.php">Event List</a></li>
                    <li><a href="../Module2/eventRegistration.php">Event Registration</a></li>
                    <li><a href="../Module2/manageEvent.php">Manage Events</a></li>
                    <li><a href="../Module2/eventCommittee.php">Committee Management</a></li>
                    <li><a href="../Module2/eventMerit.php">Merit Applications</a></li>
                    <li><a href="../Attendance/advisor_attendance_slot.php" class="active">Attendance Slot</a></li>
                    <li><a href="../Merit/MeritApprovalEventAdvisor.php">Merit Approval</a></li>
                    <li>
                        <form method="post" action="../ManageLogin/Logout.php" style="display:inline;">
                            <button name="logout" class="sidebar-logout-button">Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
    <div class="flex-grow-1 p-4">

        <header class="main-header">
            <h1>Create & Manage Attendance Slots</h1>
        </header>

        <section class="form-section mt-4">
            <form method="post" class="slot-form">
                <h2>Create New Attendance Slot</h2><br>

                <label>Event ID:</label>
                <input type="text" name="eventID" required class="form-control mb-3">

                <label>Slot Time:</label>
                <input type="time" name="slotTime" required class="form-control mb-3">

                <button type="submit" name="create_slot" class="btn btn-primary">Create Slot</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-3"><?= $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success mt-3"><?= $success; ?></div>
            <?php endif; ?>
        </section>

        <hr>

        <div class="container-fluid">
            <h2>Attendance Slots</h2>
            <p>Manage attendance slots for events.</p>

            <table class="table table-bordered mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>Slot ID</th>
                        <th>Slot Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>QR Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                    <tr>
                        <td><?= htmlspecialchars($slot['slotID']); ?></td>
                        <td><?= htmlspecialchars($slot['slotName']); ?></td>
                        <td><?= $slot['attendanceDate']; ?></td>
                        <td><?= $slot['slotTime']; ?></td>
                        <td>
                            <?php if (!empty($slot['qrCodePath'])): ?>
                                <img src="../QR/<?= $slot['qrCodePath']; ?>" alt="QR Code" width="100">
                            <?php else: ?>
                                <span style="color:red;">QR Missing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $slot['slotID']; ?>">Edit</button>

                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this slot?');">
                                    <input type="hidden" name="slotID" value="<?= $slot['slotID']; ?>">
                                    <button type="submit" name="delete_slot" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $slot['slotID']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $slot['slotID']; ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="post">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editModalLabel<?= $slot['slotID']; ?>">Edit Slot: <?= $slot['slotID']; ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="edit_slotID" value="<?= $slot['slotID']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Attendance Date</label>
                                    <input type="date" class="form-control" name="edit_attendanceDate" value="<?= $slot['attendanceDate']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Slot Time</label>
                                    <input type="time" class="form-control" name="edit_slotTime" value="<?= $slot['slotTime']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" name="edit_slot_submit" class="btn btn-success">Save Changes</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

