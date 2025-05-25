<?php
    session_start();

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");


    session_unset();
    session_destroy();

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

            if (window.history && window.history.pushState) {
                window.history.pushState(null, null, window.location.href);
                window.onpopstate = function () {
                    window.location.href = 'Login.php';
                };
            }

        </script>
    </body>
</html>