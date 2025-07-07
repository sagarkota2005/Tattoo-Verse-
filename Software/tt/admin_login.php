<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

if (!isset($pdo)) {
    die('Database connection failed - PDO not set');
}

$error = '';

// If admin is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle admin login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            error_log("Attempting ADMIN login for username: " . $username);

            // Prepare statement to find an ADMIN user by username
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = 'admin'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                // No admin found with that username OR user exists but isn't admin
                error_log("Admin login failed: No admin user found or role mismatch for username: " . $username);
                $error = "Invalid admin credentials or access denied";
            } else if (!password_verify($password, $user['password'])) {
                // Admin found, but password incorrect
                error_log("Admin login failed: Invalid password for admin username: " . $username);
                $error = "Invalid admin credentials";
            } else {
                // Admin credentials are correct
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Should always be 'admin' here

                error_log("Successful ADMIN login for username: " . $username);
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                exit();
            }
        } catch(PDOException $e) {
            $error = "Database error during admin login. Please try again later.";
            error_log("Admin Login PDOException: " . $e->getMessage());
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
    <style>
        /* Ensure full height and no body scroll */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Prevent body scrollbar */
        }
        /* Define auth-page style here */
        .auth-page {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #007bff; /* Use theme color */
            padding: 1rem;
            overflow: hidden; /* Enforce no scroll on main */
        }
        /* Removed .auth-container styles */
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 500;
            color: white; /* Ensure labels are visible */
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
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
        .login-button {
            background-color: #555; /* Or use theme color like #28a745 */
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .login-button:hover {
            background-color: #333; /* Adjust hover color */
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-align: left;
        }
        .back-link a {
             color: #eee; /* Lighter link color */
             text-decoration: none;
        }
         .back-link a:hover {
             color: white;
             text-decoration: underline;
         }
    </style>
    <script src="js/admin-validation.js"></script>
</head>
<body>
    <main class="auth-page">
        <div class="login-content-wrapper" style="width: 90%; max-width: 400px; color: white; text-align: center;">
            <h2>Admin Login</h2>
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="admin_login.php" onsubmit="return validateAdminLogin();">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
             <div class="back-link" style="margin-top: 1rem; font-size: 0.9em;">
                <a href="index.php">Back to home page</a>
            </div>
        </div> 
    </main>
</body>
</html>
