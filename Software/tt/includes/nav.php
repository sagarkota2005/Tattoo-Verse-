<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
    <nav class="nav-container">
        <a href="index.php" class="logo">Tattoo Verse</a>
        <div class="hamburger" id="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="navbar-links" id="navbar-links">
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="aftercare.php">Aftercare</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                        <li><a href="admin_logout.php">Admin Logout</a></li>
                    <?php else: // Regular logged-in user ?>
                        <li><a href="appointments.php">Book Appointment</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                <?php else: // Guest user ?>
                    <li><a href="appointments.php">Book Appointment</a></li>
                    <li><a href="admin_login.php">Admin Login</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<style>
@media (max-width: 900px) {
    .hamburger {
        display: flex !important;
    }
    .nav-menu {
        display: none;
    }
    .navbar-links.active .nav-menu {
        display: flex !important;
        flex-direction: column;
        background: var(--primary-color); /* Or use a slightly different background if needed */
        position: absolute;
        top: 60px; /* Adjust as needed based on header height */
        right: 0;
        width: 200px;
        padding: 1rem 0; /* Adjusted padding */
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        border-radius: 0 0 10px 10px;
        z-index: 1001;
        list-style: none; /* Ensure no bullet points */
        margin: 0;
    }

    .navbar-links.active .nav-menu li {
        width: 100%; /* Make list items take full width */
    }

    .navbar-links.active .nav-menu li a {
        display: block; /* Make links block-level */
        padding: 12px 20px; /* Add padding */
        color: white; /* Ensure text is visible */
        text-decoration: none;
        font-size: 0.95rem;
        transition: background-color 0.3s ease;
    }

    .navbar-links.active .nav-menu li a:hover {
        background-color: rgba(255, 255, 255, 0.1); /* Subtle hover effect */
    }
}
</style>

<script>
// Hamburger menu toggle
const hamburger = document.getElementById('hamburger-menu');
const navLinks = document.getElementById('navbar-links');
hamburger.addEventListener('click', function() {
    navLinks.classList.toggle('active');
    hamburger.classList.toggle('active');
});
</script>
