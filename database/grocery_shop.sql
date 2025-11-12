-- Professional Grocery Shop Management System Database
-- Created for Bangladesh Market with Glassmorphism UI
-- Supports multi-language, payments, and accounting

CREATE DATABASE IF NOT EXISTS grocery_shop_bd;
USE grocery_shop_bd;

-- Users table (Admin and Customers)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Customer addresses
CREATE TABLE customer_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_type ENUM('home', 'office', 'other') DEFAULT 'home',
    full_address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    area VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Product categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_bn VARCHAR(100), -- Bangla name
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Suppliers
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    name_bn VARCHAR(200), -- Bangla name
    description TEXT,
    description_bn TEXT,
    category_id INT NOT NULL,
    supplier_id INT,
    sku VARCHAR(50) UNIQUE,
    barcode VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2),
    discount_price DECIMAL(10,2),
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 5,
    unit VARCHAR(20) DEFAULT 'piece', -- kg, liter, piece, etc.
    weight DECIMAL(8,2),
    dimensions VARCHAR(50),
    image VARCHAR(255),
    gallery TEXT, -- JSON array of images
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Shopping cart
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Wishlist
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Coupons and discounts
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    maximum_discount DECIMAL(10,2),
    usage_limit INT,
    used_count INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cash_on_delivery', 'bkash', 'nagad', 'rocket', 'card', 'bank_transfer') NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    coupon_code VARCHAR(50),
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'BDT',
    
    -- Delivery information
    delivery_type ENUM('home_delivery', 'pickup') DEFAULT 'home_delivery',
    delivery_address TEXT,
    delivery_city VARCHAR(50),
    delivery_area VARCHAR(50),
    delivery_phone VARCHAR(20),
    delivery_date DATE,
    delivery_time_slot VARCHAR(50),
    
    -- Payment information
    transaction_id VARCHAR(100),
    payment_details TEXT, -- JSON for payment gateway response
    
    notes TEXT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Order status history
CREATE TABLE order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    comment TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Income and Expenditure Accounting System
CREATE TABLE account_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_bn VARCHAR(100),
    type ENUM('income', 'expense') NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT NOT NULL,
    reference_type ENUM('order', 'purchase', 'salary', 'rent', 'utility', 'other') DEFAULT 'other',
    reference_id INT, -- Can link to order_id or other references
    payment_method ENUM('cash', 'bank', 'bkash', 'nagad', 'rocket', 'card') DEFAULT 'cash',
    transaction_date DATE NOT NULL,
    created_by INT NOT NULL,
    receipt_image VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES account_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Inventory tracking
CREATE TABLE inventory_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    previous_stock INT NOT NULL,
    new_stock INT NOT NULL,
    reference_type ENUM('purchase', 'sale', 'adjustment', 'return') NOT NULL,
    reference_id INT,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Notifications
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    type ENUM('order', 'stock', 'payment', 'general') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    data TEXT, -- JSON data for additional info
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- System settings
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Delivery areas and charges
CREATE TABLE delivery_areas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    area_name VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    delivery_charge DECIMAL(8,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    delivery_time VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews and ratings
CREATE TABLE product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_title VARCHAR(200),
    review_text TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);

-- Insert default data
INSERT INTO users (username, email, password, full_name, phone, role) VALUES 
('admin', 'admin@groceryshop.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '+8801700000000', 'admin');

INSERT INTO categories (name, name_bn, description) VALUES 
('Vegetables', 'সবজি', 'Fresh vegetables and greens'),
('Fruits', 'ফল', 'Fresh seasonal fruits'),
('Dairy & Eggs', 'দুগ্ধজাত ও ডিম', 'Milk, cheese, yogurt and eggs'),
('Meat & Fish', 'মাংস ও মাছ', 'Fresh meat and fish'),
('Rice & Grains', 'চাল ও শস্য', 'Rice, wheat, lentils and grains'),
('Spices & Condiments', 'মসলা ও সস', 'Spices, herbs and condiments'),
('Snacks & Beverages', 'নাস্তা ও পানীয়', 'Snacks, drinks and beverages'),
('Personal Care', 'ব্যক্তিগত যত্ন', 'Personal hygiene and care products'),
('Household Items', 'গৃহস্থালী সামগ্রী', 'Cleaning supplies and household items');

INSERT INTO account_categories (name, name_bn, type, description) VALUES 
('Product Sales', 'পণ্য বিক্রয়', 'income', 'Revenue from product sales'),
('Delivery Charges', 'ডেলিভারি চার্জ', 'income', 'Income from delivery services'),
('Product Purchase', 'পণ্য ক্রয়', 'expense', 'Cost of purchasing products'),
('Staff Salary', 'কর্মচারী বেতন', 'expense', 'Employee salary payments'),
('Rent', 'ভাড়া', 'expense', 'Shop/office rent'),
('Utilities', 'ইউটিলিটি', 'expense', 'Electricity, water, internet bills'),
('Marketing', 'বিপণন', 'expense', 'Advertising and promotional expenses'),
('Transportation', 'পরিবহন', 'expense', 'Vehicle and transportation costs');

INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES 
('site_name', 'Fresh Grocery BD', 'text', 'Website name'),
('site_currency', 'BDT', 'text', 'Default currency'),
('currency_symbol', '৳', 'text', 'Currency symbol'),
('tax_rate', '0', 'number', 'Tax rate percentage'),
('min_order_amount', '500', 'number', 'Minimum order amount'),
('free_delivery_amount', '2000', 'number', 'Free delivery threshold'),
('default_delivery_charge', '60', 'number', 'Default delivery charge'),
('site_email', 'info@freshgrocerybd.com', 'text', 'Site contact email'),
('site_phone', '+8801700000000', 'text', 'Site contact phone'),
('order_prefix', 'FGB', 'text', 'Order number prefix'),
('low_stock_threshold', '10', 'number', 'Low stock alert threshold');

INSERT INTO delivery_areas (area_name, city, delivery_charge, min_order_amount) VALUES 
('Dhanmondi', 'Dhaka', 60.00, 500.00),
('Gulshan', 'Dhaka', 80.00, 500.00),
('Uttara', 'Dhaka', 100.00, 500.00),
('Mirpur', 'Dhaka', 70.00, 500.00),
('Old Dhaka', 'Dhaka', 60.00, 500.00),
('Chittagong City', 'Chittagong', 120.00, 800.00),
('Sylhet City', 'Sylhet', 150.00, 1000.00);
