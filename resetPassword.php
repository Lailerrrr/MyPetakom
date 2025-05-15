<?php
    session_start();

    // Placeholder logic for when the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST["email"]);
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];

        if ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            // Here you'd process the password reset (e.g., update database)
            // For now, simulate success
            $success = "Your password has been reset successfully.";
        }

    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="login.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Reset Password - MyPetakom</title>
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

            <h1>Reset Your Password</h1>
            <p class="welcome-text">Enter your email and new password.</p>

             <?php if (!empty($error)) : ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php elseif (!empty($success)) : ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>

            
            
            <form action="resetPassword.php" method="POST" class="login-form">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                 <div class="form-actions">
                    <button type="submit">Reset Password</button>
                    <a href="login.php" class="forgot-link">Back to Login</a>
                </div>

            </form>
            
            
        </main>

        <footer>
            &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
        </footer>

    </body>
</html>