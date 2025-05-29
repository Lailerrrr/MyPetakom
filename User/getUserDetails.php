<?php
require_once '../DB_mypetakom/db.php';

header('Content-Type: application/json');

// Optional: Enable CORS if frontend is hosted elsewhere
// header('Access-Control-Allow-Origin: *');

// Check for required parameters
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];

// Validate the type parameter
$validTypes = ['student', 'staff'];
if (!in_array($type, $validTypes)) {
    echo json_encode(['error' => 'Invalid user type']);
    exit();
}

// Prepare the appropriate SQL query based on user type
if ($type === 'student') {
    $query = "SELECT studentID AS id, studentName AS name, studentEmail AS email, verify AS status, lastLogin FROM student WHERE studentID = ?";
} else {
    $query = "SELECT staffID AS id, staffName AS name, staffEmail AS email, staffRole AS status, lastLogin FROM staff WHERE staffID = ?";
}

$stmt = $conn->prepare($query);

// Check if statement preparation was successful
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit();
}

// Bind and execute the query
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists and respond accordingly
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $response = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'status' => $row['status'] ?? 'N/A',
        'lastLogin' => $row['lastLogin'] ?? 'N/A'
    ];

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'User not found']);
}

// Clean up
$stmt->close();
$conn->close();
?>
