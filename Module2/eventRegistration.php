<?php
session_start();
require_once '../DB_mypetakom/db.php';


if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];
$successMsg = "";
$errorMsg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = $_POST['eventName'];
    $eventID = uniqid("EVT"); // Auto-generate
    $eventDescription = $_POST['eventDescription'];
    $eventDate = $_POST['eventDate'];
    $venue = $_POST['venue'];
    $approvalDate = $_POST['approvalDate'];
    $status = $_POST['status'];
    

   $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Ensure upload folder exists
    }
    $approvalLetter = "";

    if (isset($_FILES['approvalLetter']) && $_FILES['approvalLetter']['error'] == 0) {
        $filename = basename($_FILES["approvalLetter"]["name"]);
        $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $filename); // Clean file name
        $targetFilePath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["approvalLetter"]["tmp_name"], $targetFilePath)) {
        $approvalLetter = $targetFilePath;

            $stmt = $conn->prepare("INSERT INTO event (eventName, eventID, eventDescription, eventDate, venue, approvalLetter, approvalDate, status, advisorID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $eventName, $eventID, $eventDescription, $eventDate, $venue, $approvalLetter, $approvalDate, $status, $advisorID);

            if ($stmt->execute()) {
                $successMsg = "Event registered successfully.";
            } else {
                $errorMsg = "Database error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errorMsg = "Failed to upload approval letter.";
        }
    } else {
        $errorMsg = "Please upload the approval letter.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Event Registration - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css" />
    <link rel="stylesheet" href="../Module2/eventRegistration.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="container"> 
        <?php include '../sidebar.php'; ?>
    
        <main class="main-content">
        
            <h2>📅 Register a New Event</h2>

            <?php if ($successMsg): ?>
                <p style="color: green;"><?php echo $successMsg; ?></p>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <p style="color: red;"><?php echo $errorMsg; ?></p>
            <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                   

                    <label>Event Name:</label>
                    <input type="text" name="eventName" required><br><br>

                    <label>Event Description:</label>
                    <textarea name="eventDescription" required></textarea><br><br>

                    <label>Event Date:</label>
                    <input type="date" name="eventDate" required><br><br>

                    <label>Geolocation (Venue):</label>
                    <input type="text" name="venue" placeholder="Google Maps link or address" required><br><br>

                    <label>Approval Date:</label>
                    <input type="date" name="approvalDate" required><br><br>

                    <label>Approval Letter (PDF):</label>
                    <input type="file" name="approvalLetter" accept=".pdf" required><br><br>

                    <label>Status:</label>
                    <select name="status" required>
                        <option value="Active">Active</option>
                        <option value="Postponed">Postponed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select><br><br>

                    <button type="submit">Register Event</button>
                </form>       
        </main>
    </div>
</body>
</html>
