<?php
/**
 * API: Delete attachment
 */

session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

// Check if logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$attachment_id = intval($_POST['attachment_id'] ?? 0);
$lesson_id = intval($_POST['lesson_id'] ?? 0);

if ($attachment_id === 0 || $lesson_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Get attachment info
$stmt = $conn->prepare("SELECT * FROM attachments WHERE attachment_id = ? AND lesson_id = ?");
$stmt->bind_param("ii", $attachment_id, $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

$file = $result->fetch_assoc();

// Delete physical file
if (delete_file($file['file_name'])) {
    // Delete database record
    $stmt = $conn->prepare("DELETE FROM attachments WHERE attachment_id = ?");
    $stmt->bind_param("i", $attachment_id);
    
    if ($stmt->execute()) {
        log_activity($conn, 'delete', 'attachments', $attachment_id, "ลบไฟล์: {$file['file_original_name']}");
        echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Cannot delete file']);
}

$stmt->close();
$conn->close();
?>