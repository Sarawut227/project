<?php
/**
 * Frontend Subjects Page
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$level_id = intval($_GET['level'] ?? 0);

// Get level info
$stmt = $conn->prepare("SELECT * FROM levels WHERE level_id = ? AND is_active = 1");
$stmt->bind_param("i", $level_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$level = $result->fetch_assoc();

// Get subjects with lesson count
$subjects = $conn->query("
    SELECT s.*,
           (SELECT COUNT(*) FROM lessons WHERE subject_id = s.subject_id AND status = 'published') as lesson_count
    FROM subjects s
    WHERE s.level_id = {$level_id} AND s.is_active = 1
    ORDER BY s.sort_order ASC
");

$site_name = get_setting($conn, 'site_name', SITE_NAME);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($level['level_name']); ?> - <?php echo $site_name; ?></title>
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
    <div class="container" style="margin-top: 20px;">
        <nav style="color: var(--text-secondary); font-size: 14px;">
            <a href="index.php" style="color: var(--text-secondary);">หน้าแรก</a>
            <span> / </span>
            <span><?php echo e($level['level_name']); ?></span>
        </nav>
    </div>
    
    <!-- Page Header -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1><?php echo e($level['level_name']); ?></h1>
                <p><?php echo e($level['description']); ?></p>
            </div>
            
            <?php if ($subjects->num_rows > 0): ?>
                <div class="grid grid-3">
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <div class="card">
                            <div style="margin-bottom: 16px;">
                                <span class="badge badge-primary"><?php echo e($subject['subject_code']); ?></span>
                            </div>
                            <h3 style="margin-bottom: 12px;">
                                <?php echo e($subject['subject_name']); ?>
                            </h3>
                            <p style="color: var(--text-secondary); margin-bottom: 16px;">
                                <?php echo e(truncate_text($subject['description'] ?? 'ไม่มีคำอธิบาย', 100)); ?>
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--border-color);">
                                <div style="font-size: 14px; color: var(--text-secondary);">
                                    <i class="fas fa-clock"></i>
                                    <?php echo $subject['hours_theory'] . '-' . $subject['hours_practice'] . '-' . $subject['hours_self']; ?> ชม.
                                </div>
                                <div>
                                    <?php if ($subject['lesson_count'] > 0): ?>
                                        <a href="lessons.php?subject=<?php echo $subject['subject_id']; ?>" class="btn btn-primary btn-sm">
                                            ดูบทเรียน (<?php echo $subject['lesson_count']; ?>)
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">ยังไม่มีบทเรียน</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-book" style="font-size: 64px; color: var(--text-light); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--text-secondary);">ยังไม่มีรายวิชา</h3>
                        <p style="color: var(--text-secondary);">ระดับชั้นนี้ยังไม่มีรายวิชาในขณะนี้</p>
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