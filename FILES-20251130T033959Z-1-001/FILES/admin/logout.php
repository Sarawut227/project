<?php
/**
 * Admin Logout
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Log activity before logout
if (is_logged_in()) {
    log_activity($conn, 'logout', 'users', get_user_id(), 'ออกจากระบบ');
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
redirect('login.php');
?>