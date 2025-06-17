<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['data'])) {
    echo "<h2 style='color: red;'>No data provided</h2>";
    exit();
}

$json = json_decode($_GET['data'], true);

if (!$json) {
    echo "<h2 style='color: red;'>Failed to decode JSON</h2>";
    echo "<pre>" . htmlspecialchars($_GET['data']) . "</pre>";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Details</title>
    <style>
        body {
            background-color: #1a001f;
            color: #f0d9ff;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #3a0f5a;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
        }
        h2 {
            text-align: center;
            color: #f9c74f;
        }
        .info {
            margin: 15px 0;
        }
        .info label {
            font-weight: bold;
            display: block;
            color: #fcbf49;
            margin-bottom: 5px;
        }
        .info p {
            margin: 0;
            padding: 8px;
            background-color: #4a177a;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($json['eventName']) ?></h2>

    <div class="info"><label>Description:</label>
        <p><?= nl2br(htmlspecialchars($json['eventDescription'])) ?></p>
    </div>
    <div class="info"><label>Date:</label>
        <p><?= htmlspecialchars($json['eventDate']) ?></p>
    </div>
    <div class="info"><label>Venue:</label>
        <p><?= htmlspecialchars($json['venue']) ?></p>
    </div>
    <div class="info"><label>Status:</label>
        <p><?= htmlspecialchars($json['status']) ?></p>
    </div>
    <div class="info"><label>Level:</label>
        <p><?= htmlspecialchars($json['eventLevel']) ?></p>
    </div>
</div>

</body>
</html>
