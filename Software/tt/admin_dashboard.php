<?php
session_start();
include 'config/database.php'; // Include database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Not logged in or not an admin, redirect to login
    header("Location: admin_login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
$error = '';
$success = '';
$appointmentActionMessage = ''; // For accept/decline feedback

// --- Handle Appointment Actions (Accept/Decline) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_appointment']) && isset($_POST['appointment_id'])) {
        $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_SANITIZE_NUMBER_INT);
        if ($appointmentId) {
            try {
                $stmt = $pdo->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ? AND status = 'pending'");
                if ($stmt->execute([$appointmentId])) {
                    if ($stmt->rowCount() > 0) {
                        $_SESSION['appointment_action_message'] = "<div class='message success'><i class='fas fa-check-circle'></i> Appointment ID: {$appointmentId} confirmed successfully.</div>";
                    } else {
                        $_SESSION['appointment_action_message'] = "<div class='message warning'><i class='fas fa-exclamation-triangle'></i> Appointment ID: {$appointmentId} was already processed or not found.</div>";
                    }
                } else {
                     throw new Exception("Database execution failed.");
                }
            } catch (Exception $e) {
                $_SESSION['appointment_action_message'] = "<div class='message error'><i class='fas fa-times-circle'></i> Error confirming appointment: " . $e->getMessage() . "</div>";
            }
        }
         header('Location: admin_dashboard.php#confirmed-appointments-section'); // Redirect to CONFIRMED section after accepting
         exit();
    } elseif (isset($_POST['decline_appointment']) && isset($_POST['appointment_id'])) {
        $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_SANITIZE_NUMBER_INT);
         if ($appointmentId) {
            try {
                $stmt = $pdo->prepare("UPDATE appointments SET status = 'declined' WHERE id = ?");
                 if ($stmt->execute([$appointmentId])) {
                    if ($stmt->rowCount() > 0) {
                        $_SESSION['appointment_action_message'] = "<div class='message success'><i class='fas fa-check-circle'></i> Appointment ID: {$appointmentId} declined successfully.</div>";
                    } else {
                        $_SESSION['appointment_action_message'] = "<div class='message warning'><i class='fas fa-exclamation-triangle'></i> Appointment ID: {$appointmentId} was already processed or not found.</div>";
                    }
                } else {
                     throw new Exception("Database execution failed.");
                }
            } catch (Exception $e) {
                $_SESSION['appointment_action_message'] = "<div class='message error'><i class='fas fa-times-circle'></i> Error declining appointment: " . $e->getMessage() . "</div>";
            }
        }
         header('Location: admin_dashboard.php#manage-appointments-section'); // Redirect back to MANAGE (pending) section after decline
         exit();
    }
}

// Display feedback message from session if it exists
if (isset($_SESSION['appointment_action_message'])) {
    $appointmentActionMessage = $_SESSION['appointment_action_message'];
    unset($_SESSION['appointment_action_message']); // Clear message after displaying
}

