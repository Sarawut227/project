<?php
/**
 * Admin Login Page
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Validate inputs
        if (empty($username) || empty($password)) {
            $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        } else {
            // Check user credentials
            $stmt = $conn->prepare("SELECT user_id, username, password, full_name, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Verify password
                if (password_verify($password, $row['password'])) {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['full_name'] = $row['full_name'];
                    $_SESSION['role'] = $row['role'];
                    
                    // Log activity
                    log_activity($conn, 'login', 'users', $row['user_id'], 'เข้าสู่ระบบ');
                    
                    // Redirect to dashboard
                    redirect('index.php');
                } else {
                    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
                }
            } else {
                $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
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
    <title>เข้าสู่ระบบ - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-logo">
                <h1><?php echo SITE_NAME; ?></h1>
                <p>ระบบจัดการบทเรียน</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div class="form-group">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                
                <div class="form-group">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
            </form>
            
            <div class="mt-3 text-center">
                <small class="text-secondary">
                    ชื่อผู้ใช้เริ่มต้น: <strong>admin</strong><br>
                    รหัสผ่านเริ่มต้น: <strong>password</strong>
                </small>
            </div>
        </div>
    </div>
</body>
</html>