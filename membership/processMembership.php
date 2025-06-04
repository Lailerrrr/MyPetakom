<?php
session_start();
require_once '../DB_mypetakom/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Input validation (now allowing string membershipID)
    if (!isset($_POST['membershipID'], $_POST['action'])) {
        die("Invalid form submission: Missing membershipID or action.");
    }

    $membershipID = trim($_POST['membershipID']);
    $action = $_POST['action'];

    // Sanitize action
    if ($action === 'approve') {
        $newStatus = 'Approved';
    } elseif ($action === 'reject') {
        $newStatus = 'Rejected';
    } else {
        die("Invalid action.");
    }

    // Update the membership status in the database
    $stmt = $conn->prepare("UPDATE membership SET status = ? WHERE membershipID = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $newStatus, $membershipID); // both are now strings

    if ($stmt->execute()) {
        header("Location: verifyMembership.php?success=Membership ID $membershipID updated to $newStatus.");
    } else {
        header("Location: verifyMembership.php?error=Failed to update membership status for ID $membershipID.");
    }

    $stmt->close();
    $conn->close();
    exit();

} else {
    // Redirect if accessed directly
    header("Location: verifyMembership.php");
    exit();
}
