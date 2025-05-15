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
    ...
    <meta http-equiv="refresh" content="3;url=login.php" />
</head>
<body>
    ...
    <h1>You have successfully logged out</h1>
    <p>Redirecting to login page in 3 seconds...</p>
    <a href="login.php">Click here if you are not redirected</a>
</body>
</html>