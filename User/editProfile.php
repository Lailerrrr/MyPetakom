<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: manageProfiles.php");
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];
$table = $type === 'student' ? 'student' : 'staff';
$idCol = $type === 'student' ? 'studentID' : 'staffID';
$nameCol = $type === 'student' ? 'studentName' : 'staffName';
$emailCol = $type === 'student' ? 'studentEmail' : 'staffEmail';
$passwordCol = $type === 'student' ? 'studentPassword' : 'staffPassword';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE $table SET $nameCol=?, $emailCol=?, $passwordCol=? WHERE $idCol=?");
    $stmt->bind_param("ssss", $name, $email, $password, $id);
    $stmt->execute();

    header("Location: manageProfiles.php");
    exit();
}

// Fetch current data
$result = $conn->query("SELECT * FROM $table WHERE $idCol = '$id'");
if ($result->num_rows === 0) {
    echo "No user found.";
    exit();
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit <?= ucfirst($type) ?> Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <h2>✏️ Edit <?= ucfirst($type) ?> Profile</h2>
        <form method="POST">
            <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($user[$nameCol]) ?>" required></label>
            <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user[$emailCol]) ?>" required></label>
            <label>Password: <input type="password" name="password" placeholder="Enter new password" required></label>
            <button type="submit">Update Profile</button>
        </form>
        <p><a href="manageProfiles.php">← Back to Manage Profiles</a></p>
    </div>
</body>
</html>
