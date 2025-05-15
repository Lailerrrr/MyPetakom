<?php

    session_start();

    $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email']);

            $stmt = $con->prepare("SELECT * FROM User WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $_SESSION['reset_email'] = $email; // store email for use in resetPassword.php
                header("Location: resetPassword.php");
                exit();
            } else {
                $message = "Email not found in our records.";
            }
            $stmt->close();

        }
?>


<!DOCTYPE html>
<html>

    <head>

        <meta charset="UTF-8" />
        <link rel="stylesheet" href="login.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Forgot Password - MyPetakom</title>

    </head>

    <body>
        
        <header class="header">

            <div class="logos">
                <img src="umpsa-logo.png" alt="UMPSA Logo" />
                <img src="petakom-logo.png" alt="PETAKOM Logo" />
            </div>

            <div class="title">MyPetakom Student Portal</div>

            <div class="nav-right">
                <a href="login.php">Login</a>
            </div>

        </header>

        <main class="main-content">

            <h1>Forgot Password</h1>
            <h2>Faculty Of Computing</h2>
            <p class="welcome-text">Enter your email to reset your password</p>

           
            <?php 
                if ($message) echo "<p style='color:red;'>$message</p>"; 
            ?>

            <form action="forgot-password.php" method="POST" class="login-form">

                <label>Enter your registered email:</label><br>
                <input type="email" name="email" required><br><br>
                <input type="submit" value="Submit">
            
                <div class="form-actions">
                    <button type="submit">Reset Password</button>
                    <a href="login.php" class="forgot-link">Already have an account?</a>
                </div>

            </form>

        </main>
        
        <footer>
            &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
        </footer>

    </body>

</html>