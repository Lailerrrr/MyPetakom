<?php
    session_start();

    $con = mysqli_connect("localhost", "root", "");
    if (!$con) {
        die('Could not connect: ' . mysqli_connect_error());
    }

    mysqli_select_db($con, "mypetakom_db") or die(mysqli_error($con));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Normalize role input to lowercase
        $roleInput = strtolower(trim($_POST['role']));
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check the role and select the corresponding table
        switch ($roleInput) {
            case 'student':
                $table = 'student';
                $emailColumn = 'studentEmail';
                $passColumn = 'studentPassword';
                $idColumn = 'studentID';
                $homePage = '../Home/studentHomePage.php';
                break;
            case 'event advisor':
                $table = 'advisor';
                $emailColumn = 'advisorEmail';
                $passColumn = 'advisorPassword';
                $idColumn = 'advisorID';
                $homePage = '../Home/advisorHomePage.php';
                break;
            case 'petakom coordinator':
                $table = 'administrator';
                $emailColumn = 'adminEmail';
                $passColumn = 'adminPassword';
                $idColumn = 'adminID';
                $homePage = '../Home/adminHomePage.php';
                break;
            default:
                $error = "Invalid role selected.";
                break;
        }


        if (!isset($error)) {
            $stmt = $con->prepare("SELECT $idColumn FROM $table WHERE $emailColumn = ? AND $passColumn = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                // Fetch user ID
                $row = $result->fetch_assoc();
                $userID = $row[$idColumn];

                // Store role, username, and user ID in session
                $_SESSION['role'] = $roleInput;
                $_SESSION['email'] = $email;
                $_SESSION['userID'] = $userID;

                // Set cookies to remember the user for 7 days
                setcookie('role', $roleInput, time() + (86400 * 7), "/");
                setcookie('email', $email, time() + (86400 * 7), "/");
                setcookie('userID', $userID, time() + (86400 * 7), "/");

                header("Location: $homePage");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
    }

?>






<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Login - MyPetakom</title>
        <link rel="stylesheet" href="Login.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    </head>

    <body>

        <header class="site-header">
            <div class="header-left">
                <img src="petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <img src="umpsa-logo.png" alt="UMPSA Logo" class="logo" />
            </div>

            <div class="site-title">MyPetakom Student Portal</div>

            <div class="header-right">
                <a href="signup.php" class="signup-link">Sign Up</a>
            </div>
        </header>

        <main class="login-section">

            <div class="login-card">

                <h1 class="login-heading">Login</h1>
                <h2 class="subheading">Faculty of Computing</h2>
                <p class="welcome-text">Welcome to MyPetakom</p>

                <form class="login-form" action="#" method="POST">

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="Student">Student</option>
                        <option value="Event Advisor">Event Advisor</option>
                        <option value="PETAKOM Coordinator">PETAKOM Coordinator</option>
                    </select>

                    <div class="form-actions">
                        <button type="submit" class="btn-login">Login</button>
                    </div>
                </form>
                <?php
                    if (isset($error)) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                ?>
                <p><a href="forgot-password.php" class="forgot-link">Forgot Password?</a></p>
            </div>
        </main>

        <footer class="site-footer">
            &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
        </footer>

    </body>
</html>
