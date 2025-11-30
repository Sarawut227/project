<?php
/**
 * Authentication Check
 * Include this file in every admin page
 */

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect(ADMIN_URL . '/login.php');
}

// Check session timeout (optional - 30 minutes)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    redirect(ADMIN_URL . '/login.php?timeout=1');
}

// Update last activity time
$_SESSION['last_activity'] = time();

?>