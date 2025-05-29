<?php
session_start();
require_once '../DB_mypetakom/db.php';

if (!isset($_SESSION['userID']) || strtolower($_SESSION['staffRole']) !== 'petakom coordinator') {
    header("Location: ../ManageLogin/login.php");
    exit();
}

// Delete operation
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];
    $table = ($type === 'student') ? 'student' : 'staff';
    $idField = ($type === 'student') ? 'studentID' : 'staffID';

    $stmt = $conn->prepare("DELETE FROM $table WHERE $idField=?");
    $stmt->bind_param("s", $id);
       if ($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting user: " . $stmt->error;
    }
    
    header("Location: manageProfile.php");
    exit();
}

// Add new student/advisor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $table = ($type === 'student') ? 'student' : 'staff';
    $id = $_POST['id'];

    // Check if the ID already exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE " . ($type === 'student' ? 'studentID' : 'staffID') . " = ?");
    $checkStmt->bind_param("s", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

       if ($count > 0) {
            $_SESSION['error'] = "Error: The ID '$id' already exists.";
        } else {
            if ($type === 'student') {
                $stmt = $conn->prepare("INSERT INTO student (studentID, studentName, studentEmail, studentPassword, verify) VALUES (?, ?, ?, ?, 'pending')");
            } else {
                $role = ($id === $_SESSION['userID']) ? 'petakom coordinator' : 'Event Advisor';
                $stmt = $conn->prepare("INSERT INTO staff (staffID, staffName, staffEmail, staffPassword, staffRole) VALUES (?, ?, ?, ?, ?)");
            }
            
            $stmt->bind_param("ssss".($type === 'advisor' ? "s" : ""), $id, $name, $email, $password, ...($type === 'advisor' ? [$role] : []));
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "User added successfully!";
            } else {
                $_SESSION['error'] = "Error adding user: " . $stmt->error;
            }
        }
        header("Location: manageProfile.php");
        exit();
    }



// Edit user operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $type = $_POST['type'];
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $table = ($type === 'student') ? 'student' : 'staff';
    $idField = ($type === 'student') ? 'studentID' : 'staffID';
    $nameField = ($type === 'student') ? 'studentName' : 'staffName';
    $emailField = ($type === 'student') ? 'studentEmail' : 'staffEmail';
    
    $stmt = $conn->prepare("UPDATE $table SET $nameField=?, $emailField=? WHERE $idField=?");
    $stmt->bind_param("sss", $name, $email, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating user: " . $stmt->error;
    }
    header("Location: manageProfile.php");
    exit();
}



