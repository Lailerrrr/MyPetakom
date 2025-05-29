<?php
session_start();
require_once '../DB_mypetakom/db.php'; // Ensure this path is correct

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

// IMPORTANT: Ensure $_SESSION['role'] is set upon successful login in your login.php
// If you intend to restrict this page to 'petakom_coordinator' only, uncomment and ensure role is set.
// if (($_SESSION['role'] ?? '') !== 'petakom_coordinator') {
//     header("Location: ../ManageLogin/login.php"); // Or to an unauthorized access page
//     exit();
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membershipID'], $_POST['action'])) {

    // CSRF token validation
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Log this error for security monitoring
        error_log("CSRF token mismatch for user ID: " . ($_SESSION['userID'] ?? 'N/A'));
        header("Location: verifyMembership.php?error=Invalid+request+security+token");
        exit();
    }

    $membershipID = intval($_POST['membershipID']);
    $action = $_POST['action'];

    // Validate action value
    if (!in_array($action, ['approve', 'reject'], true)) {
        header("Location: verifyMembership.php?error=Invalid+action+specified");
        exit();
    }

    $newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Check if membership record exists and its current status
    $checkStmt = $conn->prepare("SELECT status FROM membership WHERE membershipID = ?");
    if (!$checkStmt) {
        // Proper error handling, log the error
        error_log("Database prepare error (checkStmt): " . $conn->error);
        header("Location: verifyMembership.php?error=Database+error+occurred");
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
        header("Location: verifyMembership.php?error=Membership+already+" . urlencode($newStatus) . ".");
        exit();
    }

    // Update membership status
    $updateStmt = $conn->prepare("UPDATE membership SET status = ? WHERE membershipID = ?");
    if (!$updateStmt) {
        error_log("Database prepare error (updateStmt): " . $conn->error);
        $conn->close();
        header("Location: verifyMembership.php?error=Database+update+error");
        exit();
    }
    $updateStmt->bind_param("si", $newStatus, $membershipID);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        $updateStmt->close();
        $conn->close();
        header("Location: verifyMembership.php?success=Membership+status+successfully+updated+to+" . urlencode($newStatus) . ".");
        exit();
    } else {
        $updateStmt->close();
        $conn->close();
        header("Location: verifyMembership.php?error=Failed+to+update+membership+status.");
        exit();
    }
} else {
    // Invalid request method or missing data
    header("Location: verifyMembership.php?error=Invalid+request+method+or+missing+data");
    exit();
}

?>