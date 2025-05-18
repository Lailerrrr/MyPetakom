<?php
$host = "localhost";         // or your server IP
$username = "root";          // your database username
$password = "";              // your database password
$database = "mypetakom_db";  // your database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
