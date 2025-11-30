<?php
/**
 * Add User
 */

require_once '../includes/auth.php';

// Admin only
check_permission('admin');

$page_title = 'เพิ่มผู้ใช้งาน';

// Get positions
$positions = get_positions($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $full_name = clean_input($_POST['full_name']);
    $position_id = intval($_POST['position_id']) ?: null;
    $email = clean_input($_POST['email']);
    $role = clean_input($_POST['role']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // Check duplicate username
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                set_alert('danger', 'ชื่อผู้ใช้นี้มีอยู่แล้ว');
            } else {
                // Check duplicate email
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    set_alert('danger', 'อีเมลนี้มีอยู่แล้ว');
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user
                    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, position_id, email, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssi", $username, $hashed_password, $full_name, $position_id, $email, $role, $is_active);
                    
                    if ($stmt->execute()) {
                        $user_id = $stmt->insert_id;
                        log_activity($conn, 'create', 'users', $user_id, "เพิ่มผู้ใช้งาน: {$username}");
                        set_alert('success', 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว');
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
                    <h1 class="page-title">เพิ่มผู้ใช้งาน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการผู้ใช้งาน</a></li>
                        <li>เพิ่มผู้ใช้งาน</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อผู้ใช้ <span style="color: red;">*</span></label>
                            <input type="text" name="username" class="form-control" required placeholder="ชื่อผู้ใช้สำหรับเข้าสู่ระบบ">
                            <small class="form-text">ภาษาอังกฤษตัวเล็ก ไม่มีช่องว่าง</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">รหัสผ่าน <span style="color: red;">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="รหัสผ่านอย่างน้อย 6 ตัวอักษร">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อ-สกุล <span style="color: red;">*</span></label>
                            <input type="text" name="full_name" class="form-control" required placeholder="ชื่อและนามสกุลเต็ม">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ตำแหน่ง</label>
                            <select name="position_id" class="form-control">
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?php echo $pos['position_id']; ?>">
                                        <?php echo e($pos['position_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">อีเมล <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">บทบาท <span style="color: red;">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="teacher">ครู</option>
                                <option value="admin">แอดมิน</option>
                            </select>
                            <small class="form-text">แอดมินจะสามารถเข้าถึงทุกอย่างในระบบ</small>
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