<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Admin Dashboard - MyPetakom</title>
        <link rel="stylesheet" href="adminHomePage.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />

    </head>
    <body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="petakom-logo.png" alt="PETAKOM Logo" class="sidebar-logo" />
            <div>
                <h2>MyPetakom</h2>
                <p class="role-label">💼 Administrator</p>
            </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="#">Manage Users</a></li>
                <li><a href="#">System Logs</a></li>
                <li><a href="#">Event Control</a></li>
                <li><a href="#">Analytics</a></li>
                <li><a href="#">Merit Settings</a></li>
                <li><a href="#">Backup & Restore</a></li>
                <li><a href="#">System Config</a></li>
                <li>
                    <form method="post" style="display:inline;">
                        <button name="logout">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">

        <div class="dashboard-indicator">
            <span class="dashboard-role">💼 Admin Dashboard</span>
            <span class="dashboard-user">Logged in as: <strong></strong></span>
        </div>

        <header class="main-header">
            <h1>Welcome, !</h1>
            <p>Manage platform operations, user roles, and event monitoring.</p>
        </header>

        <section class="dashboard-cards">
            <div class="card">
                <h3>Total Users</h3>
                <p><?= $totalUsers; ?>+ Registered</p>
            </div>

            <div class="card">
                <h3>Pending Events</h3>
                <p><?= $pendingEvents; ?> Waiting Approval</p>
            </div>

            <div class="card">
                <h3>Merit Requests</h3>
                <p><?= $meritRequests; ?> Submitted Today</p>
            </div>

            <div class="card">
                <h3>System Uptime</h3>
                <p><?= $uptime; ?> This Month</p>
            </div>
        </section>

        <section class="form-section">
            <h2>Update Merit Request Count</h2>
            <form method="POST">
                <input type="number" name="meritValue" placeholder="Enter new count" required />
                <button type="submit" name="updateMerit">Update</button>
            </form>
        </section>

    </main>

    </body>
</html>
