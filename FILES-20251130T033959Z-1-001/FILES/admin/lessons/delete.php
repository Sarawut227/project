<?php
/**
 * Delete Lesson
 */

require_once '../includes/auth.php';

$lesson_id = intval($_GET['id'] ?? 0);

// Get lesson data
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lesson_id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'ไม่พบข้อมูลบทเรียน');
    redirect('index.php');
}

$lesson = $result->fetch_assoc();

if (!can_edit_resource($lesson['created_by'])) {
    set_alert('danger', 'คุณไม่มีสิทธิ์ลบบทเรียนนี้');
    redirect('index.php');
}


// Get and delete all attachments
$attachments = $conn->query("SELECT * FROM attachments WHERE lesson_id = {$lesson_id}");
while ($file = $attachments->fetch_assoc()) {
    delete_file($file['file_name']);
}

// Delete attachments records
$conn->query("DELETE FROM attachments WHERE lesson_id = {$lesson_id}");

// Delete lesson
$stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
$stmt->bind_param("i", $lesson_id);

if ($stmt->execute()) {
    log_activity($conn, 'delete', 'lessons', $lesson_id, "ลบบทเรียน: {$lesson['lesson_title']}");
    set_alert('success', 'ลบบทเรียนเรียบร้อยแล้ว');
} else {
    set_alert('danger', 'เกิดข้อผิดพลาดในการลบ');
}

$stmt->close();
redirect('index.php');
?>