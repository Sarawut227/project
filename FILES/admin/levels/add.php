<?php
/**
 * Add Level
 */

require_once '../includes/auth.php';

$page_title = 'เพิ่มระดับชั้น';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level_code = clean_input($_POST['level_code']);
    $level_name = clean_input($_POST['level_name']);
    $description = clean_input($_POST['description']);
    $sort_order = intval($_POST['sort_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        // Validate
        if (empty($level_code) || empty($level_name)) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // Check duplicate code
            $stmt = $conn->prepare("SELECT level_id FROM levels WHERE level_code = ?");
            $stmt->bind_param("s", $level_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                set_alert('danger', 'รหัสระดับชั้นนี้มีอยู่แล้ว');
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO levels (level_code, level_name, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $level_code, $level_name, $description, $sort_order, $is_active);
                
                if ($stmt->execute()) {
                    $level_id = $stmt->insert_id;
                    log_activity($conn, 'create', 'levels', $level_id, "เพิ่มระดับชั้น: {$level_name}");
                    set_alert('success', 'เพิ่มระดับชั้นเรียบร้อยแล้ว');
                    redirect('index.php');
                } else {
                    set_alert('danger', 'เกิดข้อผิดพลาด: ' . $conn->error);
                }
            }
            $stmt->close();
        }
    }
}
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
                    <h1 class="page-title">เพิ่มระดับชั้น</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการระดับชั้น</a></li>
                        <li>เพิ่มระดับชั้น</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">รหัสระดับชั้น <span style="color: red;">*</span></label>
                            <input type="text" name="level_code" class="form-control" required placeholder="เช่น PVOS1">
                            <small class="form-text">รหัสภาษาอังกฤษไม่ซ้ำกัน</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อระดับชั้น <span style="color: red;">*</span></label>
                            <input type="text" name="level_name" class="form-control" required placeholder="เช่น ปวส.1">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="คำอธิบายเกี่ยวกับระดับชั้นนี้"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" class="form-control" value="1" min="0">
                            <small class="form-text">ลำดับน้อยจะแสดงก่อน</small>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span>เปิดใช้งาน</span>
                            </label>
                        </div>
                        
                        <div class="form-group df-end">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> ย้อนกลับ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> บันทึก
                            </button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>