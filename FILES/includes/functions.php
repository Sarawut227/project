<?php
/**
 * Helper Functions
 * LMS System
 */

/**
 * Sanitize input data
 */
function clean_input($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = clean_input($value);
        }
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Display alert message
 */
function set_alert($type, $message) {
    $_SESSION['alert_type'] = $type; // success, danger, warning, info
    $_SESSION['alert_message'] = $message;
}

/**
 * Show alert message
 */
function show_alert() {
    if (isset($_SESSION['alert_message'])) {
        $type = $_SESSION['alert_type'] ?? 'info';
        $message = $_SESSION['alert_message'];
        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        unset($_SESSION['alert_message']);
        unset($_SESSION['alert_type']);
    }
}

/**
 * Format file size
 */
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

/**
 * Format date to Thai format
 */
function thai_date($date, $format = 'd/m/Y H:i') {
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Get file extension
 */
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 */
function get_username() {
    return $_SESSION['username'] ?? null;
}

/**
 * Get current user full name
 */
function get_user_fullname() {
    return $_SESSION['full_name'] ?? 'ผู้ใช้งาน';
}

/**
 * Log activity
 */
function log_activity($conn, $action, $table_name, $record_id, $description) {
    $user_id = get_user_id();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, table_name, record_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $user_id, $action, $table_name, $record_id, $description, $ip_address);
    $stmt->execute();
    $stmt->close();
}

/**
 * Upload file
 */
function upload_file($file, $upload_dir = UPLOAD_PATH) {
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'ไฟล์ไม่ถูกต้อง'];
    }

    // Check upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'message' => 'ไฟล์มีขนาดใหญ่เกินไป'];
        default:
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์'];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'ไฟล์มีขนาดใหญ่เกิน ' . format_file_size(MAX_FILE_SIZE)];
    }

    // Check file extension
    $ext = get_file_extension($file['name']);
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'ไฟล์นามสกุล .' . $ext . ' ไม่ได้รับอนุญาต'];
    }

    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    if (!in_array($mime_type, ALLOWED_MIME_TYPES)) {
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ได้รับอนุญาต'];
    }

    // Generate unique filename
    $new_filename = uniqid() . '_' . time() . '.' . $ext;
    $destination = $upload_dir . $new_filename;

    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'ไม่สามารถย้ายไฟล์ได้'];
    }

    return [
        'success' => true,
        'filename' => $new_filename,
        'original_name' => $file['name'],
        'file_size' => $file['size'],
        'file_type' => $mime_type,
        'file_path' => $destination
    ];
}

/**
 * Delete file
 */
function delete_file($filename, $upload_dir = UPLOAD_PATH) {
    $file_path = $upload_dir . $filename;
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Get site settings
 */
function get_setting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Escape output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}



/**
 * ===================================
 * Permission & Role Functions
 * ===================================
 */

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user role
 */
function get_user_role() {
    return $_SESSION['role'] ?? 'teacher';
}

/**
 * Check if user owns the resource
 */
function can_edit_resource($created_by) {
    if (is_admin()) return true;
    return get_user_id() == $created_by;
}

/**
 * Get WHERE clause for filtering data by user
 */
function get_user_filter($table_alias = '') {
    if (is_admin()) {
        return "1=1";
    }
    
    $user_id = get_user_id();
    $prefix = $table_alias ? $table_alias . '.' : '';
    return "{$prefix}created_by = {$user_id}";
}

/**
 * Check permission and redirect if no access
 */
function check_permission($required_role = 'admin') {
    if ($required_role === 'admin' && !is_admin()) {
        set_alert('danger', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        redirect(ADMIN_URL . '/index.php');
    }
}

/**
 * Get all positions
 */
function get_positions($conn) {
    $result = $conn->query("SELECT * FROM positions WHERE is_active = 1 ORDER BY position_name");
    $positions = [];
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
    return $positions;
}

/**
 * Get position name by ID
 */
function get_position_name($conn, $position_id) {
    if (!$position_id) return '-';
    
    $stmt = $conn->prepare("SELECT position_name FROM positions WHERE position_id = ?");
    $stmt->bind_param("i", $position_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['position_name'];
    }
    
    return '-';
}

?>