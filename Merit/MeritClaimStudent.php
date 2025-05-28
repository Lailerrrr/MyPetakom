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

//Handle new claim submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['StdID'] ?? '';
    $event_id = $_POST['EventID'] ?? '';
    $document = $_FILES['document']['name'] ?? '';

    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($document);

        if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
            $claimStatus = "Pending";
            $approvalDate = null;
            $approvalBy = null;

            $sql = "INSERT INTO meritclaim (claimID, claimStatus, claimLetter, approval_date, approval_by, eventID, studentID)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $claimID = uniqid("CLM"); // generate unique claim ID
            $stmt->bind_param("sssssss", $claimID, $claimStatus, $document, $approvalDate, $approvalBy, $event_id, $student_id);

            if ($stmt->execute()) {
                echo "<script>alert('Claim submitted successfully.');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "❌ Sorry, there was an error uploading your file.";
        }
    } else {
        echo "❌ No file uploaded.";
    }  
}

// Fetch claim list
$sql = "SELECT * FROM meritclaim WHERE studentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Merit Claim - MyPetakom</title>
    <link rel="stylesheet" href="MeritClaimStudent.css" /> <!-- Your Pretty Savage CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="../User/studentProfile.php">Profile</a></li>
                <li><a href="../membership/applyMembership.php">Apply Membership</a></li>
                <li><a href="../membership/viewMembership.php">View Membership</a></li>
                <li><a href="../Attendance/event_register.php">Event Attendance</a></li>
                <li><a href="../Merit/MeritClaimStudent.php">Merit Claim</a></li>
                <li><a href="../Merit/ScanQR.php">Scan QR</a></li>
                <li><a href="../ManageLogin/Logout.php">Logout</a></li>
            </ul>
    </nav>
</aside>

<main class="main-content">
    <header class="main-header">
        <h1>MANAGE MISSING MERIT CLAIM</h1><br>
        <p>Welcome, <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($student_id); ?>)</p>
        
    
    <div class="claims-table">
        <h2>YOUR MERIT CLAIMS</h2></p>
        </header>
        <table border="1" cellpadding="5">
    <tr>
        <th>Claim ID</th>
        <th>Event ID</th>
        <th>Letter</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['claimID']) ?></td>
            <td><?= htmlspecialchars($row['eventID']) ?></td>
            <td><a href="uploads/<?= htmlspecialchars($row['claimLetter']) ?>" target="_blank">View</a></td>
            <td><?= htmlspecialchars($row['claimStatus']) ?></td>
            <td>
    <?php if ($row['claimStatus'] === 'Pending') { ?>
        <a href="EditClaim.php?id=<?= $row['claimID'] ?>">Edit</a>
        <a href="DeleteClaim.php?id=<?= $row['claimID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
    <?php } else { ?>
        <span style="color:gray;">Locked</span>
    <?php } ?>
</td>

        </tr>
    <?php endwhile; ?>
</table>
        </table>
    </div><br>
    <div>
        <form action="" method="post" enctype="multipart/form-data">
    <label for="Stdid">Student ID:</label>
    <input type="text" id="Stdid" name="StdID" required><br><br>

    <label for="EID">Event ID:</label>
    <input type="text" id="EID" name="EventID" required><br><br>

    <label for="file-upload">Upload Official Letter (PDF): </label> 
    <input id="file-upload" type="file" name="document" accept=".pdf" required><br><br>

    <input type="submit" value="Submit"> 
</form>
</div>




</main>

<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const claimId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteClaim.php?id=${claimId};
            }
        });
    });
});
</script>

</body>
</html>