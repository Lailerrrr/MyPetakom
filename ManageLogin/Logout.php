<?php
    session_start();

    // 🔐 Unset all session variables
    $_SESSION = [];
   

    // Also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    // Clear cookies
    setcookie('role', '', time() - 3600, "/");
    setcookie('email', '', time() - 3600, "/");
    setcookie("userID", "", time() - 3600, "/");

    // 🚫 Prevent browser caching (in case user tries to press Back)
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    // 🔁 Redirect to login page
    header("Location: login.php");
    exit;

   
?>

