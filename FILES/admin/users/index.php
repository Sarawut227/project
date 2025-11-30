<?php
/**
 * Manage Users
 */

require_once '../includes/auth.php';

// Admin only
check_permission('admin');

$page_title = 'จัดการผู้ใช้งาน';

// Get all users with position
$users = $conn->query("
    SELECT u.*, p.position_name
    FROM users u
    LEFT JOIN positions p ON u.position_id = p.position_id
    ORDER BY u.created_at DESC
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
                    <h1 class="page-title">จัดการผู้ใช้งาน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li>จัดการผู้ใช้งาน</li>
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
                                <i class="fas fa-plus"></i> เพิ่มผู้ใช้งาน
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($users->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ชื่อผู้ใช้</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th>ตำแหน่ง</th>
                                        <th>อีเมล</th>
                                        <th>บทบาท</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?php echo e($row['username']); ?></strong></td>
                                            <td><?php echo e($row['full_name']); ?></td>
                                            <td>
                                                <?php if ($row['position_name']): ?>
                                                    <span class="badge badge-info"><?php echo e($row['position_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($row['email'] ?? '-'); ?></td>
                                            <td>
                                                <?php if ($row['role'] == 'admin'): ?>
                                                    <span class="badge badge-danger">แอดมิน</span>
                                                <?php else: ?>
                                                    <span class="badge badge-primary">ครู</span>
                                                <?php endif; ?>
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
                                                    <a href="edit.php?id=<?php echo $row['user_id']; ?>" class="btn-icon btn-edit" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($row['user_id'] != get_user_id()): ?>
                                                    <a href="delete.php?id=<?php echo $row['user_id']; ?>" class="btn-icon btn-delete" title="ลบ" data-confirm-delete>
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
                            <i class="fas fa-users"></i>
                            <h3>ยังไม่มีผู้ใช้งาน</h3>
                            <p>เริ่มต้นเพิ่มผู้ใช้งานแรกของคุณ</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มผู้ใช้งาน
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