<?php
session_start();
require_once '../DB_mypetakom/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userID'])) {
    $studentID = $_POST['userID'];

    $query = "DELETE FROM student WHERE studentID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentID);

    if ($stmt->execute()) {
        session_destroy();
        header("Location: ./ManageLogin/login.php?msg=account_deleted");
        exit();
    } else {
        echo "Error deleting profile.";
    }
}
?>
