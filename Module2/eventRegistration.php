<?php
session_start();
require_once '../DB_mypetakom/db.php';
require_once '../phpqrcode/qrlib.php'; // Adjust path as needed


if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = $_POST['eventName'];
    $eventID = uniqid("EVT");
    $eventDescription = $_POST['eventDescription'];
    $eventDate = $_POST['eventDate'];
    $venue = $_POST['venue'];
    $approvalDate = $_POST['approvalDate'];
    $status = $_POST['status'];
    $eventLevel = $_POST['eventLevel'];

    // Check staffID is still set (failsafe)
    if (empty($staffID)) {
        $errorMsg = "❌ Session expired. Please log in again.";
    } else {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['approvalLetter']) && $_FILES['approvalLetter']['error'] == 0) {
            $filename = basename($_FILES["approvalLetter"]["name"]);
            $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $filename);
            $targetFilePath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES["approvalLetter"]["tmp_name"], $targetFilePath)) {
                $approvalLetter = $targetFilePath;

                $stmt = $conn->prepare("INSERT INTO event (eventName, eventID, eventDescription, eventDate, venue, approvalLetter, approvalDate, status, eventLevel, staffID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssss", $eventName, $eventID, $eventDescription, $eventDate, $venue, $approvalLetter, $approvalDate, $status, $eventLevel, $staffID);

                $qrDir = "../Module2/qrcodes/";
                if (!is_dir($qrDir)) {
                    mkdir($qrDir, 0777, true);
                }

                $eventData = [
                      "eventName" => $eventName,
                      "eventDescription" => $eventDescription,
                      "eventDate" => $eventDate,
                      "venue" => $venue,
                      "status" => $status,
                      "eventLevel" => $eventLevel 
                  ];

                 // Encode JSON, then URL encode it for safe use in URL
                $encodedData = urlencode(json_encode($eventData));

                // Use your IP address or domain here
                $qrContent = "http://localhost/MyPetakom/Module2/EventDetails.php?data=" . $encodedData;

                $qrFilename = $qrDir . $eventID . ".png";
                QRcode::png($qrContent, $qrFilename);

                if ($stmt->execute()) {
                    $successMsg = "✅ Event registered successfully.";
                } else {
                    $errorMsg = "❌ Database error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $errorMsg = "❌ Failed to upload approval letter.";
            }
        } else {
            $errorMsg = "❌ Please upload the approval letter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Event Registration - MyPetakom</title>
  <link rel="stylesheet" href="../sidebar.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Inter', sans-serif;
      background: #0a0a0a;
      color: white;
      min-height: 100vh;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100%;
    }

    .main-content {
      flex: 1;
      background: #1a001a;
      padding: 40px 60px;
      overflow-y: auto;
    }

    h2 {
      font-size: 32px;
      color: #f3b4ec;
      margin-bottom: 20px;
    }

    form input[type="text"],
    form input[type="date"],
    form input[type="file"],
    form textarea,
    form select {
      background: rgba(255, 255, 255, 1) !important;
      color: black !important;
      border: 1px solid #c267e4;
      border-radius: 8px;
      padding: 10px;
      margin-top: 5px;
      width: 100%;
    }

    form label {
      display: block;
      font-weight: 600;
      margin-top: 15px;
      color: #fcdfff;
    }

    form button {
      margin-top: 20px;
      padding: 12px 20px;
      background: #b14ac6;
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    form button:hover {
      background: #e179db;
    }

    .message {
      margin-top: 15px;
      padding: 10px 15px;
      border-radius: 10px;
      font-weight: 600;
    }

    .success {
      background: #1abc9c;
      color: white;
    }

    .error {
      background: #e74c3c;
      color: white;
    }

    .session-info {
      background-color: #333;
      padding: 10px;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 20px;
      color: #f3f3f3;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .main-content {
        padding: 20px;
      }

      form {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
      <h2>📅 Register a New Event</h2>

      <div class="session-info">
        Logged in as: <strong><?php echo htmlspecialchars($staffID); ?></strong>
      </div>

      <?php if ($successMsg): ?>
        <div class="message success"><?php echo $successMsg; ?></div>
      <?php elseif ($errorMsg): ?>
        <div class="message error"><?php echo $errorMsg; ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <label>Event Name:</label>
        <input type="text" name="eventName" required>

        <label>Event Description:</label>
        <textarea name="eventDescription" required></textarea>

        <label>Event Date:</label>
        <input type="date" name="eventDate" required>

        <label>Venue:</label>
        <input type="text" name="venue" placeholder="Google Maps link or address" required>

        <label>Approval Date:</label>
        <input type="date" name="approvalDate" required>

        <label>Approval Letter (PDF):</label>
        <input type="file" name="approvalLetter" accept=".pdf" required>

        <label>Event Level:</label>
        <select name="eventLevel" required>
          <option value="UMPSA" selected>UMPSA</option>
          <option value="International">International</option>
          <option value="National">National</option>
          <option value="State">State</option>
          <option value="District">District</option>
        </select>

        <label>Status:</label>
        <select name="status" required>
          <option value="Active" selected>Active</option>
          <option value="Postponed">Postponed</option>
          <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit">Register Event</button>
      </form>
    </main>
  </div>
</body>
</html>
