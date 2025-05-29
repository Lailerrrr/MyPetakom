<?php
require_once '../DB_mypetakom/db.php';

header('Content-Type: application/json');

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];

if ($type === 'student') {
    $stmt = $conn->prepare("SELECT studentID as id, studentName as name, 
                           studentEmail as email, verify FROM student WHERE studentID = ?");
} else {
    $stmt = $conn->prepare("SELECT staffID as id, staffName as name, 
                           staffEmail as email, staffRole as role FROM staff WHERE staffID = ?");
}

$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'User not found']);
}
?>