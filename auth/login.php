<?php
require_once '../config/config.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../customer/dashboard.php');
    }
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check rate limiting
        if (!checkRateLimit('login', 5, 300)) {
            $error = 'Too many login attempts. Please try again later.';
        } else {
            try {
                // Get user from database
                $stmt = $db->prepare("SELECT id, username, email, password, full_name, phone, role, status FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && verifyPassword($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $error = 'Your account is not active. Please contact support.';
                    } else {
                        // Login successful
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['user_role'] = $user['role'];
                        
                        // Set remember me cookie if requested
                        if ($remember) {
                            $token = generateSecureToken();
                            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                            // Store token in database (you'd need to create a remember_tokens table)
                        }
                        
                        // Log the login
                        logActivity($user['id'], 'login', 'User logged in successfully');
                        
                        // Redirect based on role
                        if ($user['role'] === 'admin') {
                            header('Location: ../admin/dashboard.php');
                        } else {
                            header('Location: ../customer/dashboard.php');
                        }
                        exit();
                    }
                } else {
                    $error = 'Invalid email or password';
                    logSecurityEvent('failed_login', "Failed login attempt for email: $email", 'warning');
                }
            } catch (Exception $e) {
                $error = 'An error occurred. Please try again.';
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
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/glassmorphism.css" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #006A4E 0%, #00A86B 50%, #20C997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-2xl);
            box-shadow: var(--glass-shadow);
            padding: 2rem;
            text-align: center;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }
        
        .login-title {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .login-subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-floating .form-control {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid var(--glass-border);
            color: var(--text-dark);
        }
        
        .form-floating .form-control:focus {
            background: rgba(255, 255, 255, 0.4);
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 106, 78, 0.25);
        }
        
        .form-floating label {
            color: var(--text-light);
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-lg);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all var(--transition-normal);
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #004D3A 0%, #006A4E 100%);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }
        
        .divider {
            margin: 1.5rem 0;
            position: relative;
            text-align: center;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--glass-border);
        }
        
        .divider span {
            background: var(--glass-bg);
            padding: 0 1rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        .login-links {
            margin-top: 1.5rem;
        }
        
        .login-links a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-links a:hover {
            text-decoration: underline;
        }
        
        .alert-glass {
            background: rgba(220, 53, 69, 0.1);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: var(--radius-md);
            color: #842029;
            margin-bottom: 1rem;
        }
        
        .success-glass {
            background: rgba(32, 201, 151, 0.1);
            border-color: rgba(32, 201, 151, 0.3);
            color: #0f5132;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            z-index: 10;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .form-check-input {
            margin-right: 0.5rem;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in">
            <div class="login-logo">
                <i class="fas fa-shopping-basket"></i>
            </div>
            
            <h2 class="login-title">Welcome Back!</h2>
            <p class="login-subtitle">Sign in to your account</p>
            
            <?php if ($error): ?>
                <div class="alert alert-glass" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-glass success-glass" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <label for="email">Email address</label>
                </div>
                
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                
                <div class="remember-me">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>
            
            <div class="divider">
                <span>or</span>
            </div>
            
            <div class="login-links">
                <p class="mb-0">Don't have an account? <a href="register.php">Sign up here</a></p>
                <p class="mt-2 mb-0"><a href="../index.php">← Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
