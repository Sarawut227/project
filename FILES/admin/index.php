<?php
/**
 * =================================================
 * ไฟล์: admin/index.php
 * หน้าที่: Admin Dashboard (แสดงสถิติตาม Role)
 * =================================================
 */

require_once 'includes/auth.php';

$page_title = 'แดชบอร์ด';

// กำหนด filter ตาม role
$user_filter = get_user_filter('l');

// Get statistics
if (is_admin()) {
    // Admin เห็นสถิติทั้งหมด
    $stmt = $conn->query("SELECT COUNT(*) as count FROM levels WHERE is_active = 1");
    $total_levels = $stmt->fetch_assoc()['count'];

    $stmt = $conn->query("SELECT COUNT(*) as count FROM subjects WHERE is_active = 1");
    $total_subjects = $stmt->fetch_assoc()['count'];

    $stmt = $conn->query("SELECT COUNT(*) as count FROM lessons WHERE status = 'published'");
    $total_lessons = $stmt->fetch_assoc()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $total_users = $stmt->fetch_assoc()['count'];
} else {
    // Teacher เห็นเฉพาะของตัวเอง
    $user_id = get_user_id();
    
    $total_levels = 0; // Teacher ไม่แสดง
    
    // แสดงจำนวนรายวิชาของตัวเอง
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM subjects WHERE created_by = ? AND is_active = 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_subjects = $result->fetch_assoc()['count'];
    
    // แสดงจำนวนบทเรียนของตัวเอง
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM lessons WHERE created_by = ? AND status = 'published'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_lessons = $result->fetch_assoc()['count'];
    
    // แสดงจำนวนไฟล์แนบของตัวเอง
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM attachments a
        JOIN lessons l ON a.lesson_id = l.lesson_id
        WHERE l.created_by = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_files = $result->fetch_assoc()['count'];
}

// Get recent lessons (filtered by user)
$recent_lessons = $conn->query("
    SELECT l.*, s.subject_name, lv.level_name, u.full_name as creator_name
    FROM lessons l
    JOIN subjects s ON l.subject_id = s.subject_id
    JOIN levels lv ON s.level_id = lv.level_id
    LEFT JOIN users u ON l.created_by = u.user_id
    WHERE {$user_filter}
    ORDER BY l.created_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="admin-content">
                <?php show_alert(); ?>
                
                <div class="page-header">
                    <h1 class="page-title">แดชบอร์ด</h1>
                    <p>ภาพรวมระบบบทเรียนออนไลน์</p>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <?php if (is_admin()): ?>
                    <!-- Admin Dashboard -->
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_levels; ?></div>
                        <div class="stat-label">ระดับชั้นเรียน</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_subjects; ?></div>
                        <div class="stat-label">รายวิชาทั้งหมด</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_lessons; ?></div>
                        <div class="stat-label">บทเรียนทั้งหมด</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">ผู้ใช้งาน</div>
                    </div>
                    <?php else: ?>
                    <!-- Teacher Dashboard -->
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_subjects; ?></div>
                        <div class="stat-label">รายวิชาของฉัน</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_lessons; ?></div>
                        <div class="stat-label">บทเรียนของฉัน</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-paperclip"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_files; ?></div>
                        <div class="stat-label">ไฟล์แนบทั้งหมด</div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Lessons -->
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo is_admin() ? 'บทเรียนล่าสุด' : 'บทเรียนของฉัน'; ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_lessons->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ชื่อบทเรียน</th>
                                            <th>วิชา</th>
                                            <th>ระดับชั้น</th>
                                            <?php if (is_admin()): ?>
                                            <th>ผู้สร้าง</th>
                                            <?php endif; ?>
                                            <th>สถานะ</th>
                                            <th>วันที่สร้าง</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $recent_lessons->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo e($row['lesson_title']); ?></td>
                                                <td><?php echo e($row['subject_name']); ?></td>
                                                <td><?php echo e($row['level_name']); ?></td>
                                                <?php if (is_admin()): ?>
                                                <td><?php echo e($row['creator_name'] ?? 'ไม่ระบุ'); ?></td>
                                                <?php endif; ?>
                                                <td>
                                                    <?php if ($row['status'] == 'published'): ?>
                                                        <span class="badge badge-success">เผยแพร่</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">ร่าง</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo thai_date($row['created_at']); ?></td>
                                                <td>
                                                    <a href="lessons/edit.php?id=<?php echo $row['lesson_id']; ?>" class="btn-icon btn-edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <h3>ยังไม่มีบทเรียน</h3>
                                <p>เริ่มต้นสร้างบทเรียนแรกของคุณ</p>
                                <a href="lessons/add.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> สร้างบทเรียน
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3>เมนูด่วน</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-3">
                            <?php if (is_admin()): ?>
                            <a href="levels/add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มระดับชั้น
                            </a>
                            <?php endif; ?>
                            <a href="subjects/add.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> เพิ่มรายวิชา
                            </a>
                            <a href="lessons/add.php" class="btn btn-warning">
                                <i class="fas fa-plus"></i> สร้างบทเรียน
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>