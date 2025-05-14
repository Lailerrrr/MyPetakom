<?php
   
  

    if($_SERVER["REQUEST_METHOD"] == "POST"){
      $password = trim($_POST['password']);
      $email = trim($_POST['email']);
      $role = $_POST['role'];

      //Process login logic here


    }
        
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