<?php
/**
 * =================================================
 * ไฟล์: admin/includes/sidebar.php
 * หน้าที่: Sidebar with Role-based Menu
 * =================================================
 */

if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', '/admin');
}

$__URI_PATH__ = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '/';

function is_active(string $regex): string {
    global $__URI_PATH__;
    return preg_match($regex, $__URI_PATH__) ? 'active' : '';
}

$is_admin = is_admin();
?>
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo ADMIN_URL; ?>/index.php" class="sidebar-brand">
            <?php echo defined('SITE_NAME') ? SITE_NAME : 'Admin'; ?>
        </a>
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '(/index\.php)?$#'); ?>">
                <i class="fas fa-home"></i>
                <span>แดชบอร์ด</span>
            </a>
        </li>

        <?php if ($is_admin): ?>
        <!-- Levels (Admin Only) -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/levels/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '/levels(/|$)#'); ?>">
                <i class="fas fa-layer-group"></i>
                <span>จัดการระดับชั้น</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Subjects (All Users) ✅ เปลี่ยนจาก Admin Only -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/subjects/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '/subjects(/|$)#'); ?>">
                <i class="fas fa-book"></i>
                <span>จัดการรายวิชา</span>
            </a>
        </li>

        <!-- Lessons (All Users) -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/lessons/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '/lessons(/|$)#'); ?>">
                <i class="fas fa-file-alt"></i>
                <span>จัดการบทเรียน</span>
            </a>
        </li>

        <?php if ($is_admin): ?>
        <!-- Users (Admin Only) -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/users/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '/users(/|$)#'); ?>">
                <i class="fas fa-users-cog"></i>
                <span>ผู้ใช้งาน</span>
            </a>
        </li>

        <!-- Settings (Admin Only) -->
        <!-- <li>
            <a href="<?php echo ADMIN_URL; ?>/settings/index.php"
               class="<?php echo is_active('#^' . preg_quote(ADMIN_URL, '#') . '/settings(/|$)#'); ?>">
                <i class="fas fa-cog"></i>
                <span>ตั้งค่า</span>
            </a>
        </li> -->
        <?php endif; ?>

        <!-- View site -->
        <li>
            <a href="/" target="_blank" rel="noopener">
                <i class="fas fa-globe"></i>
                <span>ดูหน้าเว็บไซต์</span>
            </a>
        </li>

        <!-- Logout -->
        <li>
            <a href="<?php echo ADMIN_URL; ?>/logout.php" onclick="return confirm('ต้องการออกจากระบบ?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>ออกจากระบบ</span>
            </a>
        </li>
    </ul>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>