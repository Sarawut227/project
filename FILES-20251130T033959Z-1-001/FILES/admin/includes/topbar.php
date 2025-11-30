<?php
/**
 * Admin Topbar
 */
?>
<div class="admin-topbar">
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <h2 class="topbar-title"><?php echo $page_title ?? 'Dashboard'; ?></h2>
    
    <div class="topbar-user">
        <div class="user-info">
            <span class="user-name"><?php echo e(get_user_fullname()); ?></span>
            <span class="user-role"><?php echo e($_SESSION['role'] ?? 'User'); ?></span>
        </div>
        <div class="user-avatar">
            A
        </div>
    </div>
</div>