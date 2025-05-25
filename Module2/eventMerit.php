<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];
$successMsg = $errorMsg = "";

// Handle merit application form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eventID'])) {
    $eventID = $_POST['eventID'];

    // Check if merit application already exists
    $check = $conn->prepare("SELECT * FROM meritapplication WHERE eventID = ?");
    $check->bind_param("s", $eventID);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        // Generate a unique meritApplicationID (e.g., "MA202405250001")
        $uniqueID = 'MA' . rand(10, 99);

        // Get current date
        $appliedDate = date("Y-m-d");

        // Prepare insert query
        $stmt = $conn->prepare("INSERT INTO meritapplication (meritApplicationID, appliedDate, approvalStatus, eventID) VALUES (?, ?, 'Pending', ?)");
        $stmt->bind_param("sss", $uniqueID, $appliedDate, $eventID);

        if ($stmt->execute()) {
            $successMsg = "Merit application submitted successfully. Awaiting coordinator approval.";
        } else {
            $errorMsg = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMsg = "Merit application for this event has already been submitted.";
    }

    $check->close();
}


// Fetch events under this advisor
$events = $conn->query("SELECT eventID, eventName FROM event WHERE advisorID = '$advisorID'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Merit Application - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventMerit.css">
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

        select, button {
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
        <h2>🎖️ Merit Application</h2>
        <p>The Event Advisor must apply for student participation merit during registration to ensure that the event qualifies for a merit award. The merit application will be approved by the Petakom Coordinator.</p>

        <?php if (!empty($successMsg)): ?>
            
            <p class="success"><?php echo $successMsg; ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMsg)): ?>
            <p class="error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Select Event for Merit Application:</label>
            <select name="eventID" required>
                <option value="">-- Select Event --</option>
                <?php while ($row = $events->fetch_assoc()): ?>
                    <option value="<?php echo $row['eventID']; ?>"><?php echo $row['eventName']; ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Submit Merit Application</button>
        </form>
    </main>
</div>
</body>
</html>
