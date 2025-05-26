<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once '../DB_mypetakom/db.php'; // Ensure this sets $conn = new mysqli(...)

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Basic validation
        if (empty($email) || empty($password)) {
            echo json_encode(["success" => false, "message" => "Please enter both email and password."]);
            exit();
        }

        // Use prepared statements for secure query
        $sql = "SELECT staffID, staffPassword FROM staff WHERE staffEmail = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $row['staffPassword'])) {
                $_SESSION['userID'] = $row['staffID'];
                echo json_encode(["success" => true, "redirectURL" => "../Home/adminHomePage.php"]);
            } else {
                echo json_encode(["success" => false, "message" => "❌ Incorrect password."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "❌ Email not found."]);
        }

        $stmt->close();
        $conn->close();
    }
?>

