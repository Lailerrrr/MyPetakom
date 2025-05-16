<?php
   
    session_start();

    // Database connection settings
    $host = "localhost";              // Host name
    $dbUsername = "root";            // Database username
    $dbPassword = "";                // Database password
    $dbName = "db_mypetakom";  // Database name

    // Create connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle login form submission
    if($_SERVER["REQUEST_METHOD"] == "POST"){
      $password = trim($_POST['password']);
      $email = trim($_POST['email']);
      $role = $_POST['role'];

        // Validate input
        if (!empty($email) && !empty($password) && !empty($role)) {
            $sql = "SELECT * FROM login WHERE email = ? AND role = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $role);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if user exists
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
      
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Store user data in session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    if ($role === 'student') {
                        header("Location: student_dashboard.php");
                    } elseif ($role === 'advisor') {
                        header("Location: advisor_dashboard.php");
                    } elseif ($role === 'coordinator') {
                        header("Location: coordinator_dashboard.php");
                    }
                    exit();
                } else {
                    echo "<script>alert('Incorrect password.');</script>";
                }
            } else {
                echo "<script>alert('User not found or role mismatch.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('All fields are required.');</script>";
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
      <title>Login - MyPetakom</title>
        
    </head>
    <body>
      <header class="header">

        <div class="logos">
          <img src="umpsa-logo.png" alt="UMPSA Logo" />
          <img src="petakom-logo.png" alt="PETAKOM Logo" />
        </div>

        <div class="title">MyPetakom Student Portal</div>

        <div class="nav-right">
          <a href="signup.php">Sign Up</a>
        </div>

      </header>

      <main class="main-content">

        <h1>Login</h1>
        <h2>Faculty Of Computing</h2>
        <p class="welcome-text">Welcome to MyPetakom</p>

        <form action="login.php" method="POST" class="login-form">

          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required />

          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required />

          <label for="role">Role:</label>

          <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="student">Student</option>
            <option value="advisor">Event Advisor</option>
            <option value="coordinator">PETAKOM Coordinator</option>
          </select>

          <div class="form-actions">
            <button type="submit">Login</button>
            <a href="forgot-password.php" class="forgot-link">Forgot Password</a>
          </div>

        </form>
      </main>

      <footer>
        &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
      </footer>
    </body>
</html>