// --- Fetch All Appointments ---
$allAppointments = [];
$appointmentFetchError = ''; // Error var for appointments
try {
    // Get all appointments
    $stmt = $pdo->query("
        SELECT 
            app.id, 
            u.username as customer_name, 
            u.email as customer_email, 
            app.appointment_date, 
            app.appointment_time, 
            app.service_type as service, 
            app.address, 
            COALESCE(a.username, 'N/A') as artist_name,
            app.notes,
            app.status
        FROM appointments app
        JOIN users u ON app.user_id = u.id 
        LEFT JOIN users a ON app.artist_id = a.id AND a.role = 'artist'
        ORDER BY app.appointment_date DESC, app.appointment_time ASC
    ");
    $allAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Display error within the appointment section later
    $appointmentFetchError = "<div class='message error'><i class='fas fa-times-circle'></i> Error fetching appointments: " . $e->getMessage() . "</div>";
}

// --- Fetch Confirmed Appointments ---
$confirmedAppointments = [];
$confirmedAppointmentFetchError = ''; // Specific error var for confirmed
try {
    // Join with users to get customer and artist names
    $stmt = $pdo->query("
        SELECT
            app.id,
            cust.username as customer_name,
            cust.email as customer_email,
            app.appointment_date,
            app.appointment_time,
            app.service_type as service,
            app.address,
            COALESCE(art.username, 'N/A') as artist_name,
            app.notes
        FROM appointments app
        JOIN users cust ON app.user_id = cust.id
        LEFT JOIN users art ON app.artist_id = art.id AND art.role = 'artist'
        WHERE app.status = 'confirmed'
          AND CONCAT(app.appointment_date, ' ', app.appointment_time) >= NOW()
        ORDER BY app.appointment_date ASC, app.appointment_time ASC
    ");
    $confirmedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $confirmedAppointmentFetchError = "<div class='message error'><i class='fas fa-times-circle'></i> Error fetching confirmed appointments: " . $e->getMessage() . "</div>";
}

// Handle Add Artist form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artist'])) {
    $artistUsername = trim(filter_var($_POST['artist_username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $artistEmail = trim(filter_var($_POST['artist_email'], FILTER_SANITIZE_EMAIL));
    $artistPassword = trim($_POST['artist_password']);
    $uploadOk = 1;
    $imagePath = ''; // Initialize image path

    // Comprehensive server-side validation
    $errors = [];

    // Username validation
    if (empty($artistUsername)) {
        $errors['username'] = "Username is required.";
    } elseif (strlen($artistUsername) < 3) {
        $errors['username'] = "Username must be at least 3 characters long.";
    } elseif (strlen($artistUsername) > 50) {
        $errors['username'] = "Username cannot exceed 50 characters.";
    } elseif (!preg_match('/^[A-Za-z ]+$/', $artistUsername)) {
        $errors['username'] = "Username can only contain letters and spaces.";
    }

    // Email validation
    if (empty($artistEmail)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($artistEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    // Password validation
    if (empty($artistPassword)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($artistPassword) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $artistPassword)) {
        $errors['password'] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $artistPassword)) {
        $errors['password'] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $artistPassword)) {
        $errors['password'] = "Password must contain at least one number.";
    }

    if (!empty($errors)) {
        $error = "Please correct the following errors:<br>" . implode("<br>", $errors);
    } else {
        // --- Image Upload Handling --- 
        if (isset($_FILES['artist_image']) && $_FILES['artist_image']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/artists/"; // Relative path to upload directory
            // Create directory if it doesn't exist
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); // Adjust permissions as needed
            }

            $imageFileType = strtolower(pathinfo($_FILES['artist_image']["name"], PATHINFO_EXTENSION));
            // Generate a unique name
            $uniqueName = uniqid('artist_', true) . '.' . $imageFileType;
            $targetFile = $targetDir . $uniqueName;

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["artist_image"]["tmp_name"]);
            if ($check === false) {
                $error = "File is not a valid image.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check file size (e.g., 5MB limit)
            if ($_FILES["artist_image"]["size"] > 5000000) {
                $error = "Sorry, your file is too large (max 5MB).";
                $uploadOk = 0;
            }

            // Move the file if validation passed
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["artist_image"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile; // Store the path for the database
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                    $uploadOk = 0; // Ensure we don't proceed with DB insert if move failed
                }
            } 
        } elseif (isset($_FILES['artist_image']) && $_FILES['artist_image']['error'] != UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors
            $error = "Error uploading file. Error code: " . $_FILES['artist_image']['error'];
            $uploadOk = 0;
        }
        // --- End Image Upload Handling ---

        // Proceed with database insertion ONLY if there were no previous errors AND upload was successful (or no file was uploaded)
        if (empty($error) && $uploadOk == 1) { 
            try {
                // Check if username or email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$artistUsername, $artistEmail]);
                if ($stmt->fetch()) {
                    $error = "Username or email already exists.";
                } else {
                    // Hash the password
                    $hashedPassword = password_hash($artistPassword, PASSWORD_DEFAULT);

                    // Prepare SQL statement to insert new artist WITH image path
                    $sql = "INSERT INTO users (username, email, password, role, specialization, image_path) VALUES (?, ?, ?, 'artist', ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    
                    // Execute the statement
                    if ($stmt->execute([$artistUsername, $artistEmail, $hashedPassword, $_POST['artist_specialization'], $imagePath])) {
                        header("Location: index.php?message=artist_added"); // Optional: add a success message query param
                        exit();
                    } else {
                        $error = "Failed to add artist to the database.";
                    }
                }
            } catch (PDOException $e) {
                error_log("Admin dashboard: Error adding artist - " . $e->getMessage()); 
                // Check for specific constraint violation errors if needed
                if ($e->getCode() == '23000') { // Integrity constraint violation
                    $error = "Database error: Username or Email might already be taken.";
                } else {
                    $error = "Database error occurred while adding the artist. Please check logs or contact support."; 
                }
            }
        }
    }
}

