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
        $email = strtolower(trim($_POST['email']));
        $password = $_POST['password'];

        // Define default values
        $table = "";
        $emailColumn = "";
        $passColumn = "";
        $idColumn = "";
        $homePage = "";   

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
            case 'petakom coordinator':
                $table = 'staff';
                $emailColumn = 'staffEmail';
                $passColumn = 'staffPassword';
                $idColumn = 'staffID';
                $homePage = ($roleInput === 'event advisor') 
                        ? '../Home/advisorHomePage.php' 
                        : '../Home/adminHomePage.php';
                break;
            default:
                $error = "Invalid role selected.";
        }


        if (!isset($error)) {
            // Use correct fields in SELECT
            $selectFields = ($table === 'staff') ? "$idColumn, $passColumn, staffRole" : "$idColumn, $passColumn, studentName";
            $stmt = $con->prepare("SELECT $selectFields FROM $table WHERE $emailColumn = ?");
            if (!$stmt) {
                die("Prepare failed: " . $con->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
                if ($result && $result->num_rows === 1) {
                    // Fetch user ID
                    $row = $result->fetch_assoc();


                // Debug - remove these after confirming it works
                
                echo "<pre>";
                var_dump($row);
                var_dump("Password input: " . $password);
                var_dump("Password hash DB: " . $row[$passColumn]);
                var_dump("Password verify: " . password_verify($password, $row[$passColumn]));
                echo "</pre>";
                



                    if (password_verify($password, $row[$passColumn])) {
                    // Set session and cookies
                    $_SESSION['role'] = $roleInput;
                    $_SESSION['email'] = $email;
                    $_SESSION['userID'] =  $row[$idColumn];

                     if ($table === 'student') {
                        $_SESSION['studentName'] = $row['studentName'];
                    } else {
                        $_SESSION['staffRole'] = $row['staffRole'];
                    }


                    // Set cookies to remember the user for 7 days
                    setcookie('role', $roleInput, time() + (86400 * 7), "/");
                    setcookie('email', $email, time() + (86400 * 7), "/");
                    setcookie('userID', $row[$idColumn], time() + (86400 * 7), "/");

                    header("Location: $homePage");
                    exit;

                    } else {
                        $error = "Invalid email or password.";
                    }
                } else {
                    $error = "No user found with that email.";
                }
                    
                $stmt->close();
            }
        }
    $con->close();


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
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <img src="/MyPetakom/umpsa-logo.png" alt="UMPSA Logo" class="logo" />
            </div>
            <div class="site-title">MyPetakom Portal</div>
        </header>

        <main class="login-section">
            <div class="login-card">
                <h1 class="login-heading">Login</h1>
                <h2 class="subheading">Faculty of Computing</h2>
                <p class="welcome-text">Welcome to MyPetakom</p>

                <form class="login-form" action="login.php" method="POST">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="student">Student</option>
                        <option value="event advisor">Event Advisor</option>
                        <option value="petakom coordinator">PETAKOM Coordinator</option>
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

                <p><a href="forgotPassword.php" class="forgot-link">Forgot Password?</a></p>
            </div>
        </main>

        <footer class="site-footer">
            &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
        </footer>

    </body>
</html>
