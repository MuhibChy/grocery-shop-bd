<?php
require_once '../config/config.php';
requireAdmin();

// Get dashboard statistics
try {
    // Total orders
    $stmt = $db->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue FROM orders WHERE status != 'cancelled'");
    $stmt->execute();
    $order_stats = $stmt->fetch();
    
    // Today's orders
    $stmt = $db->prepare("SELECT COUNT(*) as today_orders, SUM(total_amount) as today_revenue FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'");
    $stmt->execute();
    $today_stats = $stmt->fetch();
    
    // Total customers
    $stmt = $db->prepare("SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'");
    $stmt->execute();
    $customer_stats = $stmt->fetch();
    
    // Total products
    $stmt = $db->prepare("SELECT COUNT(*) as total_products FROM products WHERE status = 'active'");
    $stmt->execute();
    $product_stats = $stmt->fetch();
    
    // Low stock products
    $low_stock_products = getLowStockProducts();
    
    // Recent orders
    $stmt = $db->prepare("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll();
    
    // Monthly revenue data for chart
    $stmt = $db->prepare("SELECT MONTH(created_at) as month, SUM(total_amount) as revenue FROM orders WHERE YEAR(created_at) = YEAR(CURDATE()) AND status != 'cancelled' GROUP BY MONTH(created_at) ORDER BY month");
    $stmt->execute();
    $monthly_revenue = $stmt->fetchAll();
    
} catch (Exception $e) {
    $order_stats = ['total_orders' => 0, 'total_revenue' => 0];
    $today_stats = ['today_orders' => 0, 'today_revenue' => 0];
    $customer_stats = ['total_customers' => 0];
    $product_stats = ['total_products' => 0];
    $low_stock_products = [];
    $recent_orders = [];
    $monthly_revenue = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/glassmorphism.css" rel="stylesheet">
    <link href="../assets/css/responsive.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border-right: 1px solid var(--glass-border);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background: var(--gradient-light);
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-dark);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: var(--glass-green-light);
            color: var(--primary-green);
            text-decoration: none;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .top-navbar {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .stats-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            text-align: center;
            transition: all var(--transition-normal);
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: var(--text-light);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .chart-container {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-backdrop);
            -webkit-backdrop-filter: var(--glass-backdrop);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            height: 400px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform var(--transition-normal);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-pattern">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4 class="text-primary fw-bold mb-0">
                <i class="fas fa-shopping-basket me-2"></i>
                Admin Panel
            </h4>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        <i class="fas fa-tags"></i>
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users"></i>
                        Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="accounting.php">
                        <i class="fas fa-calculator"></i>
                        Accounting
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        View Site
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-glass mobile-menu-btn me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="mb-0">Dashboard</h2>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="container-fluid p-4">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card fade-in">
                        <div class="stats-icon" style="background: var(--gradient-primary);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-number"><?php echo number_format($order_stats['total_orders'] ?? 0); ?></div>
                        <div class="stats-label">Total Orders</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card fade-in">
                        <div class="stats-icon" style="background: var(--success-green);">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stats-number"><?php echo formatCurrency($order_stats['total_revenue'] ?? 0); ?></div>
                        <div class="stats-label">Total Revenue</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card fade-in">
                        <div class="stats-icon" style="background: #17a2b8;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-number"><?php echo number_format($customer_stats['total_customers'] ?? 0); ?></div>
                        <div class="stats-label">Total Customers</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card fade-in">
                        <div class="stats-icon" style="background: #ffc107;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stats-number"><?php echo number_format($product_stats['total_products'] ?? 0); ?></div>
                        <div class="stats-label">Total Products</div>
                    </div>
                </div>
            </div>

            <!-- Today's Stats -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="glass-card p-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-calendar-day text-primary me-2"></i>
                            Today's Performance
                        </h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-primary"><?php echo number_format($today_stats['today_orders'] ?? 0); ?></h3>
                                    <small class="text-muted">Orders Today</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-success"><?php echo formatCurrency($today_stats['today_revenue'] ?? 0); ?></h3>
                                    <small class="text-muted">Revenue Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="glass-card p-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Low Stock Alert
                        </h5>
                        <?php if (count($low_stock_products) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($low_stock_products, 0, 5) as $product): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0">
                                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                                        <span class="badge badge-warning"><?php echo $product['stock_quantity']; ?> left</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($low_stock_products) > 5): ?>
                                <a href="products.php?filter=low_stock" class="btn btn-outline-primary btn-sm mt-2">
                                    View All (<?php echo count($low_stock_products); ?>)
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">All products are well stocked!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Orders -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Monthly Revenue
                        </h5>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="glass-card p-4 h-100">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Recent Orders
                        </h5>
                        <div class="recent-orders" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <h6 class="mb-1">#<?php echo $order['order_number']; ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['full_name']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary"><?php echo formatCurrency($order['total_amount']); ?></div>
                                        <span class="badge badge-<?php echo $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="orders.php" class="btn btn-outline-primary btn-sm w-100 mt-3">
                            View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const monthlyData = <?php echo json_encode($monthly_revenue); ?>;
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const revenueData = new Array(12).fill(0);
        
        monthlyData.forEach(item => {
            revenueData[item.month - 1] = parseFloat(item.revenue);
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Revenue (৳)',
                    data: revenueData,
                    borderColor: '#006A4E',
                    backgroundColor: 'rgba(0, 106, 78, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Auto-refresh dashboard every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
