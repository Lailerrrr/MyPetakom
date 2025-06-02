<?php
require_once '../phpqrcode/qrlib.php'; // Make sure this path is correct

if (isset($_GET['studentID'])) {
    $studentID = $_GET['studentID'];

    // URL to student merit info
    $localIP = '172.20.10.6';
    $url = "http://172.20.10.6/MyPetakom/Merit/StudentMeritInfo.php?studentID=" . urlencode($studentID);

    header('Content-Type: image/png');
    $size = 80;
    QRcode::png($url, false, QR_ECLEVEL_L, $size);
} else {
    echo "No student ID provided.";
}
