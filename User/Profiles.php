<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$userID = $_SESSION['userID'];
$role = strtolower($_SESSION['role'] ?? $_SESSION['staffRole'] ?? 'student');

// Determine table and field names based on role
$table = ($role === 'student') ? 'student' : 'staff';
$idField = ($role === 'student') ? 'studentID' : 'staffID';
$nameField = ($role === 'student') ? 'studentName' : 'staffName';
$emailField = ($role === 'student') ? 'studentEmail' : 'staffEmail';
$passField = ($role === 'student') ? 'studentPassword' : 'staffPassword';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if (!$email) {
        $_SESSION['error'] = "Invalid email format";
    } else {
        if ($password) {
            $stmt = $conn->prepare("UPDATE $table SET $nameField=?, $emailField=?, $passField=? WHERE $idField=?");
            $stmt->bind_param("ssss", $name, $email, $password, $userID);
        } else {
            $stmt = $conn->prepare("UPDATE $table SET $nameField=?, $emailField=? WHERE $idField=?");
            $stmt->bind_param("sss", $name, $email, $userID);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating profile: " . $stmt->error;
        }
    }
    header("Location: profile.php");
    exit();
}

// Fetch current user data
$stmt = $conn->prepare("SELECT $nameField, $emailField FROM $table WHERE $idField=?");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found";
    header("Location: ../ManageLogin/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | MyPetakom</title>
    <link rel="stylesheet" href="../Home/adminHomePage.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
            <div class="sidebar-text">
                <h2>MyPetakom</h2>
                <p class="role-label"><?= ucfirst($role) ?></p>
            </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="../Home/<?= $role === 'student' ? 'studentHomePage.php' : 'advisorHomePage.php' ?>">Dashboard</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <?php if ($role !== 'student'): ?>
              
                <?php endif; ?>
                <li>
                    <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                        <button type="submit" name="logout" class="sidebar-button">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="profile-content">
            <div class="profile-header">
                <h2>👤 My Profile</h2>
                <span class="role-label"><?= ucwords(str_replace('_', ' ', $role)) ?></span>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user[$nameField]) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user[$emailField]) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password confirmation validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password && password !== confirmPassword) {
                alert('Passwords do not match!');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>