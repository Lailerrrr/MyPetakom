<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || strtolower($_SESSION['staffRole']) !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Invalid request.");
}

$type = $_GET['type']; // 'student' or 'advisor'
$id = $_GET['id'];

if ($type === 'student') {
    $table = 'student';
    $idField = 'studentID';
    $nameField = 'studentName';
    $emailField = 'studentEmail';
    // Remove lastLogin because it does not exist
    $sql = "SELECT $idField, $nameField, $emailField FROM $table WHERE $idField = ?";
} else if ($type === 'advisor') {
    $table = 'staff';
    $idField = 'staffID';
    $nameField = 'staffName';
    $emailField = 'staffEmail';
    // Remove lastLogin as well if not exist
    $sql = "SELECT $idField, $nameField, $emailField, staffRole FROM $table WHERE $idField = ?";
} else {
    die("Unknown user type.");
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .user-details { border: 1px solid #ccc; padding: 15px; max-width: 400px; }
        .user-details h2 { margin-top: 0; }
        .user-details p { margin: 5px 0; }
        a { text-decoration: none; color: #2196F3; }
    </style>
</head>
<body>
    <div class="user-details">
        <h2>User Details</h2>
        <p><strong>ID:</strong> <?= htmlspecialchars($user[$idField]) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($user[$nameField]) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user[$emailField]) ?></p>
        <?php if ($type === 'advisor'): ?>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['staffRole']) ?></p>
        <?php endif; ?>
        <p><a href="manageProfile.php">Back to Manage Profiles</a></p>
    </div>
</body>
</html>
