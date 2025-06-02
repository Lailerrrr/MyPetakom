<?php
require_once '../DB_mypetakom/db.php';

if (!isset($_GET['studentID'])) {
    echo "No student ID provided.";
    exit;
}

$studentID = $_GET['studentID'];

// Query total merit
$sql = "SELECT student.studentName, student.studentID, SUM(merit.totalMerit) AS totalMerit
        FROM merit 
        JOIN student ON merit.studentID = student.studentID
        WHERE student.studentID = ?
        GROUP BY student.studentID, student.studentName";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

$stmt->bind_param("s", $studentID);
if (!$stmt->execute()) {
    echo "Error executing statement: " . $stmt->error;
    exit;
}

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data):
?>
<!DOCTYPE html>
<html>
<head>
    <title>STUDENT MERIT INFORMATION</title>
    <style>
        body {
            font-family: Arial;
            margin: 0; /* Remove default margin */
            background-color: #f8d3e0; /* Light pink background */
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
        }
        .card {
            border: 2px solid #6a0dad; /* Purple border */
            background-color: #e0b0ff; /* Light purple background */
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #6a0dad; /* Purple text for the heading */
        }
        p {
            color: #4b0082; /* Darker purple for paragraph text */
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>STUDENT MERIT INFORMATION</h2>
        <p><strong>NAME:</strong> <?= htmlspecialchars($data['studentName']) ?></p>
        <p><strong>ID:</strong> <?= htmlspecialchars($data['studentID']) ?></p>
        <p><strong>TOTAL MERIT:</strong> <?= htmlspecialchars($data['totalMerit']) ?> points</p>
    </div>
</body>
</html>
<?php
else:
    echo "Student data not found.";
endif;

$stmt->close();
$conn->close();
?>




