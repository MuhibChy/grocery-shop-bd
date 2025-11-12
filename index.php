<?php
require_once 'config/config.php';

// Initialize arrays
$featured_products = [];
$categories = [];

// Get featured products (only if database is connected)
if ($db) {
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE status = 'active' AND featured = 1 ORDER BY created_at DESC LIMIT 8");
        $stmt->execute();
        $featured_products = $stmt->fetchAll();
    } catch (Exception $e) {
        $featured_products = [];
    }

    // Get categories
    try {
        $stmt = $db->prepare("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC LIMIT 6");
        $stmt->execute();
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {
        $categories = [];
    }
}

// If no database connection, show setup message
$show_setup_message = ($db === null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Fresh Groceries Delivered</title>
    <meta name="description" content="Fresh groceries delivered to your doorstep in Bangladesh. Quality products, competitive prices, fast delivery.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/glassmorphism.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-pattern">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-shopping-basket me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link-glass active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-glass" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-glass" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-glass" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-glass" href="contact.php">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                        <a href="customer/cart.php" class="btn btn-glass position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge badge-primary position-absolute top-0 start-100 translate-middle">
                                <?php echo function_exists('getCartCount') ? getCartCount($_SESSION['user_id']) : '0'; ?>
                            </span>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="customer/dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="customer/orders.php">Orders</a></li>
                                <li><a class="dropdown-item" href="customer/profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="auth/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="auth/login.php" class="btn btn-outline-primary">Login</a>
                        <a href="auth/register.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if ($show_setup_message): ?>
    <!-- Setup Message -->
    <div class="alert alert-warning alert-dismissible fade show m-0" role="alert" style="margin-top: 76px;">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <strong>Setup Required!</strong> 
                    Database connection failed. Please complete the setup process.
                </div>
                <div class="ms-3">
                    <a href="test.php" class="btn btn-sm btn-outline-dark me-2">Run Diagnostic</a>
                    <a href="setup/create_admin.php" class="btn btn-sm btn-dark">Setup Now</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: <?php echo $show_setup_message ? '0' : '76px'; ?>; padding: 100px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in">
                        <h1 class="display-4 fw-bold text-dark mb-4">
                            Fresh Groceries<br>
                            <span class="text-primary">Delivered Fast</span>
                        </h1>
                        <p class="lead text-muted mb-4">
                            Get fresh vegetables, fruits, and daily essentials delivered to your doorstep across Bangladesh. Quality guaranteed, prices unbeatable.
                        </p>
                        <div class="hero-buttons">
                            <a href="shop.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Start Shopping
                            </a>
                            <a href="#categories" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-list me-2"></i>
                                Browse Categories
                            </a>
                        </div>
                        
                        <div class="hero-features mt-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="feature-item text-center">
                                        <i class="fas fa-truck text-primary fs-2 mb-2"></i>
                                        <h6>Free Delivery</h6>
                                        <small class="text-muted">On orders over ৳2000</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="feature-item text-center">
                                        <i class="fas fa-leaf text-success fs-2 mb-2"></i>
                                        <h6>Fresh Products</h6>
                                        <small class="text-muted">Directly from farms</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="feature-item text-center">
                                        <i class="fas fa-clock text-warning fs-2 mb-2"></i>
                                        <h6>Quick Delivery</h6>
                                        <small class="text-muted">Same day delivery</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image slide-in-right">
                        <div class="placeholder-image d-flex align-items-center justify-content-center rounded-glass" style="height: 400px; background: var(--gradient-primary); color: white; box-shadow: var(--shadow-xl);">
                            <div class="text-center">
                                <i class="fas fa-shopping-basket fa-5x mb-3"></i>
                                <h4>Fresh Groceries</h4>
                                <p>Quality Products Delivered</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Shop by Categories</h2>
                <p class="text-muted">Explore our wide range of fresh products</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="glass-card text-center p-4 h-100">
                        <div class="category-icon mb-3">
                            <i class="fas fa-carrot text-primary fs-1"></i>
                        </div>
                        <h6 class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></h6>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($category['description']); ?></p>
                        <a href="shop.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-primary btn-sm">
                            Shop Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Featured Products</h2>
                <p class="text-muted">Handpicked fresh items just for you</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="glass-card product-card h-100">
                        <div class="product-image">
                            <img src="assets/uploads/products/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php if ($product['discount_price']): ?>
                                <span class="badge badge-success position-absolute top-0 start-0 m-2">
                                    Sale
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body-glass">
                            <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                            
                            <div class="price-section mb-3">
                                <?php if ($product['discount_price']): ?>
                                    <span class="text-primary fw-bold fs-5"><?php echo function_exists('formatCurrency') ? formatCurrency($product['discount_price']) : '৳ ' . number_format($product['discount_price'], 2); ?></span>
                                    <span class="text-muted text-decoration-line-through ms-2"><?php echo function_exists('formatCurrency') ? formatCurrency($product['price']) : '৳ ' . number_format($product['price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="text-primary fw-bold fs-5"><?php echo function_exists('formatCurrency') ? formatCurrency($product['price']) : '৳ ' . number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-actions d-flex gap-2">
                                <button class="btn btn-primary flex-fill" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-cart-plus me-1"></i>
                                    Add to Cart
                                </button>
                                <button class="btn btn-outline-primary" onclick="toggleWishlist(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="shop.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>
                    View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-shopping-basket me-2"></i>
                        <?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-muted">Your trusted partner for fresh groceries in Bangladesh. Quality products, competitive prices, and fast delivery.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="shop.php" class="text-muted text-decoration-none">Shop</a></li>
                        <li><a href="categories.php" class="text-muted text-decoration-none">Categories</a></li>
                        <li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Customer Care</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Track Order</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Returns</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQs</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <div class="contact-info">
                        <p class="text-muted mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <?php echo SITE_PHONE; ?>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <?php echo SITE_EMAIL; ?>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Dhaka, Bangladesh
                        </p>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