// Handle Delete Artist form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_artist'])) {
    $artistId = trim(filter_var($_POST['delete_artist_id'], FILTER_SANITIZE_NUMBER_INT));

    if (empty($artistId)) {
        $error = "Invalid artist ID provided for deletion.";
    } else {
        try {
            // Ensure we only delete users with the 'artist' role for safety
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'artist'");
            if ($stmt->execute([$artistId])) {
                if ($stmt->rowCount() > 0) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = "Artist not found or already deleted.";
                }
            } else {
                $error = "Failed to delete artist. Database error.";
                error_log("Admin dashboard: Failed to execute delete artist statement - " . $artistId);
            }
        } catch (PDOException $e) {
            $error = "Database error while deleting artist.";
            error_log("Admin dashboard: PDOException on delete - " . $e->getMessage());
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Tattoo Verse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4; /* Lighter background for contrast */


            color: #333; /* Default text color */
        }

        .auth-page {
            display: flex;
            min-height: 100vh;
            align-items: flex-start; /* Align to top */
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Keep gradient background */
            padding: 2rem 1rem; /* Add some padding */
            box-sizing: border-box;
        }

        .dashboard-content-wrapper {
            width: 100%;
            max-width: 900px; /* Slightly wider */
            background-color: #ffffff; /* White background for content */
            color: #333; /* Dark text on white */
            padding: 2rem; /* More padding inside the wrapper */
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 2rem; /* Space from top */
        }

        .dashboard-header {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-bottom: 1.5rem;
             padding-bottom: 1rem;
             border-bottom: 1px solid #eee;
        }

        .dashboard-header h2 {
             margin: 0;
             font-size: 1.8rem;
             color: #444;
        }

        .welcome-logout span {
             margin-right: 1rem;
             color: #555;
        }

        .admin-button.logout {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .admin-button.logout:hover {
            background-color: #c82333;
        }

        .admin-nav {
            margin-bottom: 2rem; /* Space below nav */
            text-align: left;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        .admin-nav a {
            display: inline-block;
            padding: 0.8rem 1.5rem; /* Larger tabs */
            margin-right: 10px;
            color: #555;
            text-decoration: none;
            border-radius: 5px 5px 0 0; /* Top radius */
            transition: background-color 0.3s, color 0.3s;
            border: 1px solid transparent;
            border-bottom: none;
            font-weight: 500;
        }
        .admin-nav a:hover {
            color: #007bff;
            background-color: #f8f9fa; /* Light background on hover */
        }
        .admin-nav a.active {
            color: #007bff;
            background-color: #ffffff; /* White background for active */
            border-color: #eee;
            border-bottom-color: #ffffff; /* Connect with content */
            position: relative;
            top: 1px; /* Align with border */
        }

        .admin-section {
            display: none; /* Hide sections by default */
        }
        .admin-section.active {
            display: block; /* Show active section */

        }
        /*@keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }*/

        .form-section {
             background-color: #fdfdfd; /* Slightly off-white */
             padding: 2rem; /* More padding */
             border: 1px solid #eee;
             border-radius: 0 8px 8px 8px; /* Match wrapper */
             text-align: left;
             margin-top: -1px; /* Overlap border with nav */
        }
        .form-section h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            color: #333;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.8rem;
        }
        .form-section h3 i {
            margin-right: 0.5rem;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 1.2rem; /* Consistent spacing */
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .form-button {
             padding: 0.8rem 1.5rem;
             color: white;
             border: none;
             border-radius: 4px;
             cursor: pointer;
             font-size: 1rem;
             font-weight: 500;
             transition: background-color 0.3s, box-shadow 0.3s;
             display: inline-flex; /* Align icon and text */
             align-items: center;
        }
        .form-button i {
             margin-right: 0.5rem;
        }
        .form-button.add {
             background-color: #28a745;
        }
        .form-button.add:hover {
             background-color: #218838;
             box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .form-button.delete {
             background-color: #dc3545;
        }
        .form-button.delete:hover {
             background-color: #c82333;
             box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .message-area {
            margin-bottom: 1.5rem; /* Space below messages */
            width: 100%;
            text-align: left;
        }
        .error-message, .success-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            font-size: 0.95rem;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
        }
         .error-message i, .success-message i {
            margin-right: 0.6rem;
            font-size: 1.1rem;
         }
        .error-message {
             background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;
        }
        .success-message {
             background-color: #d4edda; color: #155724; border-color: #c3e6cb;
        }
        .message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* General helper */
        .no-artists-message {
            color: #666;
            padding: 1rem;
            background-color: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 4px;
            text-align: center;
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .dashboard-content-wrapper {
                padding: 1.5rem;
                margin-top: 1rem;
            }
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }
            .dashboard-header h2 {
                margin-bottom: 0.5rem;
                font-size: 1.6rem;
            }
            .welcome-logout {
                 margin-top: 0.5rem;
                 width: 100%;
                 text-align: left; /* Align logout to left below title */
            }
            .admin-nav a {
                 padding: 0.6rem 1rem;
                 font-size: 0.9rem;
            }
            .form-section {
                 padding: 1.5rem;
            }
            .form-section h3 {
                 font-size: 1.2rem;
            }
             .form-group input,
             .form-group select {
                 padding: 0.7rem 0.9rem;
                 font-size: 0.95rem;
             }
            .form-button {
                padding: 0.7rem 1.2rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-content-wrapper {
                padding: 1rem;
            }
             .dashboard-header h2 {
                font-size: 1.4rem;
            }
            .admin-nav {
                 white-space: normal; /* Allow tabs to wrap */
            }
            .admin-nav a {
                display: block; /* Stack tabs */
                margin-right: 0;
                margin-bottom: 5px;
                text-align: center;
                border-radius: 4px; /* Full radius when stacked */
                border: 1px solid #eee;
             }
             .admin-nav a.active {
                 position: static;
                 border-color: #ddd;
                 background-color: #e9ecef;
             }
            .form-section {
                 padding: 1rem;
             }
             .form-section h3 {
                  font-size: 1.1rem;
             }
            .form-button {
                width: 100%; /* Full width button on mobile */
                justify-content: center;
            }
        }

        /* Table Styles */
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        .appointments-table th,
        .appointments-table td {
            padding: 12px 15px; /* Increased padding */
            text-align: left;
            border-bottom: 1px solid #f0f0f0; /* Lighter border between rows */
            vertical-align: middle; /* Align cell content vertically */
        }

        /* Status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.declined {
            background-color: #f8d7da;
            color: #721c24;
        }
        }

        /* Status styles */
        .pending-status,
        .confirmed-status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .pending-status {
            background-color: #fff3cd;
            color: #856404;
        }

        .confirmed-status {
            background-color: #d4edda;
            color: #155724;
        }
        }
        .appointments-table thead th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        .appointments-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .appointments-table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Action Buttons Specific Styles */
        .action-buttons form {
            margin: 0;
        }
        .action-buttons .form-button {
            padding: 6px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1em;
            color: white;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .action-buttons .accept-button {
            background-color: #28a745;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }
        .action-buttons .accept-button:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }
        .action-buttons .decline-button {
            background-color: #dc3545;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }
        .action-buttons .decline-button:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        .action-buttons .form-button i {
            pointer-events: none;
        }

        /* Feedback Message Styles */
        .message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .message i {
            margin-right: 8px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* Responsive Styles for Appointments Table */
        @media screen and (max-width: 768px) {
            .appointments-table {
                border: none; /* Remove main table border */
                box-shadow: none;
                border-radius: 0;
            }

            .appointments-table thead {
                display: none; /* Hide table header */
            }

            .appointments-table tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }

            .appointments-table td {
                display: block;
                text-align: right; /* Align data to the right */
                padding-left: 50%; /* Push data to the right */
                position: relative;
                border-bottom: 1px solid #eee; /* Separator inside the 'card' */
            }

            .appointments-table td:last-child {
                 border-bottom: 0; /* Remove border for the last cell in a row */
            }

            .appointments-table td::before {
                content: attr(data-label); /* Use data-label as the label */
                position: absolute;
                left: 15px; /* Position label on the left */
                width: calc(50% - 30px); /* Calculate width considering padding */
                text-align: left;
                font-weight: bold;
                white-space: nowrap;
                color: #555;
            }

            .appointments-table .action-buttons {
                 text-align: right; /* Ensure buttons stay right-aligned */
                 padding-left: 15px; /* Reset padding */
             }

            .appointments-table .action-buttons::before {
                 /* Adjust label position slightly for buttons if needed */
             }
        }

        .error-message, .success-message, .message {
            padding: 15px;
            margin-bottom: 20px;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 15px;
        }
        .appointment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            position: relative;
        }
        .appointment-card .header {
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        .appointment-card .id {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .appointment-info {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            width: 100px;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .appointment-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        .btn-success, .btn-danger {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .service-tag {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.9em;
            color: #495057;
        }
    </style>
    <style>
        .form-group input.error {
            border-color: #dc3545;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
    <script src="js/admin-validation.js"></script>
    <style>
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out;
        }
        .form-group input.error {
            border-color: #dc3545;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
    <script src="js/admin-validation.js"></script>
</head>
<body>
    <main class="auth-page"> 
        <div class="dashboard-content-wrapper"> 
             <div class="dashboard-header">
                 <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
                 <div class="welcome-logout">
                     <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                     <a href="admin_logout.php" class="admin-button logout">Logout</a> 
                 </div>
             </div>

            <?php // Area for displaying messages ?>
            <div class="message-area">
                <?php if (!empty($error)): // Display general page load errors ?>
                    <div class="error-message">
                         <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): // Display general page load successes ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($appointmentActionMessage)): ?>
                    <?php echo $appointmentActionMessage; ?>
                <?php endif; ?>
            </div>

            <?php // Navigation for Admin Sections ?>
            <div class="admin-nav">
                <a href="#" data-section="add-artist-section" class="active"><i class="fas fa-user-plus"></i> Add Artist</a>
                <a href="#" data-section="delete-artist-section"><i class="fas fa-user-minus"></i> Delete Artist</a>
                <a href="#" data-section="manage-appointments-section"><i class="fas fa-calendar-alt"></i> Manage Appointments</a>
                <a href="#" data-section="confirmed-appointments-section"><i class="fas fa-calendar-check"></i> Confirm Appointment</a>
                <?php // Add links for other future sections here ?>
            </div>

            <?php // Add Artist Section Container ?>
            <div id="add-artist-section" class="admin-section active"> 
                <div class="form-section"> 
                    <h3><i class="fas fa-user-plus"></i> Add New Artist</h3>
                    <form id="addArtistForm" method="POST" action="admin_dashboard.php" enctype="multipart/form-data" onsubmit="return validateAddArtist();">
                        <input type="hidden" name="add_artist" value="1">
                        <div class="form-group">
                            <label for="artist_username">Artist Username:</label>
                            <input type="text" id="artist_username" name="artist_username" required value="<?php echo isset($_POST['artist_username']) ? htmlspecialchars($_POST['artist_username']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="artist_email">Artist Email:</label>
                            <input type="email" id="artist_email" name="artist_email" required value="<?php echo isset($_POST['artist_email']) ? htmlspecialchars($_POST['artist_email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="artist_password">Password (min. 8 chars):</label>
                            <input type="password" id="artist_password" name="artist_password" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="artist_specialization">Specialization:</label>
                            <input type="text" id="artist_specialization" name="artist_specialization" required value="<?php echo isset($_POST['artist_specialization']) ? htmlspecialchars($_POST['artist_specialization']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="artist_image">Artist Image:</label>
                            <input type="file" id="artist_image" name="artist_image" accept="image/*">
                        </div>
                        <button type="submit" class="form-button add"><i class="fas fa-plus-circle"></i> Add Artist</button>
                    </form>
                </div>
            </div>

            <?php // Delete Artist Section Container ?>
            <div id="delete-artist-section" class="admin-section">
                <div class="form-section"> 
                    <h3><i class="fas fa-user-minus"></i> Delete Artist</h3>
                    <?php
                    // Fetch artists for the dropdown
                    try {
                        $stmt = $pdo->query("SELECT id, username, email FROM users WHERE role = 'artist' ORDER BY username ASC");
                        $artists_to_delete = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $artists_to_delete = [];
                        // Display error within this section or rely on the main message area
                        echo '<p class="error-message"><i class="fas fa-exclamation-circle"></i> Error loading artists list for deletion.</p>';
                        error_log("Admin dashboard: Error fetching artists for delete dropdown - " . $e->getMessage());
                    }
                    ?>

                    <?php if (count($artists_to_delete) > 0): ?>
                        <form action="admin_dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete the selected artist? This action cannot be undone.');">
                            <div class="form-group">
                                <label for="delete_artist_id">Select Artist to Delete:</label>
                                <select name="delete_artist_id" id="delete_artist_id" required>
                                    <option value="">-- Select Artist --</option>
                                    <?php foreach ($artists_to_delete as $artist): ?>
                                        <option value="<?php echo htmlspecialchars($artist['id']); ?>">
                                            <?php echo htmlspecialchars($artist['username']) . ' (' . htmlspecialchars($artist['email']) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="delete_artist" value="1" class="form-button delete"><i class="fas fa-trash-alt"></i> Delete Selected Artist</button>
                        </form>
                    <?php else: ?>
                        <p class="no-artists-message">No artists currently available to delete.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php // Manage Appointments Section Container ?>
            <div id="manage-appointments-section" class="admin-section">
                <div class="form-section">
                    <h3><i class="fas fa-calendar-check"></i> Manage Appointments</h3>
                    <?php if (!empty($appointmentActionMessage)): ?>
                        <?php echo $appointmentActionMessage; ?>
                    <?php endif; ?>

                    <?php if (!empty($appointmentFetchError)): ?>
                        <?php echo $appointmentFetchError; ?>
                    <?php elseif (empty($allAppointments)): ?>
                        <p>No pending appointments found.</p>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach ($allAppointments as $app): ?>
                                <?php if ($app['status'] === 'pending'): ?>
                                    <div class="appointment-card">
                                        <div class="id">ID: <?php echo htmlspecialchars($app['id']); ?></div>
                                        <div class="header">
                                            <h4><?php echo htmlspecialchars($app['customer_name']); ?></h4>
                                            <div><?php echo htmlspecialchars($app['customer_email']); ?></div>
                                        </div>
                                        <div class="appointment-info">
                                            <div class="info-row">
                                                <span class="info-label">Date:</span>
                                                <span class="info-value"><?php echo htmlspecialchars(date('d M Y', strtotime($app['appointment_date']))); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Time:</span>
                                                <span class="info-value"><?php echo htmlspecialchars(date('h:i A', strtotime($app['appointment_time']))); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Service:</span>
                                                <span class="info-value">
                                                    <span class="service-tag"><?php echo htmlspecialchars($app['service']); ?></span>
                                                </span>
                                            </div>
                                            <?php if (!empty($app['artist_name'])): ?>
                                            <div class="info-row">
                                                <span class="info-label">Artist:</span>
                                                <span class="info-value"><?php echo htmlspecialchars($app['artist_name']); ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($app['address'])): ?>
                                            <div class="info-row">
                                                <span class="info-label">Address:</span>
                                                <span class="info-value"><?php echo htmlspecialchars($app['address']); ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($app['notes'])): ?>
                                            <div class="info-row">
                                                <span class="info-label">Notes:</span>
                                                <span class="info-value"><?php echo nl2br(htmlspecialchars($app['notes'])); ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="appointment-actions">
                                            <form method="POST" style="display: flex; gap: 10px; width: 100%;">
                                                <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($app['id']); ?>">
                                                <button type="submit" name="accept_appointment" class="btn-success">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                                <button type="submit" name="decline_appointment" class="btn-danger">
                                                    <i class="fas fa-times"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php // Confirmed Appointments Section Container ?>
            <div id="confirmed-appointments-section" class="admin-section"> 
                <div class="form-section">
                    <h3><i class="fas fa-calendar-day"></i> Confirmed Appointments</h3> 
                    <?php if (!empty($confirmedAppointmentFetchError)): ?>
                        <?php echo $confirmedAppointmentFetchError; // Display fetch error if exists ?>
                    <?php elseif (empty($confirmedAppointments)): ?>
                        <p>No confirmed appointments found.</p>
                    <?php else: ?>
                        <div class="appointments-grid">
                            <?php foreach ($confirmedAppointments as $app): ?>
                                <div class="appointment-card">
                                    <div class="id">ID: <?php echo htmlspecialchars($app['id']); ?></div>
                                    <div class="header">
                                        <h4><?php echo htmlspecialchars($app['customer_name']); ?></h4>
                                        <div><?php echo htmlspecialchars($app['customer_email']); ?></div>
                                    </div>
                                    <div class="appointment-info">
                                        <div class="info-row">
                                            <span class="info-label">Date:</span>
                                            <span class="info-value"><?php echo htmlspecialchars(date('d M Y', strtotime($app['appointment_date']))); ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Time:</span>
                                            <span class="info-value"><?php echo htmlspecialchars(date('h:i A', strtotime($app['appointment_time']))); ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Service:</span>
                                            <span class="info-value">
                                                <span class="service-tag"><?php echo htmlspecialchars($app['service']); ?></span>
                                            </span>
                                        </div>
                                        <?php if (!empty($app['artist_name'])): ?>
                                        <div class="info-row">
                                            <span class="info-label">Artist:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($app['artist_name']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($app['address'])): ?>
                                        <div class="info-row">
                                            <span class="info-label">Address:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($app['address']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($app['notes'])): ?>
                                        <div class="info-row">
                                            <span class="info-label">Notes:</span>
                                            <span class="info-value"><?php echo nl2br(htmlspecialchars($app['notes'])); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php /* Placeholder for future admin sections can go here */ ?>

        </div> 
    </main> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.admin-nav a');
            const sections = document.querySelectorAll('.admin-section'); // Selects all sections

            // Function to show a section
            function showSection(targetId) {
                sections.forEach(section => {
                    section.classList.toggle('active', section.id === targetId);
                });
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.getAttribute('data-section') === targetId);
                });
            }

            // Event listeners for nav links
            navLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const targetId = link.getAttribute('data-section');
                    showSection(targetId);
                });
            });
        });
    </script>

</body>
</html>