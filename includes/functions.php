<?php
/**
 * Common Functions
 * Professional Grocery Shop Management System
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Format date time
 */
function formatDateTime($datetime, $format = 'd M Y h:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Generate order number
 */
function generateOrderNumber() {
    $prefix = getSiteSetting('order_prefix', 'FGB');
    $timestamp = time();
    $random = rand(100, 999);
    return $prefix . $timestamp . $random;
}

/**
 * Generate transaction number
 */
function generateTransactionNumber() {
    return 'TXN' . time() . rand(1000, 9999);
}

/**
 * Upload file
 */
function uploadFile($file, $directory = 'products') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }

    $uploadDir = UPLOAD_PATH . $directory . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, ALLOWED_IMAGE_TYPES)) {
        return false;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $directory . '/' . $fileName;
    }

    return false;
}

/**
 * Delete file
 */
function deleteFile($filePath) {
    $fullPath = UPLOAD_PATH . $filePath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Resize image
 */
function resizeImage($source, $destination, $width, $height) {
    $imageInfo = getimagesize($source);
    $mime = $imageInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);

    $ratio = min($width / $originalWidth, $height / $originalHeight);
    $newWidth = $originalWidth * $ratio;
    $newHeight = $originalHeight * $ratio;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($newImage, $destination, 90);
            break;
        case 'image/png':
            imagepng($newImage, $destination);
            break;
        case 'image/gif':
            imagegif($newImage, $destination);
            break;
    }

    imagedestroy($image);
    imagedestroy($newImage);
    return true;
}

/**
 * Send email
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    // This would integrate with PHPMailer or similar
    // For now, return true as placeholder
    return true;
}

/**
 * Send SMS (Bangladesh)
 */
function sendSMS($phone, $message) {
    // This would integrate with local SMS gateway
    // For now, return true as placeholder
    return true;
}

/**
 * Log activity
 */
function logActivity($userId, $action, $details = '') {
    global $db;
    try {
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Create notification
 */
function createNotification($userId, $type, $title, $message, $data = null) {
    global $db;
    try {
        $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, data, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            $data ? json_encode($data) : null
        ]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get user cart count
 */
function getCartCount($userId) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Get wishlist count
 */
function getWishlistCount($userId) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Check if product is in wishlist
 */
function isInWishlist($userId, $productId) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetch() ? true : false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Calculate delivery charge
 */
function calculateDeliveryCharge($area, $orderAmount) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT delivery_charge, min_order_amount FROM delivery_areas WHERE area_name = ? AND status = 'active'");
        $stmt->execute([$area]);
        $result = $stmt->fetch();
        
        if ($result) {
            $freeDeliveryThreshold = getSiteSetting('free_delivery_amount', 2000);
            if ($orderAmount >= $freeDeliveryThreshold) {
                return 0;
            }
            return $result['delivery_charge'];
        }
        
        return getSiteSetting('default_delivery_charge', 60);
    } catch (Exception $e) {
        return getSiteSetting('default_delivery_charge', 60);
    }
}

/**
 * Apply coupon discount
 */
function applyCoupon($code, $orderAmount) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE()) AND (usage_limit IS NULL OR used_count < usage_limit)");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();
        
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon'];
        }
        
        if ($orderAmount < $coupon['minimum_amount']) {
            return ['success' => false, 'message' => 'Minimum order amount not met'];
        }
        
        $discount = 0;
        if ($coupon['type'] == 'percentage') {
            $discount = ($orderAmount * $coupon['value']) / 100;
            if ($coupon['maximum_discount'] && $discount > $coupon['maximum_discount']) {
                $discount = $coupon['maximum_discount'];
            }
        } else {
            $discount = $coupon['value'];
        }
        
        return [
            'success' => true,
            'discount' => $discount,
            'coupon' => $coupon
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error applying coupon'];
    }
}

/**
 * Update product stock
 */
function updateProductStock($productId, $quantity, $operation = 'decrease') {
    global $db;
    try {
        $db->beginTransaction();
        
        // Get current stock
        $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $db->rollback();
            return false;
        }
        
        $currentStock = $product['stock_quantity'];
        $newStock = $operation == 'increase' ? $currentStock + $quantity : $currentStock - $quantity;
        
        if ($newStock < 0) {
            $db->rollback();
            return false;
        }
        
        // Update stock
        $stmt = $db->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
        $stmt->execute([$newStock, $productId]);
        
        // Log inventory change
        $stmt = $db->prepare("INSERT INTO inventory_logs (product_id, type, quantity, previous_stock, new_stock, reference_type, created_at) VALUES (?, ?, ?, ?, ?, 'sale', NOW())");
        $stmt->execute([
            $productId,
            $operation == 'increase' ? 'in' : 'out',
            $quantity,
            $currentStock,
            $newStock
        ]);
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

/**
 * Get low stock products
 */
function getLowStockProducts() {
    global $db;
    try {
        $threshold = getSiteSetting('low_stock_threshold', 10);
        $stmt = $db->prepare("SELECT * FROM products WHERE stock_quantity <= ? AND status = 'active' ORDER BY stock_quantity ASC");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Translate text based on current language
 */
function translate($key, $default = '') {
    // This would integrate with language files
    // For now, return the key or default
    return $default ?: $key;
}

/**
 * Get Bangla numbers
 */
function getBanglaNumber($number) {
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return str_replace($english, $bangla, $number);
}
?>
