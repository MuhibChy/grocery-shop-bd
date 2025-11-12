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

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = cleanInput($_POST['full_name'] ?? '');
    $username = cleanInput($_POST['username'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Validate required fields
    $required_fields = [
        'full_name' => 'Full name',
        'username' => 'Username',
        'email' => 'Email',
        'phone' => 'Phone number',
        'password' => 'Password'
    ];
    
    $validation_errors = validateRequired($required_fields, $_POST);
    
    if (!empty($validation_errors)) {
        $error = implode('<br>', $validation_errors);
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address';
    } elseif (!validateBDPhone($phone)) {
        $error = 'Please enter a valid Bangladesh phone number';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (!empty(validatePasswordStrength($password))) {
        $error = implode('<br>', validatePasswordStrength($password));
    } elseif (!$terms) {
        $error = 'Please accept the terms and conditions';
    } else {
        // Check rate limiting
        if (!checkRateLimit('register', 3, 300)) {
            $error = 'Too many registration attempts. Please try again later.';
        } else {
            try {
                // Check if username or email already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->fetch()) {
                    $error = 'Username or email already exists';
                } else {
                    // Create new user
                    $hashed_password = hashPassword($password);
                    
                    $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'customer', 'active', NOW())");
                    
                    if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                        $user_id = $db->lastInsertId();
                        
                        // Log the registration
                        logActivity($user_id, 'register', 'User registered successfully');
                        
                        // Create welcome notification
                        createNotification($user_id, 'general', 'Welcome to ' . SITE_NAME, 'Thank you for joining us! Start shopping for fresh groceries now.');
                        
                        $success = 'Registration successful! You can now login to your account.';
                        
                        // Optional: Auto-login the user
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['email'] = $email;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['user_role'] = 'customer';
                        
                        // Redirect to customer dashboard after 2 seconds
                        header("refresh:2;url=../customer/dashboard.php");
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
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
    <title>Register - <?php echo SITE_NAME; ?></title>
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
            padding: 2rem 0;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }
        
        .register-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-2xl);
            box-shadow: var(--glass-shadow);
            padding: 2rem;
            text-align: center;
        }
        
        .register-logo {
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
        
        .register-title {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .register-subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
            text-align: left;
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
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        .btn-register {
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
        
        .btn-register:hover {
            background: linear-gradient(135deg, #004D3A 0%, #006A4E 100%);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .terms-check {
            text-align: left;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .terms-check a {
            color: var(--primary-green);
            text-decoration: none;
        }
        
        .terms-check a:hover {
            text-decoration: underline;
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
        
        .register-links {
            margin-top: 1.5rem;
        }
        
        .register-links a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-links a:hover {
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
            text-align: left;
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
        
        @media (max-width: 576px) {
            .register-container {
                padding: 1rem;
            }
            
            .register-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card fade-in">
            <div class="register-logo">
                <i class="fas fa-user-plus"></i>
            </div>
            
            <h2 class="register-title">Create Account</h2>
            <p class="register-subtitle">Join us and start shopping!</p>
            
            <?php if ($error): ?>
                <div class="alert alert-glass" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-glass success-glass" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <br><small>Redirecting to dashboard...</small>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required value="<?php echo htmlspecialchars($full_name ?? ''); ?>">
                            <label for="full_name">Full Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                            <label for="username">Username</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <label for="email">Email address</label>
                </div>
                
                <div class="form-floating">
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+8801XXXXXXXXX" required value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    <label for="phone">Phone Number</label>
                    <small class="text-muted">Format: +8801XXXXXXXXX</small>
                </div>
                
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
                
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    <label for="confirm_password">Confirm Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
                <div id="passwordMatch"></div>
                
                <div class="terms-check">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="../pages/terms.php" target="_blank">Terms and Conditions</a> and <a href="../pages/privacy.php" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register" id="submitBtn">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>
            
            <div class="divider">
                <span>or</span>
            </div>
            
            <div class="register-links">
                <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
                <p class="mt-2 mb-0"><a href="../index.php">← Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            else feedback.push('At least 8 characters');
            
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('One uppercase letter');
            
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('One lowercase letter');
            
            if (/[0-9]/.test(password)) strength++;
            else feedback.push('One number');
            
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('One special character');
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength < 2) {
                strengthText = 'Weak';
                strengthClass = 'strength-weak';
            } else if (strength < 4) {
                strengthText = 'Medium';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong';
                strengthClass = 'strength-strong';
            }
            
            if (password.length > 0) {
                strengthDiv.innerHTML = `<span class="${strengthClass}">Password strength: ${strengthText}</span>`;
                if (feedback.length > 0) {
                    strengthDiv.innerHTML += `<br><small>Missing: ${feedback.join(', ')}</small>`;
                }
            } else {
                strengthDiv.innerHTML = '';
            }
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>';
                } else {
                    matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please accept the terms and conditions');
                return;
            }
            
            // Disable submit button to prevent double submission
            document.getElementById('submitBtn').disabled = true;
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (!alert.classList.contains('success-glass')) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }
            });
        }, 5000);
    </script>
</body>
</html>
