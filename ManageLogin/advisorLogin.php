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

            
        // Prepare statement to prevent SQL injection
        $stmt = $con->prepare("SELECT advisorID, advisorPassword FROM advisor WHERE advisorEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verify password - assuming password is hashed in DB
            if (password_verify($password, $row['advisorPassword'])) {
                $_SESSION['userID'] = $row['advisorID'];

                // You can also store role or email if needed
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'event advisor';

                $redirectURL = "../Home/advisorHomePage.php";
                echo json_encode(["success" => true, "redirectURL" => $redirectURL]);
            } else {
                echo json_encode(["success" => false, "message" => "Incorrect username or password. Please try again."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect username or password. Please try again."]);
        }

        $stmt->close();
        $con->close();
    }
    
?>