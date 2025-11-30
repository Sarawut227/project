<?php
/**
 * Main Configuration
 * LMS System
 */

// Site Settings
define('SITE_NAME', 'ระบบบทเรียนออนไลน์');
define('SITE_URL', 'https://bigzdemo19.live');
define('ADMIN_URL', SITE_URL . '/admin');

// Path Settings
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/attachments/');
define('UPLOAD_URL', SITE_URL . '/uploads/attachments/');

// Upload Settings
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xlsx', 'xls', 'ppt', 'pptx', 'txt']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain'
]);

// Session Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_lifetime', 0);

// Timezone
date_default_timezone_set('Asia/Bangkok');

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Error Reporting (Production) - Uncomment below when going live
// error_reporting(0);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', ROOT_PATH . '/logs/error.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>