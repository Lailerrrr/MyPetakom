<?php
require_once '../phpqrcode/qrlib.php';
require_once '../DB_mypetakom/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$folder = '../QRCODE/';
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$sql = "SELECT studentID FROM student";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("❌ No students found in the database.");
}

while ($row = $result->fetch_assoc()) {
    $studentID = $row['studentID'];
    $url = "http://172.20.10.6/MyPetakom/Merit/StudentMeritInfo.php?studentID=" . urlencode($studentID);

    $fileName = $studentID . '.png';
    $filePath = $folder . $fileName; // Full server path
    $relativePath = 'QRCODE/' . $fileName; // Path saved in DB

    // Force regenerate for debugging
    QRcode::png($url, $filePath, QR_ECLEVEL_L, 8);

    if (file_exists($filePath)) {
        $stmt = $conn->prepare("UPDATE student SET qr_code = ? WHERE studentID = ?");
        $stmt->bind_param("ss", $relativePath, $studentID);
        if ($stmt->execute()) {
            echo "✅ QR code for $studentID generated and saved.<br>";
        } else {
            echo "❌ Failed to update DB for $studentID<br>";
        }
        $stmt->close();
    } else {
        echo "❌ QR code file not created for $studentID<br>";
    }
}

echo "<br>✅ All QR generation complete.";
?>


