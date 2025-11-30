<?php
/**
 * Manage Levels
 */

require_once '../includes/auth.php';

$page_title = 'จัดการระดับชั้น';

// Get all levels
$levels = $conn->query("
    SELECT l.*, 
           (SELECT COUNT(*) FROM subjects WHERE level_id = l.level_id) as subject_count
    FROM levels l
    ORDER BY l.sort_order ASC
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
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/topbar.php'; ?>
            
            <div class="admin-content">
                <?php show_alert(); ?>
                
                <div class="page-header">
                    <h1 class="page-title">จัดการระดับชั้น</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li>จัดการระดับชั้น</li>
                    </ul>
                </div>
                
                <div class="card">
                    <div class="table-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="tableSearch" class="form-control" placeholder="ค้นหา...">
                        </div>
                        <div class="table-action-buttons">
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มระดับชั้น
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($levels->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัส</th>
                                        <th>ชื่อระดับชั้น</th>
                                        <th>คำอธิบาย</th>
                                        <th>จำนวนวิชา</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $levels->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['sort_order']; ?></td>
                                            <td><strong><?php echo e($row['level_code']); ?></strong></td>
                                            <td><?php echo e($row['level_name']); ?></td>
                                            <td><?php echo e(truncate_text($row['description'] ?? '', 50)); ?></td>
                                            <td>
                                                <span class="badge badge-info"><?php echo $row['subject_count']; ?> วิชา</span>
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
                                                    <a href="edit.php?id=<?php echo $row['level_id']; ?>" class="btn-icon btn-edit" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?php echo $row['level_id']; ?>" class="btn-icon btn-delete" title="ลบ" data-confirm-delete>
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
                            <i class="fas fa-layer-group"></i>
                            <h3>ยังไม่มีระดับชั้น</h3>
                            <p>เริ่มต้นเพิ่มระดับชั้นเรียนแรกของคุณ</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มระดับชั้น
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