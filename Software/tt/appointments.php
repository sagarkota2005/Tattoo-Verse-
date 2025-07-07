<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=appointments.php');
    exit();
}

// Initialize messages
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_type = $_POST['service_type'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $artist_id = isset($_POST['artist']) && !empty($_POST['artist']) ? $_POST['artist'] : null; // Get artist ID, NULL if empty
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $is_home_service = isset($_POST['home_service']) ? 1 : 0;
    $address = $is_home_service ? $_POST['address'] : '';
    $status = 'pending'; // Default status for new bookings

    // Override service type if home service is selected
    if ($is_home_service) {
        $service_type = 'Home Service';
    }

    // Validate inputs
    if (empty($service_type) || empty($appointment_date) || empty($appointment_time)) {
        $error = "Please fill in all required fields";
    } elseif ($is_home_service && empty($address)) {
        $error = "Address is required for home service";
    } else {
        try {
            // Updated INSERT statement including artist_id and status
            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time, artist_id, notes, is_home_service, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            // Updated execute array with artist_id and status
            $stmt->execute([$user_id, $service_type, $appointment_date, $appointment_time, $artist_id, $notes, $is_home_service, $address, $status]);
            $appointment_id = $pdo->lastInsertId();
            
            $success = "Your appointment request has been submitted successfully! We will review it and notify you upon confirmation."; // Updated success message
            
            // Clear form data after successful submission
            $_POST = array();
        } catch(PDOException $e) {
            $error = "Booking failed. Please try again.";
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; // Include the centralized navigation bar ?>

    <main class="appointment-page">
        <section class="appointment-form">
            <div class="container">
                <h1>Book Your Appointment</h1>
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <p class="section-desc">Choose your preferred service, date, and time</p>

                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="appointments.php" id="booking-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="service_type">Service Type</label>
                            <select name="service_type" id="service_type" required>
                                <option value="">Select a service</option>
                                <option value="Custom Design">Custom Design</option>
                                <option value="Cover Up">Cover Up</option>
                                <option value="Traditional">Traditional</option>
                                <option value="Portrait">Portrait</option>
                                <option value="Japanese">Japanese Style</option>
                                <option value="Geometric">Geometric</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="artist">Preferred Artist</label>
                            <select name="artist" id="artist">
                                <option value="">Select an artist (optional)</option>
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT id, username, specialization FROM users WHERE role = 'artist' ORDER BY username ASC");
                                    while ($artist = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $displayText = htmlspecialchars($artist['username']);
                                        if (!empty($artist['specialization'])) {
                                            $displayText .= ' - ' . htmlspecialchars($artist['specialization']);
                                        }
                                        echo '<option value="' . htmlspecialchars($artist['id']) . '">' . $displayText . '</option>';
                                    }
                                } catch(PDOException $e) {
                                    // Log error, but don't break the page
                                    error_log("Appointments Page Error fetching artists: PDOException - " . $e->getMessage());
                                    // Optionally add a disabled option indicating an error
                                    echo '<option value="" disabled>Error loading artists</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="appointment_date">Preferred Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="appointment_time">Preferred Time</label>
                            <select name="appointment_time" id="appointment_time" required>
                                <option value="">Select a time</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="4" placeholder="Describe your tattoo idea, size, placement, or any special requirements"></textarea>
                    </div>

                    <div class="home-service-section">
                        <div class="checkbox-group">
                            <input type="checkbox" name="home_service" id="home_service">
                            <label for="home_service">Request Home Service</label>
                        </div>
                        <p class="service-note">* Additional charges apply for home service</p>
                        <p class="service-note">* Home service is currently available only within Mumbai.</p>
                        
                        <div class="form-group" id="address-group" style="display: none;">
                            <label for="address">Service Address</label>
                            <textarea name="address" id="address" rows="3" placeholder="Enter your complete address for home service"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Book Appointment</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="booking-info">
            <div class="container">
                <h2>Booking Information</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-info-circle"></i>
                        <h3>Consultation</h3>
                        <p>Free consultation included with every booking</p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h3>Duration</h3>
                        <p>Sessions typically last 2-4 hours</p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-home"></i>
                        <h3>Home Service</h3>
                        <p>Available within city limits</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('home_service').addEventListener('change', function() {
            const addressGroup = document.getElementById('address-group');
            addressGroup.style.display = this.checked ? 'block' : 'none';
            
            const addressInput = document.getElementById('address');
            if (this.checked) {
                addressInput.setAttribute('required', 'required');
            } else {
                addressInput.removeAttribute('required');
            }
        });

        // Form validation
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const date = document.getElementById('appointment_date').value;
            const time = document.getElementById('appointment_time').value;
            
            const selectedDateTime = new Date(date + ' ' + time);
            const now = new Date();
            
            if (selectedDateTime <= now) {
                e.preventDefault();
                alert('Please select a future date and time');
            }
        });
    </script>
</body>
</html>
