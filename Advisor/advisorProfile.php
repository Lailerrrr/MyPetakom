<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$advisorID = $_SESSION['userID'];

// Fetch advisor details
$advisorQuery = $conn->prepare("SELECT advisorName, advisorEmail, advisor_phoneNum, advisor_department FROM advisor WHERE advisorID = ?");
$advisorQuery->bind_param("s", $advisorID);
$advisorQuery->execute();
$advisorQuery->bind_result($name, $email, $phone, $department);
$advisorQuery->fetch();
$advisorQuery->close();

// Fetch advisor's events
$eventQuery = $conn->prepare("SELECT eventName, eventDate, status FROM event WHERE advisorID = ?");
$eventQuery->bind_param("s", $advisorID);
$eventQuery->execute();
$eventResult = $eventQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advisor Profile - MyPetakom</title>
    <link rel="stylesheet" href="../sidebar.css">
    <!-- <link rel="stylesheet" href="../Module2]/eventRegistration.css"> -->
     <link rel="stylesheet" href="../Advisor/advisorProfile.css">
    <!-- <style>
        .profile-section {
            padding: 20px;
        }
        .profile-section h2 {
            margin-bottom: 10px;
        }
        .profile-details, .event-list {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
    </style> -->
</head>
<body>
    <div class="container">
        <?php include '../sidebar.php'; ?>

        <main class="main-content">
            <div class="profile-section">
                <h2>👤 Advisor Profile</h2>
                <div class="profile-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php
$eventQuery->close();
$conn->close();
?>
