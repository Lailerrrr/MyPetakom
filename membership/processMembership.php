<?php
session_start();
require_once '../DB_mypetakom/db.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // Validate required fields
    if (!isset($_POST['membershipID'], $_POST['action'])) {
        die("Missing required data.");
    }

    $membershipID = intval($_POST['membershipID']);
    $action = $_POST['action'];

    // Determine the new status
    if ($action === 'approve') {
        $newStatus = 'Approved';
    } elseif ($action === 'reject') {
        $newStatus = 'Rejected';
    } else {
        die("Invalid action.");
    }

    // Prepare and execute the SQL update query
    $stmt = $conn->prepare("UPDATE membership SET status = ? WHERE membershipID = ?");
    $stmt->bind_param("si", $newStatus, $membershipID);

    if ($stmt->execute()) {
        header("Location: verifyMembership.php?success=Membership successfully $newStatus.");
        exit();
    } else {
        header("Location: verifyMembership.php?error=Failed to update membership status.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect back if accessed directly
    header("Location: verifyMembership.php");
    exit();
}
?>
