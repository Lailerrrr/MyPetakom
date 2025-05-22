<?php
include '../DB_mypetakom/db.php'; // make sure this includes your DB connection

// Add slot
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_slot'])) {
    $event_id = $_POST['event_id'];
    $slot_date = $_POST['slot_date'];
    $slot_time = $_POST['slot_time'];

    $stmt = $conn->prepare("INSERT INTO slots (event_id, slot_date, slot_time) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $event_id, $slot_date, $slot_time);
    $stmt->execute();
    $stmt->close();
}

// Delete slot
if (isset($_GET['delete'])) {
    $slot_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM slots WHERE slot_id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
    $stmt->close();
    header("Location: advisor_slot_attendance.php");
    exit();
}

// Get slots for advisor's events
$advisor_id = 1; // example: use session value in production
$slots_query = $conn->prepare("SELECT s.slot_id, e.event_name, s.slot_date, s.slot_time FROM slots s JOIN events e ON s.event_id = e.event_id WHERE e.advisor_id = ?");
$slots_query->bind_param("i", $advisor_id);
$slots_query->execute();
$slots_result = $slots_query->get_result();
$slots_query->close();

// Get events for dropdown
$events = $conn->query("SELECT event_id, event_name FROM events WHERE advisor_id = $advisor_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Advisor Slot Attendance</title>
    <link rel="stylesheet" href="advisor_attendance_slot.css">
    <style>
        .form-container, .table-container {
            background: rgba(193, 94, 224, 0.1);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 15px #d56ce8;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #fdd7fc;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #e179db;
            background: #330033;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e179db;
        }
        th {
            color: #ffb6ff;
        }
        .delete-btn {
            background-color: #ff2d75;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="main-header">
        <h1>Slot Attendance Management</h1>
        <p>Create, view, and manage your event attendance slots</p>
    </div>

    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="event_id">Select Event</label>
                <select name="event_id" required>
                    <option value="">-- Choose Event --</option>
                    <?php while($row = $events->fetch_assoc()): ?>
                        <option value="<?= $row['event_id'] ?>"><?= $row['event_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="slot_date">Date</label>
                <input type="date" name="slot_date" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label for="slot_time">Time</label>
                <input type="time" name="slot_time" required>
            </div>
            <button type="submit" name="add_slot" class="btn">Add Slot</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Created Slots</h2>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>QR Code</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($slot = $slots_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $slot['event_name'] ?></td>
                    <td><?= $slot['slot_date'] ?></td>
                    <td><?= $slot['slot_time'] ?></td>
                    <td><img src="generate_qr.php?slot_id=<?= $slot['slot_id'] ?>" width="80"></td>
                    <td><a href="?delete=<?= $slot['slot_id'] ?>" class="delete-btn" onclick="return confirm('Delete this slot?')">Delete</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
