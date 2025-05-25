<?php
    session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

    header("Pragma: no-cache");


    session_unset();
    session_destroy();

    // Also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Clear cookies
    setcookie('role', '', time() - 3600, "/");
    setcookie('email', '', time() - 3600, "/");
    setcookie("userID", "", time() - 3600, "/");

   
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="Logout.css">
        <title>Logout</title>
    </head>
    <body>
        <div class="container1">
            <h1>You have successfully logged out from your account</h1>
            <p>Want to log in again?</p>
            <button type="button" id="loginButton" class="btn btn-primary btn-block">Login</button>
        </div>

        <script>

            document.getElementById('loginButton').addEventListener('click', function() {
                window.location.href = 'Login.php';  
            });

            // Prevent access via back button
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };

        </script>
    </body>
</html>