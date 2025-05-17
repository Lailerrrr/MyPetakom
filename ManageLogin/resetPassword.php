<?php
    session_start();
    $con = mysqli_connect("localhost", "root", "", "mypetakom_db");

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $message = "";
    $email = $_GET['email'] ?? '';
    $role = strtolower($_GET['role'] ?? '');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $role = strtolower($_POST['role']);
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $message = "Passwords do not match.";
        } else {
            switch ($role) {
                case 'student':
                    $table = 'student';
                    $emailColumn = 'studentEmail';
                    $passColumn = 'studentPassword';
                    break;
                case 'event advisor':
                    $table = 'advisor';
                    $emailColumn = 'advisorEmail';
                    $passColumn = 'advisorPassword';
                    break;
                case 'petakom coordinator':
                    $table = 'administrator';
                    $emailColumn = 'adminEmail';
                    $passColumn = 'adminPassword';
                    break;
                default:
                    $message = "Invalid role.";
                    $table = '';
                    break;
            }

            if (!empty($table)) {
                $hashed = $newPassword; // You can hash it if needed
                $stmt = $con->prepare("UPDATE $table SET $passColumn = ? WHERE $emailColumn = ?");
                $stmt->bind_param("ss", $hashed, $email);
                if ($stmt->execute()) {
                    $message = "Password successfully updated. <a href='Login.php'>Click here to login</a>.";
                } else {
                    $message = "Failed to update password.";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reset Password - MyPetakom</title>
        <link rel="stylesheet" href="Login.css">
    </head>
    <body>

    <main class="login-section">
        <div class="login-card">
            <h1 class="login-heading">Reset Password</h1>
            <form action="" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

                <label for="new_password">New Password</label><br>
                <input type="password" name="new_password" required><br>

                <label for="confirm_password">Confirm New Password</label><br>
                <input type="password" name="confirm_password" required>

                <div class="form-actions">
                    <button type="submit" class="btn-login">Update Password</button>
                </div>
            </form>

            <?php if ($message): ?>
                <p style="color: red;"><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
    </main>

    </body>
</html>
