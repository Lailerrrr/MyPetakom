<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}

$conn = new mysqli("localhost", "root", "", "mypetakom_db");

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $claimID = $_GET['id'];

    // Prepare the delete query
    $stmt = $conn->prepare("DELETE FROM meritClaim WHERE claimID = ?");
    $stmt->bind_param("s", $claimID);

    if ($stmt->execute()) {
        // Redirect after delete
        header("Location: MeritClaimStudent.php?deleted=success");
        exit();
    } else {
        echo "Error deleting claim.";
    }

    $stmt->close();
} else {
    echo "No claim ID provided.";
}

$conn->close();
?>

