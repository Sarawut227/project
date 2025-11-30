<?php
/**
 * =================================================
 * ไฟล์: admin/subjects/add.php
 * หน้าที่: เพิ่มรายวิชา (พร้อมบันทึก created_by)
 * =================================================
 */

require_once '../includes/auth.php';

$page_title = 'เพิ่มรายวิชา';

// Get active levels
$levels = $conn->query("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level_id = intval($_POST['level_id']);
    $subject_code = clean_input($_POST['subject_code']);
    $subject_name = clean_input($_POST['subject_name']);
    $description = clean_input($_POST['description']);
    $hours_theory = intval($_POST['hours_theory']);
    $hours_practice = intval($_POST['hours_practice']);
    $hours_self = intval($_POST['hours_self']);
    $sort_order = intval($_POST['sort_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        if (empty($subject_code) || empty($subject_name) || $level_id == 0) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // Check duplicate code
            $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_code = ?");
            $stmt->bind_param("s", $subject_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                set_alert('danger', 'รหัสวิชานี้มีอยู่แล้ว');
            } else {
                // Insert with created_by
                $stmt = $conn->prepare("INSERT INTO subjects (level_id, subject_code, subject_name, description, hours_theory, hours_practice, hours_self, sort_order, is_active, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $user_id = get_user_id();
                $stmt->bind_param("isssiiiiii", $level_id, $subject_code, $subject_name, $description, $hours_theory, $hours_practice, $hours_self, $sort_order, $is_active, $user_id);
                
                if ($stmt->execute()) {
                    $subject_id = $stmt->insert_id;
                    log_activity($conn, 'create', 'subjects', $subject_id, "เพิ่มรายวิชา: {$subject_name}");
                    set_alert('success', 'เพิ่มรายวิชาเรียบร้อยแล้ว');
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
                    <h1 class="page-title">เพิ่มรายวิชา</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการรายวิชา</a></li>
                        <li>เพิ่มรายวิชา</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">ระดับชั้น <span style="color: red;">*</span></label>
                            <select name="level_id" class="form-control" required>
                                <option value="">-- เลือกระดับชั้น --</option>
                                <?php while ($lv = $levels->fetch_assoc()): ?>
                                    <option value="<?php echo $lv['level_id']; ?>"><?php echo e($lv['level_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">รหัสวิชา <span style="color: red;">*</span></label>
                            <input type="text" name="subject_code" class="form-control" required placeholder="เช่น 30143-0001">
                            <small class="form-text">รหัสวิชาต้องไม่ซ้ำกัน</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อวิชา <span style="color: red;">*</span></label>
                            <input type="text" name="subject_name" class="form-control" required placeholder="ชื่อรายวิชา">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">คำอธิบายรายวิชา</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="คำอธิบายเกี่ยวกับรายวิชานี้"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="form-label">ชั่วโมงทฤษฎี (ท)</label>
                                    <input type="number" name="hours_theory" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="form-label">ชั่วโมงปฏิบัติ (ป)</label>
                                    <input type="number" name="hours_practice" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="form-label">ชั่วโมงศึกษาด้วยตนเอง (น)</label>
                                    <input type="number" name="hours_self" class="form-control" value="0" min="0">
                                </div>
                            </div>
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