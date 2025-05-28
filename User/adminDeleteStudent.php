<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Petakom Coordinator') {
    header("Location: ../ManagLogin/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../User/adminProfile.php");
    exit();
}

$studentID = $_GET['id'];

// Delete student
$stmt = $conn->prepare("DELETE FROM student WHERE studentID=?");
$stmt->bind_param("s", $studentID);
$stmt->execute();

header("Location: ../User/adminProfile.php");
exit();
?>
