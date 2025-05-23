<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = "";     // Change if needed
$dbname = "merit_system";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// Create tables
$tables = [

"User" => "
CREATE TABLE IF NOT EXISTS User (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    studentID VARCHAR(20),
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50)
)",

"Membership" => "
CREATE TABLE IF NOT EXISTS Membership (
    membershipID INT AUTO_INCREMENT PRIMARY KEY,
    studentCard VARCHAR(50),
    status VARCHAR(50),
    userID INT,
    FOREIGN KEY (userID) REFERENCES User(userID)
)",

"Event" => "
CREATE TABLE IF NOT EXISTS Event (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    eventName VARCHAR(100),
    eventDescription TEXT,
    eventDate DATE,
    venue VARCHAR(100),
    approvalLetter TEXT,
    status VARCHAR(50),
    advisorID INT,
    FOREIGN KEY (advisorID) REFERENCES User(userID)
)",

"Committee" => "
CREATE TABLE IF NOT EXISTS Committee (
    committeeID INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(50),
    eventID INT,
    userID INT,
    FOREIGN KEY (eventID) REFERENCES Event(eventID),
    FOREIGN KEY (userID) REFERENCES User(userID)
)",

"MeritApplication" => "
CREATE TABLE IF NOT EXISTS MeritApplication (
    meritApplicationID INT AUTO_INCREMENT PRIMARY KEY,
    appliedDate DATE,
    approvalStatus VARCHAR(50),
    eventID INT,
    advisorID INT,
    FOREIGN KEY (eventID) REFERENCES Event(eventID),
    FOREIGN KEY (advisorID) REFERENCES User(userID)
)",

"MissingMeritClaim" => "
CREATE TABLE IF NOT EXISTS MissingMeritClaim (
    claimID INT AUTO_INCREMENT PRIMARY KEY,
    claimStatus VARCHAR(50),
    claimLetter TEXT,
    eventID INT,
    userID INT,
    FOREIGN KEY (eventID) REFERENCES Event(eventID),
    FOREIGN KEY (userID) REFERENCES User(userID)
)",

"Merit" => "
CREATE TABLE IF NOT EXISTS Merit (
    meritID INT AUTO_INCREMENT PRIMARY KEY,
    meritScore INT,
    semester VARCHAR(10),
    academicYear VARCHAR(10),
    position VARCHAR(50),
    eventID INT,
    userID INT,
    FOREIGN KEY (eventID) REFERENCES Event(eventID),
    FOREIGN KEY (userID) REFERENCES User(userID)
)",

"AttendanceSlot" => "
CREATE TABLE IF NOT EXISTS AttendanceSlot (
    slotID INT AUTO_INCREMENT PRIMARY KEY,
    slotName VARCHAR(100),
    qrCodePath TEXT,
    eventID INT,
    FOREIGN KEY (eventID) REFERENCES Event(eventID)
)",

"AttendanceRegistration" => "
CREATE TABLE IF NOT EXISTS AttendanceRegistration (
    attendanceID INT AUTO_INCREMENT PRIMARY KEY,
    checkinTime DATETIME,
    location VARCHAR(100),
    slotID INT,
    userID INT,
    FOREIGN KEY (slotID) REFERENCES AttendanceSlot(slotID),
    FOREIGN KEY (userID) REFERENCES User(userID)
)"
];

// Execute table creation
foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table '$name' created successfully\n";
    } else {
        echo "Error creating table '$name': " . $conn->error . "\n";
    }
}

$conn->close();
?>
