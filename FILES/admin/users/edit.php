<?php
/**
 * =================================================
 * ไฟล์: admin/users/edit.php
 * หน้าที่: แก้ไขข้อมูลผู้ใช้งาน
 * =================================================
 */

require_once '../includes/auth.php';

// Admin only
check_permission('admin');

$page_title = 'แก้ไขผู้ใช้งาน';

$user_id = intval($_GET['id'] ?? 0);

// ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'ไม่พบข้อมูลผู้ใช้งาน');
    redirect('index.php');
}

$user = $result->fetch_assoc();

// ป้องกันไม่ให้แก้ไขตัวเอง
$is_self = ($user_id == get_user_id());

// Get positions
$positions = get_positions($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $full_name = clean_input($_POST['full_name']);
    $position_id = intval($_POST['position_id']) ?: null;
    $email = clean_input($_POST['email']);
    $role = clean_input($_POST['role']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $new_password = $_POST['new_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        if (empty($username) || empty($full_name) || empty($email)) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // ตรวจสอบ username ซ้ำ (ยกเว้นตัวเอง)
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
            $stmt->bind_param("si", $username, $user_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                set_alert('danger', 'ชื่อผู้ใช้นี้มีอยู่แล้ว');
            } else {
                // ตรวจสอบ email ซ้ำ (ยกเว้นตัวเอง)
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                $check_result = $stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    set_alert('danger', 'อีเมลนี้มีอยู่แล้ว');
                } else {
                    // Update user
                    if (!empty($new_password)) {
                        // เปลี่ยนรหัสผ่านด้วย
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, position_id = ?, email = ?, role = ?, is_active = ? WHERE user_id = ?");
                        $stmt->bind_param("sssssiii", $username, $hashed_password, $full_name, $position_id, $email, $role, $is_active, $user_id);
                    } else {
                        // ไม่เปลี่ยนรหัสผ่าน
                        $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, position_id = ?, email = ?, role = ?, is_active = ? WHERE user_id = ?");
                        $stmt->bind_param("sssssii", $username, $full_name, $position_id, $email, $role, $is_active, $user_id);
                    }
                    
                    if ($stmt->execute()) {
                        log_activity($conn, 'update', 'users', $user_id, "แก้ไขผู้ใช้งาน: {$username}");
                        set_alert('success', 'แก้ไขผู้ใช้งานเรียบร้อยแล้ว');
                        redirect('index.php');
                    } else {
                        set_alert('danger', 'เกิดข้อผิดพลาด: ' . $conn->error);
                    }
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
                    <h1 class="page-title">แก้ไขผู้ใช้งาน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการผู้ใช้งาน</a></li>
                        <li>แก้ไขผู้ใช้งาน</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อผู้ใช้ <span style="color: red;">*</span></label>
                            <input type="text" name="username" class="form-control" required value="<?php echo e($user['username']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" name="new_password" class="form-control" minlength="6" placeholder="เว้นว่างหากไม่ต้องการเปลี่ยน">
                            <small class="form-text">กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อ-สกุล <span style="color: red;">*</span></label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo e($user['full_name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ตำแหน่ง</label>
                            <select name="position_id" class="form-control">
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?php echo $pos['position_id']; ?>" 
                                        <?php echo ($user['position_id'] == $pos['position_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($pos['position_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">อีเมล <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?php echo e($user['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">บทบาท <span style="color: red;">*</span></label>
                            <select name="role" class="form-control" required <?php echo $is_self ? 'disabled' : ''; ?>>
                                <option value="teacher" <?php echo ($user['role'] == 'teacher') ? 'selected' : ''; ?>>ครู</option>
                                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>แอดมิน</option>
                            </select>
                            <?php if ($is_self): ?>
                                <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                <small class="form-text">ไม่สามารถเปลี่ยนบทบาทของตัวเองได้</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" 
                                    <?php echo $user['is_active'] ? 'checked' : ''; ?>
                                    <?php echo $is_self ? 'disabled' : ''; ?>>
                                <span>เปิดใช้งาน</span>
                            </label>
                            <?php if ($is_self): ?>
                                <input type="hidden" name="is_active" value="1">
                                <small class="form-text">ไม่สามารถปิดใช้งานตัวเองได้</small>
                            <?php endif; ?>
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