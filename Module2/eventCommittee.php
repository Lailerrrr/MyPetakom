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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $eventID = trim($_POST['eventID']);
    $studentID = trim($_POST['studentID']);
    $role = trim($_POST['role']);
    $committeeID = uniqid("CMT"); // Auto-generate

    if (!empty($eventID) && !empty($studentID) && !empty($role)) {
        $check = $conn->prepare("SELECT * FROM committee WHERE eventID = ? AND studentID = ?");

        if (!$check) {
            die("Prepare failed for SELECT: " . $conn->error);
        }

        $check->bind_param("ss", $eventID, $studentID);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
           $stmt = $conn->prepare("INSERT INTO committee (committeeID, eventID, studentID, role) VALUES (?, ?, ?, ?)");

            if (!$stmt) {
                die("Prepare failed for INSERT: " . $conn->error);
            }

             $stmt->bind_param("ssss", $committeeID, $eventID, $studentID, $role);
            if ($stmt->execute()) {
                $successMsg = "Student assigned as committee member successfully.";
            } else {
                $errorMsg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMsg = "This student is already a committee member for this event.";
        }
        $check->close();
    } else {
        $errorMsg = "Please fill in all fields.";
    }
}

// Fetch events under this advisor
$events = $conn->query("SELECT eventID, eventName FROM event WHERE staffID = '$staffID'");

// Fetch students
$students = $conn->query("SELECT studentID, studentName FROM student");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Committee Management - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventCommittee.css">
    <style>
        /* .container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #1a001f;
            color: #f0d9ff;
        } */

        select, input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #d59de7;
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        button {
            background-color: #c061cb;
            font-weight: bold;
        }

        button:hover {
            background-color: #d883e6;
        }

        label {
            margin-top: 20px;
            display: block;
            font-weight: bold;
        }

        .success { color: #69f069; }
        .error { color: #f57c7c; }
    </style>
</head>
<body>
<div class="container">
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <h2>👥 Manage Committee Members</h2>

        <?php if (!empty($successMsg)): ?>
            <p class="success"><?php echo htmlspecialchars($successMsg); ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            <p class="error"><?php echo htmlspecialchars($errorMsg); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Select Event:</label>
            <select name="eventID" required>
                <option value="">-- Select Event --</option>
                <?php while ($row = $events->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['eventID']); ?>"><?php echo htmlspecialchars($row['eventName']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Select Student:</label>
            <select name="studentID" required>
                <option value="">-- Select Student --</option>
                <?php while ($row = $students->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['studentID']); ?>"><?php echo htmlspecialchars($row['studentName']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Committee Role:</label>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="Chairperson">Chairperson</option>
                <option value="Secretary">Secretary</option>
                <option value="Treasurer">Treasurer</option>
                <option value="Member">Member</option>
            </select>

            <button type="submit" name="submit">Assign as Committee</button>
        </form>
    </main>
</div>
</body>
</html>
