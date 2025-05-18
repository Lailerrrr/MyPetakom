<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Advisor Dashboard - MyPetakom</title>
    <link rel="stylesheet" href="advisorHomePage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    </head>
    <body>

    <aside class="sidebar">
        <div class="sidebar-header">
        <img src="petakom-logo.png" alt="PETAKOM Logo" class="sidebar-logo" />
        <div>
            <h2>MyPetakom</h2>
            <p class="role-label">🧭 Advisor</p>
        </div>
        </div>

        <nav class="menu">
        <ul>
            <li><a href="#" class="active">Profile</a></li>
            <li><a href="#">Membership</a></li>
            <li><a href="#">Merit Overview</a></li>
            <li><a href="#">Event Registration</a></li>
            <li><a href="#">Manage Events</a></li>
            <li><a href="#">Committee Management</a></li>
            <li><a href="#">Merit Applications</a></li>
            <li><a href="#">Generate QR Code</a></li>
            <li><a href="#">User Dashboard</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
        </nav>
    </aside>

    <main class="main-content">

        <!-- DASHBOARD INDICATOR -->
        <div class="dashboard-indicator">
        <span class="dashboard-role">🧭 Advisor Dashboard</span>
        <span class="dashboard-user">Logged in as: <strong>Mr. Adam Ismail</strong></span>
        </div>

        <header class="main-header">
        <h1>Welcome, <span class="username">Mr. Adam</span>!</h1>
        <p>Here’s your PETAKOM advisor control center.</p>
        </header>

        <section class="dashboard-cards">
        <div class="card">
            <h3>Upcoming Events</h3>
            <p>2 pending approvals</p>
        </div>

        <div class="card">
            <h3>Merit Submissions</h3>
            <p>5 applications waiting review</p>
        </div>

        <div class="card">
            <h3>Event QR Tools</h3>
            <p>Generate & view attendance</p>
        </div>

        <div class="card">
            <h3>Committee Status</h3>
            <p>3 committees under supervision</p>
        </div>
        </section>

        <!-- Profile Section with Form -->
    <section class="profile-info">
    <h2>Advisor Profile</h2>
    <form id="profileForm">
        <div class="profile-details">
        <div>
            <label for="name"><strong>Name:</strong></label><br/>
            <input type="text" id="name" name="name" value="Mr. Adam Ismail" />
        </div>
        <div>
            <label for="email"><strong>Email:</strong></label><br/>
            <input type="email" id="email" name="email" value="adam.ismail@umpsa.edu.my" />
        </div>
        <div><strong>Department:</strong> Software Engineering</div>
        <div><strong>Advising Since:</strong> 2021</div>
        <div><strong>Current Events:</strong> 2 Active</div>
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
    </section>


    </main>
    <script>
    // Handle sidebar navigation clicks
    document.querySelectorAll('.menu a').forEach(link => {
        link.addEventListener('click', e => {
        e.preventDefault(); // prevent default anchor behavior
        alert(`You clicked on "${link.textContent}"`);
        // Here you could load content dynamically or navigate
        });
    });

    // Handle profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = this.name.value.trim();
        const email = this.email.value.trim();

        if(name === '' || email === '') {
        alert('Please fill in all fields.');
        return;
        }

        // You can add real submission logic here (e.g., AJAX call)
        alert(`Profile updated:\nName: ${name}\nEmail: ${email}`);
    });
    </script>

    </body>
</html>
