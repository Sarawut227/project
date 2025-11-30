<?php
/**
 * =================================================
 * ไฟล์: admin/subjects/delete.php
 * หน้าที่: ลบรายวิชา (มีการเช็คสิทธิ์)
 * =================================================
 */

require_once '../includes/auth.php';

$subject_id = intval($_GET['id'] ?? 0);

// Get subject data
$stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'ไม่พบข้อมูลรายวิชา');
    redirect('index.php');
}

$subject = $result->fetch_assoc();

// ✅ ตรวจสอบสิทธิ์ในการลบ
if (!can_edit_resource($subject['created_by'])) {
    set_alert('danger', 'คุณไม่มีสิทธิ์ลบรายวิชานี้');
    redirect('index.php');
}

// Check if subject has lessons
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM lessons WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$check_result = $stmt->get_result();
$check = $check_result->fetch_assoc();

if ($check['count'] > 0) {
    set_alert('danger', 'ไม่สามารถลบได้ เนื่องจากมีบทเรียนในรายวิชานี้');
    redirect('index.php');
}

// Delete subject
$stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);

if ($stmt->execute()) {
    log_activity($conn, 'delete', 'subjects', $subject_id, "ลบรายวิชา: {$subject['subject_name']}");
    set_alert('success', 'ลบรายวิชาเรียบร้อยแล้ว');
} else {
    set_alert('danger', 'เกิดข้อผิดพลาดในการลบ');
}

$stmt->close();
redirect('index.php');
?>