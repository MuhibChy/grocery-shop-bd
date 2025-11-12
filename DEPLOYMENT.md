# 🚀 Deployment Guide

## GitHub Pages Deployment (Frontend Demo)

Since GitHub Pages only supports static files, we'll create a demo version that showcases the UI without backend functionality.

### Option 1: GitHub Pages (Recommended for Demo)

1. **Create GitHub Repository**
2. **Push Code to GitHub**
3. **Enable GitHub Pages**
4. **Access Demo URL**

### Option 2: Full Hosting (Complete Functionality)

For full PHP/MySQL functionality, use these hosting providers:

#### **Free Hosting Options:**
- **InfinityFree** - Free PHP/MySQL hosting
- **000WebHost** - Free hosting with PHP support
- **AwardSpace** - Free hosting with database

#### **Paid Hosting Options:**
- **Hostinger** - Affordable shared hosting
- **Bluehost** - Reliable WordPress hosting
- **DigitalOcean** - VPS hosting
- **AWS** - Cloud hosting

## Environment Configuration

### Production Settings
```php
// config/production.php
define('DEVELOPMENT', false);
error_reporting(0);
ini_set('display_errors', 0);

// Use environment variables for sensitive data
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'grocery_shop_bd');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
```

### Security Checklist
- [ ] Remove setup files (`setup/`, `test.php`)
- [ ] Update default passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure proper file permissions
- [ ] Set up regular backups
- [ ] Enable security headers
- [ ] Configure firewall rules

## Database Migration

### Export from Local
```bash
mysqldump -u root -p grocery_shop_bd > database_backup.sql
```

### Import to Production
```bash
mysql -u username -p database_name < database_backup.sql
```

## File Upload Configuration

### Create Upload Directories
```bash
mkdir -p assets/uploads/products
mkdir -p assets/uploads/categories
mkdir -p assets/uploads/users
chmod 755 assets/uploads/
```

### .htaccess for Security
```apache
# Prevent direct access to PHP files in uploads
<Files "*.php">
    Order Deny,Allow
    Deny from All
</Files>
```

## Performance Optimization

### Enable Gzip Compression
```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

### Browser Caching
```apache
# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

## SSL Configuration

### Let's Encrypt (Free SSL)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d yourdomain.com
```

## Monitoring & Maintenance

### Log Files to Monitor
- Apache/Nginx error logs
- PHP error logs
- Application logs (`logs/`)
- Database slow query logs

### Regular Maintenance Tasks
- Database optimization
- Log file rotation
- Security updates
- Backup verification
- Performance monitoring

## Troubleshooting

### Common Issues
1. **Database Connection Errors**
   - Check credentials in config
   - Verify database server is running
   - Check firewall settings

2. **File Permission Errors**
   - Set proper permissions (755 for directories, 644 for files)
   - Check upload directory permissions

3. **Session Issues**
   - Verify session directory permissions
   - Check session configuration in PHP

4. **Memory Limit Errors**
   - Increase PHP memory_limit
   - Optimize database queries
   - Enable caching

### Debug Mode
```php
// Enable for troubleshooting only
define('DEVELOPMENT', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Backup Strategy

### Automated Backups
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p password database_name > backup_$DATE.sql
tar -czf files_backup_$DATE.tar.gz /path/to/website/
```

### Backup Schedule
- **Daily:** Database backups
- **Weekly:** Full file backups
- **Monthly:** Archive old backups

## Support & Resources

- **Documentation:** Full deployment guides
- **Community:** GitHub Discussions
- **Issues:** Bug reports and feature requests
- **Email:** Technical support contact
