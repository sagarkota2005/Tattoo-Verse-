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

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    error_log("Registration attempt - Username: " . $username . ", Email: " . $email);

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        try {
            error_log("Attempting registration for email: " . $email);
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                error_log("Email already exists: " . $email);
                $error = "Email already registered";
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'client', NOW())");
                
                try {
                    if ($stmt->execute([$username, $email, $hashed_password])) {
                        error_log("Successfully registered user: " . $email);
                        $success = "Registration successful! You can now login.";
                        // Store success message in session
                        $_SESSION['success'] = "Registration successful! You can now login.";
                        // Redirect to login page immediately
                        header("Location: login.php");
                        exit();
                    } else {
                        error_log("Failed to insert new user: " . $email);
                        $error = "Registration failed. Please try again.";
                    }
                } catch (PDOException $insertError) {
                    error_log("Database error during user insertion: " . $insertError->getMessage());
                    $error = "Registration failed. Database error.";
                }
            }
        } catch(PDOException $e) {
            $error = "Registration failed. Database error.";
            error_log("Registration error for " . $email . ": " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tattoo Verse</title>
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
                    <div class="form-section">
                        <h2>CREATE ACCOUNT</h2>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="error-message">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="success-message">
                                <i class="fas fa-check-circle"></i>
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="register.php" class="register-form" onsubmit="validateRegistrationForm(event)">
                            <div class="form-group">
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="username" name="username" placeholder="USERNAME" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="EMAIL" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="PASSWORD (MIN. 8 CHARACTERS)" required minlength="8">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-with-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="CONFIRM PASSWORD" required minlength="8">
                                </div>
                            </div>
                            <button type="submit" class="login-button">
                                <i class="fas fa-user-plus"></i>
                                CREATE ACCOUNT
                            </button>
                        </form>
                        <div class="form-footer">
                            <a href="login.php" class="register-button">ALREADY HAVE AN ACCOUNT? LOGIN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
