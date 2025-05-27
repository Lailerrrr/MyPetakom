<?php
session_start();
require_once '../DB_mypetakom/db.php';

// Check if user is logged in and has coordinator role
if (!isset($_SESSION['userID']) || ($_SESSION['role'] ?? '') !== 'petakom_coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membershipID'], $_POST['action'])) {

    // CSRF token validation
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header("Location: verifyMembership.php?error=Invalid+CSRF+token");
        exit();
    }

    $membershipID = intval($_POST['membershipID']);
    $action = $_POST['action'];

    // Validate action value
    if (!in_array($action, ['approve', 'reject'], true)) {
        header("Location: verifyMembership.php?error=Invalid+action");
        exit();
    }

    $newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Check if membership record exists
    $checkStmt = $conn->prepare("SELECT status FROM membership WHERE membershipID = ?");
    if (!$checkStmt) {
        header("Location: verifyMembership.php?error=Database+error");
        exit();
    }
    $checkStmt->bind_param("i", $membershipID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        $conn->close();
        header("Location: verifyMembership.php?error=Membership+record+not+found");
        exit();
    }

    $row = $checkResult->fetch_assoc();
    $currentStatus = $row['status'];
    $checkStmt->close();

    // If status is already the same, no need to update
    if ($currentStatus === $newStatus) {
        $conn->close();
        header("Location: verifyMembership.php?error=Membership+already+" . urlencode($newStatus));
        exit();
    }

    // Update membership status
    $updateStmt = $conn->prepare("UPDATE membership SET status = ? WHERE membershipID = ?");
    if (!$updateStmt) {
        $conn->close();
        header("Location: verifyMembership.php?error=Database+error+on+update");
        exit();
    }
    $updateStmt->bind_param("si", $newStatus, $membershipID);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        $updateStmt->close();
        $conn->close();
        header("Location: verifyMembership.php?success=Membership+status+updated+to+" . urlencode($newStatus));
        exit();
    } else {
        $updateStmt->close();
        $conn->close();
        header("Location: verifyMembership.php?error=Update+failed");
        exit();
    }
} else {
    // Invalid request or missing data
    header("Location: verifyMembership.php?error=Invalid+submission");
    exit();
}

?>