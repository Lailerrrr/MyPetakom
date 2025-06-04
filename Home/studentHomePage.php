<?php
session_start();

// 🔒 Prevent page from being cached after logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

$email = $_SESSION['email'];
$name = "";

// Get student name and ID
$sql = "SELECT studentName, studentID FROM student WHERE studentEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($name, $student_id);
$stmt->fetch();
$stmt->close();

function getSemesterAndAcademicYear($studentId) {
    // Step 1: Extract intake year from student ID
    $intakeYearSuffix = substr($studentId, 2, 2);
    $intakeYear = 2000 + (int)$intakeYearSuffix;

    // Step 2: Intake starts on October 1st of the intake year
    $intakeStart = new DateTime("$intakeYear-10-01");
    $currentDate = new DateTime();

    // Step 3: Loop to determine the semester based on 6-month blocks
    $semester = 0;
    for ($i = 1; $i <= 8; $i++) {
        $semStart = (clone $intakeStart)->modify('+' . (($i - 1) * 6) . ' months');
        $semEnd = (clone $semStart)->modify('+6 months');

        if ($currentDate >= $semStart && $currentDate < $semEnd) {
            $semester = $i;
            break;
        }
    }

    // If after all 8 semesters
    if ($semester === 0 && $currentDate >= $semEnd) {
        $semester = 8;
    }

    // Step 4: Determine academic year (October–September)
    $currentYear = (int)$currentDate->format('Y');
    $currentMonth = (int)$currentDate->format('m');
    if ($currentMonth >= 10) {
        $academicYear = $currentYear . '/' . ($currentYear + 0);
    } else {
        $academicYear = ($currentYear - 1) . '/' . $currentYear;
    }

    return [
        'semester' => $semester,
        'academic_year' => $academicYear,
    ];
}
$studentInfo = getSemesterAndAcademicYear($student_id);

// Get merit records
$merits = [];
$meritQuery = "SELECT semester, academicYear, totalMerit FROM merit WHERE studentID = ? ORDER BY academicYear, semester";
$meritStmt = $conn->prepare($meritQuery);
$meritStmt->bind_param("s", $student_id);
$meritStmt->execute();
$meritResult = $meritStmt->get_result();

while ($row = $meritResult->fetch_assoc()) {
    $merits[] = $row;
}

$meritStmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="studentHomePage.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
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
            <li><a href="/MyPetakom/User/Profiles.php">Profile</a></li>
            <li><a href="/MyPetakom/membership/applyMembership.php">Apply Membership</a></li>
            <li><a href="/MyPetakom/membership/viewMembership.php">View Membership</a></li>
            <li><a href="/MyPetakom/Attendance/event_register.php">Event Attendance</a></li>
            <li><a href="/MyPetakom/Merit/MeritClaimStudent.php">Merit Claim</a></li>
            <li><a href="/MyPetakom/Merit/ScanQR.php">Scan QR</a></li>
            <li><a href="/MyPetakom/ManageLogin/Logout.php">Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <div class="dashboard-indicator" style="margin-bottom: 25px; max-width: 600px; display: flex; justify-content: space-between; color: #ffd1e8; font-weight: 600;">
        <span class="dashboard-user">Logged in as: <strong><?php echo htmlspecialchars($email); ?></strong></span>
    </div>
    <header class="main-header">
        <h1>Welcome back, <span class="username"><?php echo htmlspecialchars($name); ?></span>!</h1>
        <p>Your activity summary & updates</p>
        <div style="margin-top: 10px; font-size: 16px; color: #fdd;">
            <strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?>
        </div>
    </header>

    <div style="margin: 30px 0; padding: 20px; background-color: #222; border-radius: 12px; color: #fff; max-width: 600px; box-shadow: 0 0 10px rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 15px; color: #ffd1e8;">📊 Cumulative Merit Summary</h2>

        <?php if (count($merits) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #333;">
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Semester</th>
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Academic Year</th>
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Total Merit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($merits as $m): ?>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #444;"><?php echo htmlspecialchars($m['semester']); ?></td>
                            <td style="padding: 8px; border-bottom: 1px solid #444;"><?php echo htmlspecialchars($m['academicYear']); ?></td>
                            <td style="padding: 8px; border-bottom: 1px solid #444;"><?php echo htmlspecialchars($m['totalMerit']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No merit records available yet.</p>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background-color: #333;">
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Semester</th>
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Academic Year</th>
                        <th style="padding: 8px; border-bottom: 1px solid #555;">Total Merit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #444;"><?php echo $studentInfo['semester']; ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #444;"><?php echo $studentInfo['academic_year']; ?></td>
                        <td style="padding: 8px; border-bottom: 1px solid #444;">0</td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Merit Points Over Semesters Graph -->
    <div style="margin: 30px 0; padding: 20px; background-color: #222; border-radius: 12px; color: #fff; max-width: 600px; box-shadow: 0 0 10px rgba(255,255,255,0.1);">
        <h2 style="margin-bottom: 15px; color: #ffd1e8;">📈 Merit Points Over Semesters</h2>
        <canvas id="meritChart" style="width:100%; max-width:600px;"></canvas>
    </div>

    <div class="slideshow-container">
        <div class="mySlides fade"><div class="numbertext">1 / 5</div><img src="/MyPetakom/p1.jpg" style="width:100%"></div>
        <div class="mySlides fade"><div class="numbertext">2 / 5</div><img src="/MyPetakom/p2.jpg" style="width:100%"></div>
        <div class="mySlides fade"><div class="numbertext">3 / 5</div><img src="/MyPetakom/p3.jpg" style="width:100%"></div>
        <div class="mySlides fade"><div class="numbertext">4 / 5</div><img src="/MyPetakom/p4.jpg" style="width:100%"></div>
        <div class="mySlides fade"><div class="numbertext">5 / 5</div><img src="/MyPetakom/p5.jpg" style="width:100%"></div>
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <br>
    <div style="text-align:center">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
        <span class="dot" onclick="currentSlide(4)"></span>
        <span class="dot" onclick="currentSlide(5)"></span>
    </div>
</main>

<!-- Scripts -->
<script src="studentHomePage.js"></script>

<!-- Force logout on browser back -->
<script>
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        window.location.href = "../ManageLogin/logout.php";
    };
</script>

<!-- Prepare data for the graph -->
<script>
    const semesters = [];
    const totalMerits = [];

    <?php foreach ($merits as $m): ?>
        semesters.push("Semester <?php echo htmlspecialchars($m['semester']); ?>");
        totalMerits.push(Number(<?php echo htmlspecialchars($m['totalMerit']); ?>)); // Ensure it's a number
    <?php endforeach; ?>

    console.log('Semesters:', semesters); // Debugging: Check semesters
    console.log('Total Merits:', totalMerits); // Debugging: Check total merits

    const ctx = document.getElementById('meritChart').getContext('2d');
    const meritChart = new Chart(ctx, {
        type: 'bar', // Change to 'line' if you prefer a line chart
        data: {
            labels: semesters,
            datasets: [{
                label: 'Total Merit Points',
                data: totalMerits,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>





