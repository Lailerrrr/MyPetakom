<?php
    session_start();
    require_once '../DB_mypetakom/db.php';

    // Redirect if not logged in
    if (!isset($_SESSION['userID'])) {
        header("Location: ../ManageLogin/login.php");
        exit();
    }

    // Fetch pending membership applications
    $query = "SELECT membershipID, studentID, studentCard, status FROM membership";
    $result = $conn->query($query);
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
                <li><a href="../membership/verifyMembership.php">Verify Membership</a></li>
                <li><a href="../membership/deleteMembership.php" class="active">Delete Membership</a></li>
            </ul>
        </nav>
        </aside>

        <main class="main-content">
            <div class="main-header">
                <h1>Verify Membership Applications</h1>
                <p class="subheading">Review and approve or reject student membership applications.</p>
            </div>

            <section class="application-list">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="application-card">
                            <h3><?= htmlspecialchars($row['studentName']) ?> (<?= htmlspecialchars($row['studentID']) ?>)</h3>
                            <p><strong>Program:</strong> <?= htmlspecialchars($row['program']) ?></p>
                            <div class="card-image-preview">
                                <img src="../uploads/<?= htmlspecialchars($row['cardImage']) ?>" alt="Student Card" />
                            </div>
                            <form method="post" action="processMembership.php">
                                <input type="hidden" name="application_id" value="<?= $row['id'] ?>">
                                <button name="approve" class="btn approve">Approve</button>
                                <button name="reject" class="btn reject">Reject</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-applications">No pending applications at the moment.</p>
                <?php endif; ?>
            </section>
        </main>
    </body>
</html>
