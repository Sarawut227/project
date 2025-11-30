<?php
/**
 * =================================================
 * ไฟล์: admin/lessons/index.php
 * หน้าที่: จัดการบทเรียน (มีระบบ Filter ตาม User)
 * =================================================
 */

require_once '../includes/auth.php';

$page_title = 'จัดการบทเรียน';

// Filters
$level_filter = intval($_GET['level'] ?? 0);
$subject_filter = intval($_GET['subject'] ?? 0);

// Build query with user filter
$where = "WHERE " . get_user_filter('l');

if ($level_filter > 0) {
    $where .= " AND lv.level_id = {$level_filter}";
}
if ($subject_filter > 0) {
    $where .= " AND l.subject_id = {$subject_filter}";
}

// Get lessons
$lessons = $conn->query("
    SELECT l.*, s.subject_name, s.subject_code, lv.level_name,
           (SELECT COUNT(*) FROM attachments WHERE lesson_id = l.lesson_id) as file_count,
           u.full_name as creator_name
    FROM lessons l
    JOIN subjects s ON l.subject_id = s.subject_id
    JOIN levels lv ON s.level_id = lv.level_id
    LEFT JOIN users u ON l.created_by = u.user_id
    {$where}
    ORDER BY lv.level_id, s.sort_order, l.sort_order
");

// Get levels for filter
$levels = $conn->query("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");

// Get subjects for filter (based on level)
$subjects_query = "SELECT * FROM subjects WHERE is_active = 1";
if ($level_filter > 0) {
    $subjects_query .= " AND level_id = {$level_filter}";
}
$subjects_query .= " ORDER BY sort_order";
$subjects = $conn->query($subjects_query);
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
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/topbar.php'; ?>
            
            <div class="admin-content">
                <?php show_alert(); ?>
                
                <div class="page-header">
                    <h1 class="page-title">จัดการบทเรียน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li>จัดการบทเรียน</li>
                    </ul>
                </div>
                
                <div class="card">
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="tableSearch" class="form-control" placeholder="ค้นหา...">
                        </div>
                        <div class="table-action-buttons" style="display: flex; gap: 12px;">
                            <select class="form-control" onchange="filterByLevel(this.value)" style="width: auto;">
                                <option value="0">ทุกระดับชั้น</option>
                                <?php 
                                $levels->data_seek(0);
                                while ($lv = $levels->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $lv['level_id']; ?>" <?php echo ($level_filter == $lv['level_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($lv['level_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            
                            <select class="form-control" onchange="filterBySubject(this.value)" style="width: auto;">
                                <option value="0">ทุกวิชา</option>
                                <?php while ($subj = $subjects->fetch_assoc()): ?>
                                    <option value="<?php echo $subj['subject_id']; ?>" <?php echo ($subject_filter == $subj['subject_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($subj['subject_code']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> สร้างบทเรียน
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($lessons->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>ชื่อบทเรียน</th>
                                        <th>วิชา</th>
                                        <th>ระดับชั้น</th>
                                        <?php if (is_admin()): ?>
                                        <th>ผู้สร้าง</th>
                                        <?php endif; ?>
                                        <th>ไฟล์แนบ</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $lessons->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['sort_order']; ?></td>
                                            <td><strong><?php echo e($row['lesson_title']); ?></strong></td>
                                            <td><?php echo e($row['subject_code']); ?></td>
                                            <td><span class="badge badge-info"><?php echo e($row['level_name']); ?></span></td>
                                            <?php if (is_admin()): ?>
                                            <td><?php echo e($row['creator_name'] ?? 'ไม่ระบุ'); ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <?php if ($row['file_count'] > 0): ?>
                                                    <span class="badge badge-warning"><?php echo $row['file_count']; ?> ไฟล์</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">ไม่มีไฟล์</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 'published'): ?>
                                                    <span class="badge badge-success">เผยแพร่</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">ร่าง</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?php echo SITE_URL; ?>/public/lesson.php?id=<?php echo $row['lesson_id']; ?>" target="_blank" class="btn-icon btn-view" title="ดูบทเรียน">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if (can_edit_resource($row['created_by'])): ?>
                                                    <a href="edit.php?id=<?php echo $row['lesson_id']; ?>" class="btn-icon btn-edit" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?php echo $row['lesson_id']; ?>" class="btn-icon btn-delete" title="ลบ" data-confirm-delete>
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
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
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> สร้างบทเรียน
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    <script>
        function filterByLevel(levelId) {
            window.location.href = '?level=' + levelId + '&subject=0';
        }
        
        function filterBySubject(subjectId) {
            const levelId = <?php echo $level_filter; ?>;
            window.location.href = '?level=' + levelId + '&subject=' + subjectId;
        }
    </script>
</body>
</html>