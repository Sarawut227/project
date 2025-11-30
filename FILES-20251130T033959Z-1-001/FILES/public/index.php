<?php
/**
 * Frontend Homepage
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Get site settings
$site_name = get_setting($conn, 'site_name', SITE_NAME);
$site_description = get_setting($conn, 'site_description', 'ระบบจัดเก็บบทเรียน ปวส.');

// Get active levels with subject count
$levels = $conn->query("
    SELECT l.*,
           (SELECT COUNT(*) FROM subjects WHERE level_id = l.level_id AND is_active = 1) as subject_count
    FROM levels l
    WHERE l.is_active = 1
    ORDER BY l.sort_order ASC
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-graduation-cap"></i> <?php echo $site_name; ?>
            </a>
            <ul class="navbar-menu">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/login.php">เข้าสู่ระบบ</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?php echo $site_name; ?></h1>
            <p><?php echo $site_description; ?></p>
        </div>
    </section>
    
    <!-- Levels Section -->
    <section class="section" style="padding: 80px 0;">
        <div class="container">
            <div class="section-title">
                <h2>เลือกระดับชั้นเรียน</h2>
                <p>เริ่มต้นเรียนรู้ด้วยการเลือกระดับชั้นที่คุณสนใจ</p>
            </div>
            
            <?php if ($levels->num_rows > 0): ?>
                <div class="grid grid-3">
                    <?php while ($level = $levels->fetch_assoc()): ?>
                        <a href="subjects.php?level=<?php echo $level['level_id']; ?>" style="text-decoration: none;">
                            <div class="card">
                                <div style="text-align: center; margin-bottom: 20px;">
                                    <i class="fas fa-layer-group" style="font-size: 48px; color: var(--primary-color);"></i>
                                </div>
                                <h3 style="text-align: center; color: var(--primary-color);">
                                    <?php echo e($level['level_name']); ?>
                                </h3>
                                <p style="text-align: center; color: var(--text-secondary);">
                                    <?php echo e($level['description']); ?>
                                </p>
                                <div style="text-align: center; margin-top: 20px;">
                                    <span class="badge badge-info">
                                        <?php echo $level['subject_count']; ?> รายวิชา
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-inbox" style="font-size: 64px; color: var(--text-light); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--text-secondary);">ยังไม่มีข้อมูล</h3>
                        <p style="color: var(--text-secondary);">ระบบยังไม่มีระดับชั้นเรียนในขณะนี้</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>