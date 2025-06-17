<?php
require_once '../phpqrcode/qrlib.php';
require_once '../DB_mypetakom/db.php';

$folder = '../QRCODE/';
if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
}

$sql = "SELECT studentID FROM student";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $studentID = $row['studentID'];
    $url = "http://172.20.10.6/MyPetakom/Merit/StudentMeritInfo.php?studentID=" . urlencode($studentID);

    $fileName = $studentID . '.png';
    $filePath = $folder . $fileName; // physical path
    $relativePath = 'QRCODE/' . $fileName; // this is what goes in DB

    // Only generate QR if it doesn't exist
    if (!file_exists($filePath)) {
        QRcode::png($url, $filePath, QR_ECLEVEL_L, 8);
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE student SET qr_code = ? WHERE studentID = ?");
    $stmt->bind_param("ss", $relativePath, $studentID);
    $stmt->execute();
    $stmt->close();
}

echo "QR codes generated and saved to database.";
?>

