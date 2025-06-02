<?php
require_once '../DB_mypetakom/db.php';

if (!isset($_GET['studentID'])) {
    echo "No student ID provided.";
    exit;
}

$studentID = $_GET['studentID'];

// Query total merit
$sql = "SELECT studentName, studentID, SUM(totalMerit) AS totalMerit
        FROM merit 
        JOIN student ON merit.studentID = student.studentID
        WHERE student.studentID = ?
        GROUP BY student.studentID, studentName";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Merit Info</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .card {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            margin: auto;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Student Merit Info</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($data['studentName']) ?></p>
        <p><strong>ID:</strong> <?= htmlspecialchars($data['studentID']) ?></p>
        <p><strong>Total Merit:</strong> <?= htmlspecialchars($data['totalMerit']) ?> points</p>
    </div>
</body>
</html>
<?php
else:
    echo "Student data not found.";
endif;
?>



