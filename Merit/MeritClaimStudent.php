<?php
    session_start();
    require_once '../DB_mypetakom/db.php'; // Adjust path if needed

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    $email = $_SESSION['email'];
    $name = "";
    $student_id = "";

    // Get student info
    $sql = "SELECT studentName, studentID FROM student WHERE studentEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($name, $student_id);
    $stmt->fetch();
    $stmt->close();

    // Dummy event details (replace with DB query if needed)
    $event = [
        'name' => 'Tech Talk 2025',
        'description' => 'A sharing session with industry professionals about current trends in tech.',
        'date' => '2025-06-10',
        'venue' => 'Library Auditorium A',
        'qr_image' => 'qr_placeholder.png' // Replace with real QR generation
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Event Attendance Registration - MyPetakom</title>
    <link rel="stylesheet" href="MeritClaimStudent.css" /> <!-- Your Pretty Savage CSS -->
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
            <li><a href="#">Manage Membership</a></li>
            <li><a href="#" class="active">Attendance Registration</a></li>
            <li><a href="#">Merit Claim</a></li>
            <li><a href="../ManageLogin/Logout.php">Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>Attendance Registration</h1>
        <p>Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($student_id); ?>)</p>
    </header>



    </div>
</main>

</body>
</html>
