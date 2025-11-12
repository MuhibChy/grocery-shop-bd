# 🛒 Professional Grocery Shop Management System

A comprehensive grocery shop management system designed specifically for Bangladesh market with modern glassmorphism UI, multi-platform responsive design, and complete e-commerce functionality.

![Bangladesh Flag](https://img.shields.io/badge/Made%20for-Bangladesh-green?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiBmaWxsPSIjMDA2QTRFIi8+CjxjaXJjbGUgY3g9IjEwIiBjeT0iMTIiIHI9IjQiIGZpbGw9IiNGRjAwMDAiLz4KPC9zdmc+)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

## ✨ Features

### 🎨 **Modern Glassmorphism UI**
- Translucent cards and panels with frosted glass effect
- Bangladesh green color theme (৳ currency support)
- Mobile-first responsive design
- Smooth animations and transitions
- Dark/Light mode support

### 👨‍💼 **Admin Dashboard**
- Real-time analytics and charts
- Product & inventory management (CRUD)
- Order management and tracking
- Customer management
- **Income/Expenditure accounting system**
- Sales reports (daily/weekly/monthly)
- Low stock alerts
- Bulk import/export (CSV)

### 🛍️ **Customer Features**
- User registration and authentication
- Product browsing with search & filters
- Shopping cart and wishlist
- Order placement and tracking
- Multiple delivery options
- Coupon and discount system
- Order history and invoices
- Profile management

### 💳 **Payment Integration**
- **bKash** integration ready
- **Nagad** support
- **Rocket** payment gateway
- Cash on Delivery (COD)
- Card payments
- Secure checkout process

### 📱 **Multi-Platform Support**
- Responsive design (Mobile, Tablet, Desktop)
- Progressive Web App (PWA) ready
- Touch-friendly interface
- Mobile navigation
- Optimized for all screen sizes

### 🇧🇩 **Bangladesh-Specific Features**
- Bangladeshi Taka (৳) currency
- Local phone number validation
- Delivery areas (Dhaka, Chittagong, Sylhet)
- Multi-language support (English + Bangla)
- Local business practices integration

## 🚀 **Live Demo**

🌐 **[View Live Demo](https://your-username.github.io/grocery-shop-bd/)**

### Demo Credentials:
- **Admin Login:** `admin@groceryshop.bd` / `admin123`
- **Customer:** Register new account or use demo

## 📋 **Requirements**

- **PHP:** 8.0 or higher
- **MySQL:** 8.0 or higher
- **Web Server:** Apache/Nginx
- **Extensions:** PDO, MySQLi, GD, OpenSSL, mbstring

## ⚡ **Quick Setup**

### 1. **Clone Repository**
```bash
git clone https://github.com/your-username/grocery-shop-bd.git
cd grocery-shop-bd
```

### 2. **Database Setup**
```sql
-- Create database
CREATE DATABASE grocery_shop_bd;

-- Import schema
mysql -u root -p grocery_shop_bd < database/grocery_shop.sql
```

### 3. **Configuration**
```php
// Update config/config.php with your settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'grocery_shop_bd');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. **Create Admin User**
```bash
# Run setup script (delete after use)
http://your-domain.com/setup/create_admin.php
```

### 5. **Access Application**
- **Website:** `http://your-domain.com/`
- **Admin Panel:** `http://your-domain.com/admin/dashboard.php`

## 📁 **Project Structure**

```
grocery-shop-bd/
├── 📁 admin/                 # Admin panel
│   ├── dashboard.php         # Analytics dashboard
│   ├── products.php          # Product management
│   ├── orders.php            # Order management
│   └── accounting.php        # Income/Expense tracking
├── 📁 assets/                # Static assets
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript files
│   └── images/               # Images
├── 📁 auth/                  # Authentication
│   ├── login.php             # Login page
│   ├── register.php          # Registration
│   └── logout.php            # Logout handler
├── 📁 config/                # Configuration
│   ├── config.php            # Main config
│   └── database.php          # Database connection
├── 📁 customer/              # Customer panel
│   ├── dashboard.php         # Customer dashboard
│   ├── cart.php              # Shopping cart
│   └── orders.php            # Order history
├── 📁 database/              # Database files
│   └── grocery_shop.sql      # Database schema
├── 📁 includes/              # Shared functions
│   ├── functions.php         # Common functions
│   └── security.php          # Security functions
└── index.php                 # Homepage
```

## 🛠️ **Technology Stack**

### **Backend**
- **PHP 8+** - Server-side logic
- **MySQL** - Database management
- **PDO** - Database abstraction

### **Frontend**
- **HTML5** - Semantic markup
- **CSS3** - Glassmorphism styling
- **JavaScript** - Interactive features
- **Bootstrap 5** - Responsive framework

### **Features**
- **Chart.js** - Analytics visualization
- **Font Awesome** - Icon library
- **AOS.js** - Scroll animations

## 💰 **Accounting System**

### **Income Tracking**
- Product sales revenue
- Delivery charges
- Service fees
- Commission tracking

### **Expense Management**
- Product purchases
- Staff salaries
- Rent and utilities
- Marketing expenses
- Transportation costs

### **Reports**
- Daily/Monthly/Yearly summaries
- Profit & Loss statements
- Cash flow analysis
- Tax preparation data

## 🔒 **Security Features**

- **Password Hashing** - bcrypt encryption
- **SQL Injection Protection** - Prepared statements
- **XSS Prevention** - Input sanitization
- **CSRF Protection** - Token validation
- **Rate Limiting** - Brute force protection
- **Session Security** - Secure session handling

## 📱 **Mobile Features**

- **Touch Navigation** - Mobile-friendly interface
- **Swipe Gestures** - Product browsing
- **Offline Support** - PWA capabilities
- **Push Notifications** - Order updates
- **Mobile Payments** - bKash/Nagad integration

## 🌐 **Deployment Options**

### **Shared Hosting**
- Upload files via FTP/cPanel
- Import database via phpMyAdmin
- Update configuration settings

### **VPS/Cloud**
- Apache/Nginx setup
- SSL certificate installation
- Database optimization
- Caching configuration

### **GitHub Pages** (Static Demo)
- Frontend-only demonstration
- No backend functionality
- Perfect for showcasing UI/UX

## 🤝 **Contributing**

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 **Acknowledgments**

- **Bootstrap Team** - UI Framework
- **Font Awesome** - Icons
- **Chart.js** - Analytics charts
- **Bangladesh Government** - Local business guidelines

## 📞 **Support**

- **Email:** support@freshgrocerybd.com
- **Phone:** +8801700000000
- **Website:** [Fresh Grocery BD](https://freshgrocerybd.com)

## 🔗 **Links**

- **Live Demo:** [View Demo](https://your-username.github.io/grocery-shop-bd/)
- **Documentation:** [Full Docs](https://github.com/your-username/grocery-shop-bd/wiki)
- **Issues:** [Report Bug](https://github.com/your-username/grocery-shop-bd/issues)
- **Discussions:** [Community](https://github.com/your-username/grocery-shop-bd/discussions)

---

**Made with ❤️ for Bangladesh 🇧🇩**

*Professional Grocery Shop Management System - Empowering local businesses with modern technology*
