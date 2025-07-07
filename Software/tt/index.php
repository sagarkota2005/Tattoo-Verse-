<?php
session_start();
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <section class="hero" style="background-image: url('https://www.thefashionisto.com/wp-content/uploads/2024/01/Tattoo-Ideas-for-Men-Featured.jpg'); background-size: cover; background-position: center;">
        <div class="hero-content">
            <h1>Welcome to Tattoo Verse</h1>
            <p>Express yourself through art. Professional tattoo services with experienced artists.</p>
            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): // Hide for admin ?>
            <a href="appointments.php" class="btn">Book an Appointment</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="container">
            <h2>Why Choose Us</h2>
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safety First</h3>
                    <p>Strict sterilization protocols and single-use needles for your safety.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-star"></i>
                    <h3>Expert Artists</h3>
                    <p>Professionally trained and experienced tattoo artists.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-heart"></i>
                    <h3>Custom Designs</h3>
                    <p>Unique tattoos designed specifically for you.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-clock"></i>
                    <h3>Flexible Hours</h3>
                    <p>Convenient scheduling to fit your busy life.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section" style="background-image: url('https://th.bing.com/th/id/OSK.HERO1uUP--DJ5lecu6qf4ijJRjO8BAnAd9LK7iIegg4RLvk?w=472&h=280&c=1&rs=2&o=6&dpr=1.3&pid=SANGAM'); background-size: cover; background-position: center; padding: 60px 0; position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <h2 style="color:white;">Ready to Get Started?</h2>
            <p style="color:white;">Check out our gallery to see our work and meet our artists</p>
            <div class="cta-buttons">
                <a href="gallery.php" class="btn">View Gallery</a>
                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): // Hide for admin ?>
                <a href="appointments.php" class="btn btn-secondary">Book Appointment</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
</body>
</html>
