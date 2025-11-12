<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Grocery BD - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/glassmorphism.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #006A4E 0%, #00A86B 50%, #20C997 100%);
            min-height: 100vh;
        }
        .hero-section {
            padding: 100px 0;
            text-align: center;
            color: white;
        }
        .welcome-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 3rem;
            margin: 2rem auto;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .status-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin: 1rem;
            color: #333;
        }
        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="welcome-card">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-shopping-basket me-3"></i>
                    Fresh Grocery BD
                </h1>
                <p class="lead mb-4">
                    Professional Grocery Shop Management System
                </p>
                <p class="mb-4">
                    Welcome to your new grocery management platform designed specifically for Bangladesh market with modern glassmorphism UI.
                </p>
                
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="test.php" class="btn-glass">
                        <i class="fas fa-cog me-2"></i>
                        System Diagnostic
                    </a>
                    <a href="setup/create_admin.php" class="btn-glass">
                        <i class="fas fa-user-plus me-2"></i>
                        Setup Admin
                    </a>
                    <a href="auth/login.php" class="btn-glass">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </a>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="status-card text-center">
                        <i class="fas fa-database fs-1 text-primary mb-3"></i>
                        <h5>Database Setup</h5>
                        <p>Import the SQL schema and create your database structure.</p>
                        <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-primary btn-sm">
                            Open phpMyAdmin
                        </a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="status-card text-center">
                        <i class="fas fa-user-shield fs-1 text-success mb-3"></i>
                        <h5>Admin Account</h5>
                        <p>Create your administrator account to manage the system.</p>
                        <a href="setup/create_admin.php" class="btn btn-success btn-sm">
                            Create Admin
                        </a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="status-card text-center">
                        <i class="fas fa-rocket fs-1 text-warning mb-3"></i>
                        <h5>Launch System</h5>
                        <p>Start using your grocery management system.</p>
                        <a href="index.php" class="btn btn-warning btn-sm">
                            Launch Website
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <div class="status-card">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-list-check me-2"></i>
                        Setup Checklist
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Prerequisites:</h6>
                            <ul>
                                <li>✅ XAMPP installed and running</li>
                                <li>✅ Apache service started</li>
                                <li>✅ MySQL service started</li>
                                <li>✅ Files uploaded to htdocs</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Setup Steps:</h6>
                            <ol>
                                <li>Run <a href="test.php">System Diagnostic</a></li>
                                <li>Create database in phpMyAdmin</li>
                                <li>Import <code>database/grocery_shop.sql</code></li>
                                <li>Run <a href="setup/create_admin.php">Admin Setup</a></li>
                                <li>Access <a href="index.php">Main Website</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
