<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include("../DB_mypetakom/db.php");

        $role = $_POST['role']; // Student or Staff
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if ($role === 'Student') {

            $verify = "Not Applied";
            $qr_code = "";
            $studentCard = null; // No upload at registration

            $sql = "INSERT INTO student (studentID, studentName, studentEmail, studentPassword, studentCard, verify, qr_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sssssss", $id, $name, $email, $password, $studentCard, $verify, $qr_code);

            if ($stmt->execute()) {
                
                echo "<script>alert('Student registered successfully!'); window.location.href='login.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error registering student: " . $stmt->error . "');</script>";
            }
        } elseif ($role === 'Staff') {
            $staffRole = $_POST['staffRole'];

            $sql = "INSERT INTO staff (staffID, staffName, staffEmail, staffPassword, staffRole) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

             if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sssss", $id, $name, $email, $password, $staffRole);

            if ($stmt->execute()) {
                echo "<script>alert('Staff registered successfully!'); window.location.href='login.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error registering staff: " . $stmt->error . "');</script>";
            }
        }

            if (isset($stmt)) {
                $stmt->close();
            }

    $conn->close();

    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Register | MyPETAKOM</title>
        <link rel="stylesheet" href="Login.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <body>

        <header class="site-header">
            <div class="header-left">
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <img src="/MyPetakom/umpsa-logo.png" alt="UMPSA Logo" class="logo" />
            </div>
             <a href="login.php" class="signup-link">Back to Login</a>
            <div class="site-title">MyPetakom Portal</div>  
        </header>

        <section class="login-section">
            <div class="login-card">
                <h2 class="login-heading">Register</h2>
                <p class="subheading">Create your account</p>

                <form action="register.php" method="POST" class="login-form" enctype="multipart/form-data">
                    <label for="role">Register as:</label>
                    <select name="role" id="role" onchange="toggleFields()" required>
                        <option value="">-- Select Role --</option>
                        <option value="Student">Student</option>
                        <option value="Staff">Staff</option>
                    </select>

                    <label for="id">ID:</label>
                    <input type="text" name="id" id="id" placeholder="Enter your ID" required>

                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" placeholder="Enter your name" required>

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>


                    <div id="staffFields" style="display:none;">
                        <label for="staffRole">Staff Role:</label>
                        <select name="staffRole" id="staffRole" required>
                            <option value="">-- Select Staff Role --</option>
                            <option value="Event Advisor">Event Advisor</option>
                            <option value="PETAKOM Coordinator">PETAKOM Coordinator</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-login">Register</button>
                        <a href="login.php" class="forgot-link">Already have an account? Login here</a>
                    </div>
                </form>
            </div>
        </section>

        <footer class="site-footer">
            &copy; 2025 MyPETAKOM. All rights reserved.
        </footer>

    

        
        <script>
            function toggleFields() {
                const role = document.getElementById("role").value;
                const staffFields = document.getElementById("staffFields");
                const staffRole = document.getElementById("staffRole");

                if (role === "Staff") {
                    staffFields.style.display = "block";
                    staffRole.required = true;
                } else {
                    staffFields.style.display = "none";
                    staffRole.required = false;
                }
            }

            // Ensure correct fields are shown on page load
            window.onload = function () {
                toggleFields();
                document.getElementById("role").addEventListener("change", toggleFields);
            };
        </script>





    </body>
</html>
