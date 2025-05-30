<?php
session_start();
require_once '../DB_mypetakom/db.php';

// Check if user is logged in and role is 'petakom coordinator'
if (!isset($_SESSION['userID']) || strtolower($_SESSION['staffRole']) !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

// Check for required GET parameters
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: manageProfile.php");
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];

// Validate type
if ($type !== 'student' && $type !== 'advisor' && $type !== 'staff') {
    header("Location: manageProfile.php");
    exit();
}

// Map to correct table and column names
if ($type === 'student') {
    $table = 'student';
    $idCol = 'studentID';
    $nameCol = 'studentName';
    $emailCol = 'studentEmail';
    $passwordCol = 'studentPassword';
} else { // advisor or staff
    $table = 'staff';
    $idCol = 'staffID';
    $nameCol = 'staffName';
    $emailCol = 'staffEmail';
    $passwordCol = 'staffPassword';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        $error = "Name and Email cannot be empty.";
    } else {
        if (!empty($password)) {
            // Hash new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE $table SET $nameCol = ?, $emailCol = ?, $passwordCol = ? WHERE $idCol = ?");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE $table SET $nameCol = ?, $emailCol = ? WHERE $idCol = ?");
            $stmt->bind_param("sss", $name, $email, $id);
        }

        if ($stmt->execute()) {
            header("Location: manageProfile.php?success=Profile updated successfully");
            exit();
        } else {
            $error = "Failed to update profile: " . $stmt->error;
        }
    }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT $nameCol, $emailCol FROM $table WHERE $idCol = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No user found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit <?= htmlspecialchars(ucfirst($type)) ?> Profile</title>
    <link rel="stylesheet" href="profile.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .profile-container {
            width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        label { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;
            border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            background-color: #2196F3; color: white; border: none; padding: 10px 15px;
            border-radius: 4px; cursor: pointer; font-size: 16px;
        }
        button:hover { background-color: #0b7dda; }
        .error { background-color: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        a { color: #2196F3; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>✏️ Edit <?= htmlspecialchars(ucfirst($type)) ?> Profile</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>
                Name:
                <input type="text" name="name" value="<?= htmlspecialchars($user[$nameCol]) ?>" required />
            </label>
            <label>
                Email:
                <input type="email" name="email" value="<?= htmlspecialchars($user[$emailCol]) ?>" required />
            </label>
            <label>
                Password: <small>(Leave blank to keep current password)</small>
                <input type="password" name="password" placeholder="Enter new password" />
            </label>
            <button type="submit">Update Profile</button>
        </form>
        <p><a href="manageProfile.php">← Back to Manage Profiles</a></p>
    </div>
</body>
</html>
