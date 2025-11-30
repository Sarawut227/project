<?php
/**
 * Manage Subjects
 */

require_once '../includes/auth.php';

$page_title = 'จัดการรายวิชา';

// Filter by level
$level_filter = intval($_GET['level'] ?? 0);

// Build query
$where = "WHERE 1=1";
if ($level_filter > 0) {
    $where .= " AND s.level_id = {$level_filter}";
}

// Get subjects with lesson count
$subjects = $conn->query("
    SELECT s.*, l.level_name,
           (SELECT COUNT(*) FROM lessons WHERE subject_id = s.subject_id) as lesson_count
    FROM subjects s
    JOIN levels l ON s.level_id = l.level_id
    {$where}
    ORDER BY s.level_id ASC, s.sort_order ASC
");

// Get levels for filter
$levels = $conn->query("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");
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
                    <h1 class="page-title">จัดการรายวิชา</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li>จัดการรายวิชา</li>
                    </ul>
                </div>
                
                <div class="card">
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="tableSearch" class="form-control" placeholder="ค้นหา...">
                        </div>
                        <div class="table-action-buttons">
                            <select class="form-control" onchange="window.location.href='?level='+this.value" style="width: auto;">
                                <option value="0">ทุกระดับชั้น</option>
                                <?php while ($lv = $levels->fetch_assoc()): ?>
                                    <option value="<?php echo $lv['level_id']; ?>" <?php echo ($level_filter == $lv['level_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($lv['level_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มรายวิชา
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($subjects->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>รหัสวิชา</th>
                                        <th>ชื่อวิชา</th>
                                        <th>ระดับชั้น</th>
                                        <th>ชั่วโมง (ท-ป-น)</th>
                                        <th>จำนวนบท</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $subjects->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?php echo e($row['subject_code']); ?></strong></td>
                                            <td><?php echo e($row['subject_name']); ?></td>
                                            <td><span class="badge badge-info"><?php echo e($row['level_name']); ?></span></td>
                                            <td><?php echo $row['hours_theory'] . '-' . $row['hours_practice'] . '-' . $row['hours_self']; ?></td>
                                            <td>
                                                <span class="badge badge-warning"><?php echo $row['lesson_count']; ?> บท</span>
                                            </td>
                                            <td>
                                                <?php if ($row['is_active']): ?>
                                                    <span class="badge badge-success">เปิดใช้งาน</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">ปิดใช้งาน</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="../lessons/index.php?subject=<?php echo $row['subject_id']; ?>" class="btn-icon btn-view" title="ดูบทเรียน">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit.php?id=<?php echo $row['subject_id']; ?>" class="btn-icon btn-edit" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?php echo $row['subject_id']; ?>" class="btn-icon btn-delete" title="ลบ" data-confirm-delete>
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-book"></i>
                            <h3>ยังไม่มีรายวิชา</h3>
                            <p>เริ่มต้นเพิ่มรายวิชาแรกของคุณ</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มรายวิชา
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>