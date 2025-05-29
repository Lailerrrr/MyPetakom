<?php
    session_start();
    require_once '../DB_mypetakom/db.php';


    // Redirect if not logged in
    if (!isset($_SESSION['userID'])) {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];


    
    // Fetch all membership applications with student info
    $query = "
        SELECT 
            m.membershipID, 
            m.studentID, 
            m.studentCard AS cardImage, 
            m.status, 
            s.studentName
        FROM 
            membership m
        JOIN 
            student s ON m.studentID = s.studentID
        ORDER BY m.apply_at DESC
    ";

    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Verify Membership - MyPetakom</title>
        <link rel="stylesheet" href="../Home/adminHomePage.css">
        <link rel="stylesheet" href="verifyMembership.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
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

                <li><a href="../membership/verifyMembership.php"class="active">Verify Membership</a></li>
                <li><a href="../membership/deleteMembership.php" >Delete Membership</a></li>

            </ul>
        </nav>
        </aside>

        <main class="main-content">
            <div class="main-header">
                <h1>Verify Membership Applications</h1>
                <p class="subheading">Review and approve, reject, or delete student membership applications.</p>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <p style="color: #28a745; font-weight: bold;"><?= htmlspecialchars($_GET['success']) ?></p>
            <?php elseif (isset($_GET['error'])): ?>
                <p style="color: #dc3545; font-weight: bold;"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>


           <section class="application-list">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="application-card">
                            <div class="card-image-preview">
                                <img src="../uploads/<?= htmlspecialchars($row['cardImage']) ?>" alt="Student Card" />
                            </div>
                            <div class="application-details">
                                <h3><?= htmlspecialchars($row['studentName']) ?> (<?= htmlspecialchars($row['studentID']) ?>)</h3>
                                <p><strong>Status:</strong> 
                                    <span class="<?= $row['status'] === 'Pending' ? 'status-pending' : ($row['status'] === 'Approved' ? 'status-approved' : 'status-rejected') ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </p>
                                <form method="post" action="processMembership.php" class="action-buttons">
                                    <input type="hidden" name="membershipID" value="<?= $row['membershipID'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                                </form>
                            </div>


                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-applications">No membership applications found.</p>
                <?php endif; ?>
            </section>
        </main>
    </body>
</html>
