<?php
/**
 * Frontend Lessons List
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$subject_id = intval($_GET['subject'] ?? 0);

// Get subject info with level
$stmt = $conn->prepare("
    SELECT s.*, l.level_name
    FROM subjects s
    JOIN levels l ON s.level_id = l.level_id
    WHERE s.subject_id = ? AND s.is_active = 1
");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$subject = $result->fetch_assoc();

// Get lessons
$lessons = $conn->query("
    SELECT l.*
    FROM lessons l
    WHERE l.subject_id = {$subject_id} AND l.status = 'published'
    ORDER BY l.sort_order ASC
");

$site_name = get_setting($conn, 'site_name', SITE_NAME);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subject['subject_name']); ?> - <?php echo $site_name; ?></title>
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
    
    <!-- Breadcrumb -->
    <div class="container" style="margin-top: 40px;">
        <nav style="color: var(--text-secondary); font-size: 14px;">
            <a href="index.php" style="color: var(--text-secondary);">หน้าแรก</a>
            <span> / </span>
            <a href="subjects.php?level=<?php echo $subject['level_id']; ?>" style="color: var(--text-secondary);">
                <?php echo e($subject['level_name']); ?>
            </a>
            <span> / </span>
            <span><?php echo e($subject['subject_name']); ?></span>
        </nav>
    </div>
    
    <!-- Page Header -->
    <section class="section">
        <div class="container">
            <div class="card" style="margin-bottom: 24px;">
                <div style="margin-bottom: 8px;">
                    <span class="badge badge-primary"><?php echo e($subject['subject_code']); ?></span>
                </div>
                <h1 style="margin-bottom: 12px;"><?php echo e($subject['subject_name']); ?></h1>
                <p style="color: var(--text-secondary);"><?php echo e($subject['description'] ?? 'ไม่มีคำอธิบาย'); ?></p>
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary);">
                    <i class="fas fa-clock"></i> ชั่วโมงเรียน: <?php echo $subject['hours_theory'] . '-' . $subject['hours_practice'] . '-' . $subject['hours_self']; ?>
                </div>
            </div>
            
            <h2 style="margin-bottom: 24px;    margin-top: 40px;">รายการบทเรียน</h2>
            
            <?php if ($lessons->num_rows > 0): ?>
                <div class="grid grid-1">
                    <?php while ($lesson = $lessons->fetch_assoc()): ?>
                        <a href="lesson.php?id=<?php echo $lesson['lesson_id']; ?>" style="text-decoration: none;">
                            <div class="card" style="margin-bottom: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                                            <span class="badge badge-info">บทที่ <?php echo $lesson['sort_order']; ?></span>
                                        </div>
                                        <h3 style="color: var(--primary-color); margin-bottom: 4px;">
                                            <?php echo e($lesson['lesson_title']); ?>
                                        </h3>
                                        <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
                                            อัปเดต: <?php echo thai_date($lesson['updated_at'], 'd/m/Y'); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <i class="fas fa-chevron-right" style="color: var(--primary-color); font-size: 20px;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-file-alt" style="font-size: 64px; color: var(--text-light); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--text-secondary);">ยังไม่มีบทเรียน</h3>
                        <p style="color: var(--text-secondary);">วิชานี้ยังไม่มีบทเรียนในขณะนี้</p>
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