<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

if (!isset($pdo)) {
    die('Database connection failed - PDO not set');
}

$error = '';
$success = '';

// Check for success message from registration
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the message after displaying
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the page they came from or home page
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    header("Location: $redirect");
    exit();
}

// No redirect needed - allow direct access to login page

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $password = trim($_POST['password']);
        
        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password";
        } else {

        try {
            // Debug info
            error_log("Attempting login for email: " . $email);
            
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                error_log("No user found with email: " . $email);
                $error = "Invalid email or password";
            } else if (!password_verify($password, $user['password'])) {
                error_log("Invalid password for user: " . $email);
                $error = "Invalid email or password";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                error_log("Successful login for user: " . $email);
                // Always redirect to the standard user area (or intended redirect)
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header("Location: $redirect");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Database error. Please try again later.";
            error_log("Login error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }
    } elseif ($_POST['action'] === 'register') {
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } else {
            try {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "Email already registered";
                } else {
                    // Create new user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'client')");
                    $stmt->execute([$username, $email, $hashed_password]);

                    $success = "Registration successful! Please login.";
                }
            } catch(PDOException $e) {
                $error = "Registration failed. Please try again.";
                error_log($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="js/form-validation.js"></script>
</head>
<body>

    <main class="auth-page">
        <div class="auth-container">
            <div class="outer-box">
                <div class="auth-box" id="authBox">
                    
                <!-- Login Form -->
                <div class="form-section" id="loginSection">
                    <h2>LOGIN TO ACCOUNT</h2>
                    <?php if ($error): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php // Display success message (from session or local) ?>
                    <?php if ($success): ?>
                        <div class="success-message">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" onsubmit="validateLoginForm(event)">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <input type="email" id="login-email" name="email" placeholder="EMAIL" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="login-password" name="password" placeholder="PASSWORD" required>
                    </div>
                    <div class="form-footer">
                        
                    </div>
                    <button type="submit" class="login-button">LOGIN</button>
                </form>
                    <div class="form-footer register-footer">
                        <p>Don't have an account?</p>
                        <a href="register.php" class="register-button" style="color: white; text-decoration: none;">
                            <i class="fas fa-user-plus"></i>
                            CREATE NEW ACCOUNT
                        </a>
                    </div>
                </div>


                </div>
            </div>
        </div>
    </main>




</body>
</html>
