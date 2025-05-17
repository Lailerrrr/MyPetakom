<?php
    session_start();
    $con = mysqli_connect("localhost", "root", "", "mypetakom_db");

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $roleInput = strtolower(trim($_POST['role']));
        $email = trim($_POST['email']);

        switch ($roleInput) {
            case 'student':
                $table = 'student';
                $emailColumn = 'studentEmail';
                break;
            case 'event advisor':
                $table = 'advisor';
                $emailColumn = 'advisorEmail';
                break;
            case 'petakom coordinator':
                $table = 'administrator';
                $emailColumn = 'adminEmail';
                break;
            default:
                $message = "Invalid role selected.";
                $table = '';
                break;
        }

        if (!empty($table)) {
            $stmt = $con->prepare("SELECT * FROM $table WHERE $emailColumn = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // You can implement email sending or reset link generation here.
                $message = "A password reset process should be sent to your email. (Feature coming soon)";
            } else {
                $message = "Email not found in selected role.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Forgot Password - MyPetakom</title>
        <link rel="stylesheet" href="Login.css" />
    </head>
    <body>

        <main class="login-section">
            <div class="login-card">
                <h1 class="login-heading">Forgot Password</h1>
                <p class="welcome-text">Enter your email and role to reset your password</p>

                <form action="" method="POST">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>

                    <br>
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="Student">Student</option>
                        <option value="Event Advisor">Event Advisor</option>
                        <option value="PETAKOM Coordinator">PETAKOM Coordinator</option>
                    </select>

                    <div class="form-actions">
                        <button type="submit" class="btn-login">Reset Password</button>
                    </div>
                </form>

                <?php if ($message): ?>
                    <p style="color: red; margin-top: 10px;"><?php echo $message; ?></p>
                <?php endif; ?>

                <p><a href="Login.php" class="forgot-link">Back to Login</a></p>
            </div>
        </main>

    </body>
</html>
