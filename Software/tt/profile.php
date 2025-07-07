<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=profile.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user information
try {
    $stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Get user's appointments
    $stmt = $pdo->prepare("
        SELECT 
            app.id, 
            app.service_type, 
            app.appointment_date, 
            app.appointment_time, 
            app.status, 
            COALESCE(u.username, 'Any Available') as artist_name
        FROM appointments app
        LEFT JOIN users u ON app.artist_id = u.id AND u.role = 'artist'
        WHERE app.user_id = ?
        ORDER BY app.appointment_date DESC, app.appointment_time DESC
    ");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Failed to load profile data.";
    error_log($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .appointments-section {
            margin-top: 2rem;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .appointments-section h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--primary-color);
        }
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .appointments-table th, .appointments-table td {
            border: 1px solid #e0e0e0;
            padding: 10px 12px;
            text-align: left;
            vertical-align: middle;
        }
        .appointments-table th {
            background-color: #f8f9fa;
        }

        /* Style for declined status */
        .status-badge.status-declined {
            color: #dc3545; /* Bootstrap danger red */
            font-weight: bold;
        }

        /* Style for unknown/missing status */
        .status-badge.status-unknown {
            color: #dc3545; /* Bootstrap danger red */
            font-weight: bold; /* Match the declined style */
        }

        /* Add more status styles if needed, e.g., completed, cancelled */
        .no-appointments {
            text-align: center;
            color: #6c757d;
            margin-top: 1rem;
        }
        .table-responsive {
             overflow-x: auto;
         }
         @media (max-width: 768px) {
            .appointments-table th,
            .appointments-table td {
                 font-size: 0.9rem;
                 padding: 8px 10px;
            }
         }
         @media (max-width: 480px) {
             .appointments-table thead {
                 display: none; /* Hide headers on very small screens */
             }
             .appointments-table, .appointments-table tbody, .appointments-table tr, .appointments-table td {
                 display: block;
                 width: 100%;
             }
             .appointments-table tr {
                 margin-bottom: 15px;
                 border: 1px solid #ddd;
             }
             .appointments-table td {
                 text-align: right;
                 padding-left: 50%;
                 position: relative;
                 border-bottom: 1px solid #eee;
             }
             .appointments-table td::before {
                 content: attr(data-label);
                 position: absolute;
                 left: 10px;
                 width: 45%;
                 padding-right: 10px;
                 white-space: nowrap;
                 text-align: left;
                 font-weight: bold;
             }
             .appointments-table td:last-child {
                  border-bottom: 0;
              }
         }
         .info-label {
            display: flex;       /* Use flexbox for layout */
            align-items: center; /* Vertically align icon and text */
            gap: 10px;           /* Create space between icon and text */
        }
        /* Increase gap specifically for the Email row (2nd child) */
        .info-card .info-row:nth-child(2) .info-label {
            gap: 20px;
        }
   </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <main class="profile-page">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                    <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                    <span class="member-status">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></span>
                </div>
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
            </div>

            <!-- User Information -->
            <div class="profile-section">
                <h2>Account Information</h2>
                <div class="info-card">
                    <div class="info-row">
                        <div class="info-label"><i class="fas fa-user"></i>Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="fas fa-envelope"></i>Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="fas fa-calendar-alt"></i>Member Since</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                    </div>
                </div>
            </div>

            <!-- Appointments -->
            <div class="profile-section appointments-section">
                <h2>My Appointments</h2>
                <?php if (empty($appointments)): ?>
                    <p class="no-appointments">You have no appointments booked yet. <a href="appointments.php">Book Now!</a></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Artist</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $app): ?>
                                    <?php 
                                        $rawStatus = $app['status'] ?? null; // Get raw status, default to null if not set
                                        $safeStatus = htmlspecialchars($rawStatus ?? ''); // Sanitize, default to empty string if null
                                        
                                        $statusText = ucfirst($safeStatus);
                                        $statusClass = 'status-' . strtolower($safeStatus);

                                        // Check if the status text ended up empty after processing
                                        if (empty(trim($statusText))) {
                                            $statusText = 'Declined'; // Fallback text
                                            $statusClass = 'status-unknown'; // Use specific class for styling
                                        }
                                    ?>
                                    <tr>
                                        <td data-label="Service"><?php echo htmlspecialchars($app['service_type']); ?></td>
                                        <td data-label="Date"><?php echo htmlspecialchars(date('M d, Y', strtotime($app['appointment_date']))); ?></td>
                                        <td data-label="Time"><?php echo htmlspecialchars(date('h:i A', strtotime($app['appointment_time']))); ?></td>
                                        <td data-label="Artist"><?php echo htmlspecialchars($app['artist_name'] ?? 'N/A'); ?></td>
                                        <td data-label="Status">
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
