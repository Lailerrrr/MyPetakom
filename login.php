<?php
    session_start();

    $con = mysqli_connect("localhost", "root", "");
    if (!$con) {
        die('Could not connect: ' . mysqli_connect_error());
    }

    mysqli_select_db($con, "db_loginmembership") or die(mysqli_error($con));

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - MyPetakom</title>
    <style>
        body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #ffe6f0;
        color: #333;
        }

        header {
        background-color: #ffb6c1;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logos {
        display: flex;
        align-items: center;
        gap: 20px;
        }

        .logos img {
        height: 60px;
        }

        .title {
        font-size: 28px;
        font-weight: bold;
        }

        main {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 60px 20px;
        }

        h1 {
        margin-bottom: 20px;
        }

        form {
        background-color: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        max-width: 400px;
        width: 100%;
        }

        label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        }

        input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        }

        .submit-button {
        background-color: #ff69b4;
        border: none;
        color: white;
        padding: 12px;
        font-size: 16px;
        width: 100%;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        }

        .submit-button:hover {
        background-color: #ff1493;
        }

        footer {
        background-color: #ffb6c1;
        text-align: center;
        padding: 15px;
        position: fixed;
        bottom: 0;
        width: 100%;
        color: #333;
        }
    </style>
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