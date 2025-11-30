<?php
/**
 * Delete Level
 */

require_once '../includes/auth.php';

$level_id = intval($_GET['id'] ?? 0);

// Get level data
$stmt = $conn->prepare("SELECT * FROM levels WHERE level_id = ?");
$stmt->bind_param("i", $level_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'ไม่พบข้อมูลระดับชั้น');
    redirect('index.php');
}

$level = $result->fetch_assoc();

// Check if level has subjects
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM subjects WHERE level_id = ?");
$stmt->bind_param("i", $level_id);
$stmt->execute();
$check_result = $stmt->get_result();
$check = $check_result->fetch_assoc();

if ($check['count'] > 0) {
    set_alert('danger', 'ไม่สามารถลบได้ เนื่องจากมีรายวิชาอยู่ในระดับชั้นนี้');
    redirect('index.php');
}

// Delete level
$stmt = $conn->prepare("DELETE FROM levels WHERE level_id = ?");
$stmt->bind_param("i", $level_id);

if ($stmt->execute()) {
    log_activity($conn, 'delete', 'levels', $level_id, "ลบระดับชั้น: {$level['level_name']}");
    set_alert('success', 'ลบระดับชั้นเรียบร้อยแล้ว');
} else {
    set_alert('danger', 'เกิดข้อผิดพลาดในการลบ');
}

$stmt->close();
redirect('index.php');
?>