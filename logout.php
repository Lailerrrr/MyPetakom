<?php
    session_start();

    // Unset all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Remove cookies if they exist
    if (isset($_COOKIE['role'])) {
        setcookie("role", "", time() - 3600, "/");
    }
    if (isset($_COOKIE['username'])) {
        setcookie("username", "", time() - 3600, "/");
    }
    if (isset($_COOKIE['userID'])) {
        setcookie("userID", "", time() - 3600, "/");
    }

    // Redirect to login page after logout
    header("Location: login.php");
    exit();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout - MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="refresh" content="3;url=login.php" />
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header class="header">
        <div class="logos">
            <img src="umpsa-logo.png" alt="UMPSA Logo" />
            <img src="petakom-logo.png" alt="PETAKOM Logo" />
        </div>
        <div class="title">MyPetakom Student Portal</div>
    </header>

    <main class="main-content">
        <h1>You have successfully logged out</h1>
        <p>Redirecting to login page in 3 seconds...</p>
        <a href="login.php">Click here if you are not redirected</a>
    </main>

    <footer>
        &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
    </footer>
</body>
</html>