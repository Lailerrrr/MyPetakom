<?php
    session_start();
    session_unset();
    session_destroy();

    // Clear cookies
    setcookie('role', '', time() - 3600, "/");
    setcookie('email', '', time() - 3600, "/");
    setcookie("userID", "", time() - 3600, "/");

    header("Location: Login.php");
    exit;
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
            <p>A bit more to do?</p>
            <button type="button" id="loginButton" class="btn btn-primary btn-block">Login</button>
        </div>

        <script>
            document.getElementById('loginButton').addEventListener('click', function() {
                window.location.href = 'Login.php';  
            });
        </script>
    </body>
</html>