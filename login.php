<?php
    session_start();

    $con = mysqli_connect("localhost", "root", "");
    if (!$con) {
        die('Could not connect: ' . mysqli_connect_error());
    }

    mysqli_select_db($con, "db_loginmembership") or die(mysqli_error($con));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $role = $_POST['role'];
        $username = $_POST['username'];
        $password = $_POST['password'];

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="webPage.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - MyPetakom</title>
    
</head>
<body>
  <header>
    <div class="logos">
      <img src="umpsa-logo.png" alt="UMPSA Logo" />
      <img src="petakom-logo.png" alt="PETAKOM Logo" />
    </div>
    <div class="title">MyPetakom</div>
  </header>

  <main>
    <h1>Login</h1>
    <form action="login.php" method="POST">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required />

      <label for="role">Login as:</label>
      <select id="role" name="role" required>
        <option value="">-- Select Role --</option>
        <option value="student">Student</option>
        <option value="advisor">Event Advisor</option>
        <option value="coordinator">PETAKOM Coordinator</option>
      </select>

      <button type="submit" class="submit-button">Login</button>
    </form>
  </main>

  <footer>
    &copy; 2025 MyPetakom | Universiti Malaysia Pahang Al-Sultan Abdullah
  </footer>
</body>
</html>