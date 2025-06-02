<?php
require_once '../includes/phpqrcode/qrlib.php'; // Make sure this path is correct

if (isset($_GET['studentID'])) {
    $studentID = $_GET['studentID'];

    // URL to student merit info
    $url = "http://localhost/MyPetakom/Merit/StudentMeritInfo.php?studentID=" . urlencode($studentID);

    header('Content-Type: image/png');
    QRcode::png($url);
} else {
    echo "No student ID provided.";
}
