<?php
/**
 * API: Get subjects by level
 */

require_once '../../config/database.php';

header('Content-Type: application/json');

$level_id = intval($_GET['level_id'] ?? 0);

if ($level_id === 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT subject_id, subject_code, subject_name 
    FROM subjects 
    WHERE level_id = ? AND is_active = 1 
    ORDER BY sort_order ASC
");
$stmt->bind_param("i", $level_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
$stmt->close();
$conn->close();
?>