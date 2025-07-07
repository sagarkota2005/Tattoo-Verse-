<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

$error = '';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle admin login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = trim($_POST['userid']);
    $password = trim($_POST['password']);
    
    if (empty($userid) || empty($password)) {
        $error = "Please enter both User ID and password";
    } else {
        // Check against hardcoded admin credentials
        if ($userid === 'omkar1337r' && $password === 'qazwsxedc') {
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_userid'] = $userid;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid User ID or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">Tattoo Verse</a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <!--<li><a href="appointments.php">Book Appointment</a></li>-->
                <li><a href="aftercare.php">Aftercare</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="admin.php" class="active">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-page">
        <div class="auth-container">
            <div class="outer-box">
                <div class="auth-box">
                    <h2>Admin Login</h2>
                    <?php if ($error): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="admin.php" class="auth-form">
                        <div class="form-group">
                            <label for="userid">User ID</label>
                            <input type="text" id="userid" name="userid" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="js/main.js"></script>
</body>
</html>
