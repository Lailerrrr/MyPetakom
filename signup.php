

<?php
session_start();

$success = "";
$error = "";

// Database connection settings
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "db_mypetakom";

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = trim($_POST['studentID']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate required fields
    if (!empty($studentID) && !empty($name) && !empty($email) && !empty($password) && !empty($role)) {

        // Check if email already exists
        $checkSql = "SELECT userID FROM login WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        if (!$checkStmt) {
            die("Prepare failed: " . $conn->error);
        }
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash the password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $insertSql = "INSERT INTO login (studentID, name, email, password, role) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if (!$insertStmt) {
                die("Prepare failed: " . $conn->error);
            }
            $insertStmt->bind_param("sssss", $studentID, $name, $email, $hashedPassword, $role);

            if ($insertStmt->execute()) {
                $success = "Account created successfully. <a href='login.php'>Click here to login</a>";
            } else {
                $error = "Failed to register. Please try again.";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    } else {
        $error = "All fields are required.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8" />
        <link rel="stylesheet" href="login.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Sign Up - MyPetakom</title>

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

            <h1>Sign Up</h1>
            <h2>Faculty of Computing</h2>
            <p class="welcome-text">Create your MyPetakom account</p>

            <?php
                if ($success) echo "<p style='color: green;'>$success</p>";
                if ($error) echo "<p style='color: red;'>$error</p>";
            ?>

            <form method="POST" action="signup.php" class="login-form">

                <label for="studentID">Student ID:</label>
                <input type="text" id="studentID" name="studentID" required />
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required />

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />

                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="student">Student</option>
                    <option value="Event advisor">Event Advisor</option>
                    <option value="Petakom Coordinator">PETAKOM Coordinator</option>
                </select>

                <div class="form-actions">
                    <button type="submit">Sign Up</button>
                    <a href="login.php" class="forgot-link">Already have an account?</a>
                </div>

            </form>

        </main>

        <footer>
            &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah

        </footer>
        
    </body>
</html>
