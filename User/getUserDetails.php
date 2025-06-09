<?php
session_start();
require_once '../DB_mypetakom/db.php';

// Ensure only PETAKOM Coordinator can access
if (!isset($_SESSION['userID']) || strtolower($_SESSION['staffRole']) !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

// Validate query parameters
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Invalid request.");
}

$type = $_GET['type'];
$id = $_GET['id'];

// Define table and fields
if ($type === 'student') {
    $table = 'student';
    $idField = 'studentID';
    $nameField = 'studentName';
    $emailField = 'studentEmail';
    $sql = "SELECT $idField, $nameField, $emailField FROM $table WHERE $idField = ?";
} elseif ($type === 'advisor') {
    $table = 'staff';
    $idField = 'staffID';
    $nameField = 'staffName';
    $emailField = 'staffEmail';
    $sql = "SELECT $idField, $nameField, $emailField, staffRole FROM $table WHERE $idField = ?";
} else {
    die("Unknown user type.");
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database error: " . $conn->error);
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
    <meta charset="UTF-8">
    <title>User Details</title>
    <link rel="stylesheet" href="../User/profile.css">
    <link rel="stylesheet" href="../Home/adminHomePage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <style>
        body {
            background-color: #0b0b0e;
            color: white;
            font-family: 'Inter', sans-serif;
        }
        .user-details {
            background-color: #1a0a1a;
            color: #ffc8e6;
            padding: 2rem;
            max-width: 500px;
            margin: 5rem auto;
            border: 1px solid #ff4081;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(255, 64, 129, 0.25);
        }
        .user-details h2 {
            color: #ffa5d5;
            margin-bottom: 1rem;
            font-size: 24px;
        }
        .user-details p {
            margin: 10px 0;
            font-size: 16px;
        }
        .user-details strong {
            color: #ff80bf;
        }
        .user-details a {
            display: inline-block;
            margin-top: 20px;
            color: #4FC3F7;
            text-decoration: none;
        }
        .user-details a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="profile-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <div class="sidebar-text">
                    <h2>MyPetakom</h2>
                    <p class="role-label">🧑‍💼 PETAKOM Coordinator</p>
                </div>
            </div>

            <nav class="menu">
                <ul>
                    <li><a href="../Home/adminHomePage.php">Dashboard</a></li>
                    <li><a href="/MyPetakom/User/manageProfile.php" class="active">Profile</a></li>
                    <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                    <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                    <li><a href="#">Attendance Tracking</a></li>
                    <li><a href="#">Merit Applications</a></li>
                  
                    <li>
                        <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                            <button type="submit" name="logout" class="sidebar-button">Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

    <div class="user-details">
        <h2>User Details</h2>


        
        <p><strong>ID:</strong> <?= htmlspecialchars($user[$idField]) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($user[$nameField]) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user[$emailField]) ?></p>
        <?php if ($type === 'advisor'): ?>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['staffRole']) ?></p>
        <?php endif; ?>
        <a href="manageProfile.php">← Back to Manage Profiles</a>
    </div>
        </div>
</body>
</html>
