<?php
    session_start();
    require_once '../DB_mypetakom/db.php';

    if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'student') {
        header("Location: ../ManageLogin/login.php");
        exit;
    }

    $studentID = $_SESSION['userID'];
    $studentName = "";
    $successMsg = "";
    $errorMsg = "";

    // Fetch student details
    $stmt = $conn->prepare("SELECT studentName FROM student WHERE studentID = ?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $stmt->bind_result($studentName);
    $stmt->fetch();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $status = "Pending";
        $uploadDir = "../uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['studentCard']) && $_FILES['studentCard']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['studentCard']['tmp_name'];
            $fileName = basename($_FILES['studentCard']['name']);
            $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png'];

            if (!in_array($fileExt, $allowedExt)) {
                $errorMsg = "❌ Only JPG, JPEG, and PNG files are allowed.";
            } elseif ($_FILES['studentCard']['size'] > 5 * 1024 * 1024) {
                $errorMsg = "❌ File is too large. Max 5MB.";
            } else {
                $newFileName = uniqid("card_", true) . "." . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $membershipID = uniqid("M");

                    $stmt = $conn->prepare("INSERT INTO membership (membershipID, studentCard, status, studentID) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $membershipID, $newFileName, $status, $studentID);

                    if ($stmt->execute()) {
                        $successMsg = "✅ Application submitted successfully.";
                    } else {
                        $errorMsg = "❌ Database error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $errorMsg = "❌ Failed to upload file.";
                }
            }
        } else {
            $errorMsg = "❌ Please upload your student card.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Apply Membership - MyPetakom</title>
        <link rel="stylesheet" href="../Home/studentHomePage.css">
        <link rel="stylesheet" href="applyMembership.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    </head>
    
    <body>
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <div class="sidebar-text">
                    <h2>MyPetakom</h2>
                    <p class="role-label">🎓 Student</p>
                </div>
            </div>
            
            <nav class="menu">
                <ul>
                    <li><a href="#">Profile</a></li>
                    <li><a href="../membership/applyMembership.php" class="active">Apply Membership</a></li>
                    <li><a href="../membership/viewMembership.php"  >View Membership</a></li>
                    <li><a href="../Attendance/event_register.php">Attendance Registration</a></li>
                    <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                    <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                    <li><a href="../ManageLogin/Logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <h2>📝 Apply for PETAKOM Membership</h2>

            <?php if ($successMsg): ?>
                <p class="success"><?php echo $successMsg; ?></p>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <p class="error"><?php echo $errorMsg; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label>Name:</label>
                <input type="text" value="<?= htmlspecialchars($studentName) ?>" readonly>

                <label>Student ID:</label>
                <input type="text" value="<?= htmlspecialchars($studentID) ?>" readonly>
                
                <label for="studentCard">Upload Student Card (JPG/PNG):</label>
                <input type="file" name="studentCard" id="studentCard" accept=".jpg,.jpeg,.png" required>

                <button type="submit">Submit Application</button>
            </form>
        </main>
    </body>
</html>
