<?php
/**
 * =================================================
 * ไฟล์: admin/users/delete.php
 * หน้าที่: ลบผู้ใช้งาน
 * =================================================
 */

require_once '../includes/auth.php';

// Admin only
check_permission('admin');

$user_id = intval($_GET['id'] ?? 0);

// ป้องกันไม่ให้ลบตัวเอง
if ($user_id == get_user_id()) {
    set_alert('danger', 'ไม่สามารถลบบัญชีของตัวเองได้');
    redirect('index.php');
}

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

// ตรวจสอบว่ามีบทเรียนที่สร้างโดยผู้ใช้นี้หรือไม่
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM lessons WHERE created_by = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$check_result = $stmt->get_result();
$check = $check_result->fetch_assoc();

if ($check['count'] > 0) {
    set_alert('danger', 'ไม่สามารถลบได้ เนื่องจากผู้ใช้นี้มีบทเรียนที่สร้างไว้ในระบบ (' . $check['count'] . ' บทเรียน)');
    redirect('index.php');
}

// ลบผู้ใช้
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    log_activity($conn, 'delete', 'users', $user_id, "ลบผู้ใช้งาน: {$user['username']}");
    set_alert('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
} else {
    set_alert('danger', 'เกิดข้อผิดพลาดในการลบ');
}

$stmt->close();
redirect('index.php');
?>