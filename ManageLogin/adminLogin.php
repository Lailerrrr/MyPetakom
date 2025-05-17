<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $con = new mysqli("localhost", "root", "", "mypetakom_db");

        if ($con->connect_error) {
            echo json_encode(["success" => false, "message" => "Connection failed: " . $con->connect_error]);
            exit();
        }

        // Escape the input data
        $email = $con->real_escape_string($email);
        $password = $con->real_escape_string($password);

        $sql = "SELECT * FROM administrator WHERE adminEmail='$email' AND adminPassword='$password'";
        $result = $con->query($sql);

        if ($result->num_rows == 1) {
            // Successful login
            $row = $result->fetch_assoc();
            $_SESSION['userID'] = $row['adminID']; // Store userID in session

            // Determine redirect URL
            $redirectURL = "../Home/adminHomePage.php";

            echo json_encode(["success" => true, "redirectURL" => $redirectURL]);
        } else {
            // Failed login
            echo json_encode(["success" => false, "message" => "Incorrect username or password. Please try again."]);
        }

        $con->close();
    }
?>