if (isset($_POST['update_coordinator'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    
    if ($password) {
        $stmt = $conn->prepare("UPDATE staff SET staffName=?, staffEmail=?, staffPassword=? WHERE staffID=?");
        $stmt->bind_param("ssss", $name, $email, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE staff SET staffName=?, staffEmail=? WHERE staffID=?");
        $stmt->bind_param("sss", $name, $email, $id);
    }
    
    // Execute and handle results...
}

// Fetch data
$students = $conn->query("SELECT * FROM student");
$advisors = $conn->query("SELECT * FROM staff WHERE staffRole = 'Event Advisor'");
$coordinator = $conn->query("SELECT * FROM staff WHERE staffID = '{$_SESSION['userID']}'")->fetch_assoc();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Profiles</title>
    <link rel="stylesheet" href="../User/profile.css">
    <link rel="stylesheet" href="../Home/adminHomePage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            max-width: 600px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        /* Action buttons */
        .action-buttons {
            display: inline-block;
            margin-left: 10px;
        }
        
        .view-btn, .edit-btn, .delete-btn {
            padding: 5px 10px;
            margin: 0 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            color: white;
        }
        
        .view-btn { background-color: #4CAF50; }
        .edit-btn { background-color: #2196F3; }
        .delete-btn { background-color: #f44336; }
        
        /* Form styles */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Message styles */
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .error {
            background-color: #ffdddd;
            color: #d8000c;
        }
        
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
        }
        
        /* User list styles */
        .user-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            list-style-type: none;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/MyPetakom/petakom-logo.png" alt="PETAKOM Logo" class="logo" />
                <div class="sidebar-text">
                    <h2>MyPetakom</h2>
                    <p class="role-label">🧑‍💼 PETAKOM Coordinator</p>
                </div>
            </div>

            <nav class="menu">
                <ul>
                    <li><a href="../Home/adminHomePage.php">Dashboard</a></li>
                    <li><a href="/MyPetakom/User/manageProfile.php" class="active">Profile</a></li>
                    <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                    <li><a href="../Module2/eventApproval.php">Event Management</a></li>
                    <li><a href="#">Attendance Tracking</a></li>
                    <li><a href="#">Merit Applications</a></li>
                    <li><a href="#">Reports & Analytics</a></li>
                    <li><a href="#">System Settings</a></li>
                    <li>
                        <form method="post" action="../ManageLogin/Logout.php" class="logout-form">
                            <button type="submit" name="logout" class="sidebar-button">Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <h2>🛠️ Manage Profiles</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <div class="add-user-form">
                <h3>Add New User</h3>
                <form method="POST">
                    <input type="hidden" name="add_user" value="1">
                    <div class="form-group">
                        <label>User Type:</label>
                        <select name="type" required>
                            <option value="student">Student</option>
                            <option value="advisor">Advisor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ID:</label>
                        <input type="text" name="id" required>
                    </div>
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="add-btn">Add User</button>
                </form>
            </div>

            <!-- Coordinator Profile Section -->
            <div class="profile-section">
                <h3>👤 Your Coordinator Profile</h3>
                <form method="POST">
                    <input type="hidden" name="update_coordinator" value="1">
                    <input type="hidden" name="id" value="<?= $coordinator['staffID'] ?>">
                    <div class="form-group">
                        <label>ID:</label>
                        <input type="text" value="<?= $coordinator['staffID'] ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($coordinator['staffName']) ?>" required>
                    </div>
                    <!-- ... other fields ... -->
                    <button type="submit" class="edit-btn">Update Profile</button>
                </form>
            </div>

     
 

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3 id="editTitle">Edit User</h3>
        <div id="editContent">
            <form method="POST">
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" name="type" id="editType">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <button type="submit" class="edit-btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('viewModal')">&times;</span>
        <h3 id="viewTitle">User  Details</h3>
        <div id="viewContent">
            <p>Loading user details...</p>
        </div>
    </div>
</div>

<!-- User List -->
<div class="student-list">
    <h3>📋 Student List</h3>
    <ul class="user-list">
        <?php while ($row = $students->fetch_assoc()): ?>
            <li>
                <?= $row['studentID'] ?> - <?= $row['studentName'] ?>
                <div class="action-buttons">
                    <button class="view-btn" onclick="viewUser ('student', '<?= $row['studentID'] ?>')">View</button>
                    <button class="edit-btn" onclick="editUser ('student', '<?= $row['studentID'] ?>', '<?= htmlspecialchars($row['studentName'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['studentEmail'], ENT_QUOTES) ?>')">Edit</button>
                    <a href="?delete=<?= $row['studentID'] ?>&type=student" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<div class="advisor-list">
    <h3>📋 Advisor List</h3>
    <ul class="user-list">
        <?php while ($row = $advisors->fetch_assoc()): ?>
            <li>
                <?= $row['staffID'] ?> - <?= $row['staffName'] ?>
                <div class="action-buttons">
                    <button class="view-btn" onclick="viewUser ('advisor', '<?= $row['staffID'] ?>')">View</button
                    <button class="edit-btn" onclick="editUser ('advisor', '<?= $row['staffID'] ?>', '<?= htmlspecialchars($row['staffName'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['staffEmail'], ENT_QUOTES) ?>')">Edit</button>
                    <a href="?delete=<?= $row['staffID'] ?>&type=advisor" class="delete-btn" onclick="return confirm('Are you sure you want to delete this advisor?')">Delete</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>



 

   

        <!-- JavaScript Functions -->
      <script>
    // View user details
    function viewUser(type, id) {
    document.getElementById('viewContent').innerHTML = '<p>Loading user details...</p>';
    fetch(`getUserDetails.php?type=${type}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('viewContent').innerHTML = `<p>Error: ${data.error}</p>`;
            } else {
                displayUserDetails(type, data);
            }
            document.getElementById('viewModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching user details:', error);
            document.getElementById('viewContent').innerHTML = '<p>Failed to load user details.</p>';
            document.getElementById('viewModal').style.display = 'block';
        });
    }


    // Display user details in modal
    function displayUserDetails(type, data) {
        const content = `
            <p><strong>ID:</strong> ${data.id}</p>
            <p><strong>Name:</strong> ${data.name}</p>
            <p><strong>Email:</strong> ${data.email}</p>
            <p><strong>Status:</strong> ${data.status || 'N/A'}</p>
            <p><strong>Last Login:</strong> ${data.lastLogin || 'N/A'}</p>
        `;
        document.getElementById('viewContent').innerHTML = content;
    }

    // Edit user details
    function editUser(type, id, name, email) {
        document.getElementById('editType').value = type;
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name || '';
        document.getElementById('editEmail').value = email || '';
        document.getElementById('editTitle').textContent = 'Edit ' + (type === 'student' ? 'Student' : 'Advisor');
        document.getElementById('editModal').style.display = 'block';
    }

    // Close modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Optional: Close modal when clicking outside of it
    window.onclick = function(event) {
        const viewModal = document.getElementById('viewModal');
        const editModal = document.getElementById('editModal');
        if (event.target === viewModal) closeModal('viewModal');
        if (event.target === editModal) closeModal('editModal');
    }

  
function editUser(type, id, name, email) {
}



</script>



</body>
</html>
