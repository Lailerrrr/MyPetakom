<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "mypetakom_db");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = trim($_POST['studentID']);
    $studentName = trim($_POST['studentName']);
    $studentEmail = trim($_POST['studentEmail']);
    $studentPassword = $_POST['studentPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($studentPassword !== $confirmPassword) {
        $error = "❌ Passwords do not match.";
    } else {
        $hashedPassword = password_hash($studentPassword, PASSWORD_DEFAULT);
        $verify = "Pending";
        $studentCard = "";
        $qr_code = "0";

        $check = $con->prepare("SELECT studentID FROM student WHERE studentEmail = ?");
        $check->bind_param("s", $studentEmail);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "❌ An account with this email already exists.";
        } else {
            $stmt = $con->prepare("INSERT INTO student (studentID, studentName, studentEmail, studentPassword, studentCard, verify, qr_code)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $studentID, $studentName, $studentEmail, $hashedPassword, $studentCard, $verify, $qr_code);

            if ($stmt->execute()) {
                // Auto-login after registration
                $_SESSION['studentID'] = $studentID;
                $_SESSION['studentName'] = $studentName;
                $_SESSION['studentEmail'] = $studentEmail;
                $_SESSION['email'] = $studentEmail; // Needed by studentHomePage
                $_SESSION['role'] = 'student';      // Needed by studentHomePage
                $_SESSION['verify'] = $verify;

                header("Location: ../Home/studentHomePage.php");
                exit();
            } else {
                $error = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }

    $con->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MyPetakom</title>
    <link rel="stylesheet" href="Login.css"> <!-- Reuse login.css -->
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
        <h1 class="login-heading">Register</h1>
        <h2 class="subheading">Faculty of Computing</h2>
        <p class="welcome-text">Create your MyPetakom account</p>

        <?php
            if (!empty($error)) echo "<p style='color:red;'>$error</p>";
            if (!empty($success)) echo "<p style='color:lightgreen;'>$success</p>";
        ?>

        <form class="login-form" method="POST" action="register.php">
            <label for="studentID">Student ID</label>
            <input type="text" name="studentID" id="studentID" required>

            <label for="studentName">Full Name</label>
            <input type="text" name="studentName" id="studentName" required>

            <label for="studentEmail">Email</label>
            <input type="email" name="studentEmail" id="studentEmail" required>

            <label for="studentPassword">Password</label>
            <input type="password" name="studentPassword" id="studentPassword" required>

            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="confirmPassword" id="confirmPassword" required>

            <div class="form-actions">
                <button type="submit" class="btn-login">Register</button>
            </div>
        </form>

        <p class="welcome-text">Already have an account? <a href="Login.php" class="signup-link">Login here</a>.</p>
    </div>
</main>

<footer class="site-footer">
    &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
</footer>

</body>
</html>
