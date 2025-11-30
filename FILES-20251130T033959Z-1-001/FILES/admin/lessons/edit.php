<?php
/**
 * =================================================
 * ไฟล์: admin/lessons/edit.php
 * หน้าที่: แก้ไขบทเรียน + แก้ไขชื่อไฟล์ได้
 * =================================================
 */

require_once '../includes/auth.php';

$page_title = 'แก้ไขบทเรียน';

$lesson_id = intval($_GET['id'] ?? 0);

// Get lesson data
$stmt = $conn->prepare("
    SELECT l.*, s.level_id, s.subject_name, lv.level_name
    FROM lessons l
    JOIN subjects s ON l.subject_id = s.subject_id
    JOIN levels lv ON s.level_id = lv.level_id
    WHERE l.lesson_id = ?
");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'ไม่พบข้อมูลบทเรียน');
    redirect('index.php');
}

$lesson = $result->fetch_assoc();

// ตรวจสอบสิทธิ์
if (!can_edit_resource($lesson['created_by'])) {
    set_alert('danger', 'คุณไม่มีสิทธิ์แก้ไขบทเรียนนี้');
    redirect('index.php');
}

// Get attachments
$attachments = $conn->query("SELECT * FROM attachments WHERE lesson_id = {$lesson_id} ORDER BY uploaded_at");

// Get levels
$levels = $conn->query("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");

// Get subjects for current level
$subjects = $conn->query("SELECT * FROM subjects WHERE level_id = {$lesson['level_id']} AND is_active = 1 ORDER BY sort_order");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $lesson_title = clean_input($_POST['lesson_title']);
    $lesson_content = $_POST['lesson_content'];
    $sort_order = intval($_POST['sort_order']);
    $status = clean_input($_POST['status']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Handle attachment display name updates
    $attachment_display_names = $_POST['attachment_display_names'] ?? [];
    
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        if (empty($lesson_title) || $subject_id == 0) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // Update lesson
            $stmt = $conn->prepare("UPDATE lessons SET subject_id = ?, lesson_title = ?, lesson_content = ?, sort_order = ?, status = ? WHERE lesson_id = ?");
            $stmt->bind_param("issisi", $subject_id, $lesson_title, $lesson_content, $sort_order, $status, $lesson_id);
            
            if ($stmt->execute()) {
                // Update existing attachment display names
                foreach ($attachment_display_names as $attach_id => $display_name) {
                    $attach_id = intval($attach_id);
                    $display_name = clean_input($display_name);
                    
                    if (!empty($display_name)) {
                        $stmt_update = $conn->prepare("UPDATE attachments SET display_name = ? WHERE attachment_id = ? AND lesson_id = ?");
                        $stmt_update->bind_param("sii", $display_name, $attach_id, $lesson_id);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                }
                
                // Handle new file uploads
                if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                    $files_count = count($_FILES['attachments']['name']);
                    $new_file_display_names = $_POST['new_file_display_names'] ?? [];
                    
                    for ($i = 0; $i < $files_count; $i++) {
                        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                            $file = [
                                'name' => $_FILES['attachments']['name'][$i],
                                'type' => $_FILES['attachments']['type'][$i],
                                'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                                'error' => $_FILES['attachments']['error'][$i],
                                'size' => $_FILES['attachments']['size'][$i]
                            ];
                            
                            $upload_result = upload_file($file);
                            
                            if ($upload_result['success']) {
                                // ใช้ชื่อที่ผู้ใช้กำหนด หรือชื่อเดิมถ้าไม่ได้กำหนด
                                $display_name = !empty($new_file_display_names[$i]) ? clean_input($new_file_display_names[$i]) : $upload_result['original_name'];
                                
                                $stmt_file = $conn->prepare("INSERT INTO attachments (lesson_id, file_name, file_original_name, display_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                $stmt_file->bind_param("isssssi",
                                    $lesson_id,
                                    $upload_result['filename'],
                                    $upload_result['original_name'],
                                    $display_name,
                                    $upload_result['file_path'],
                                    $upload_result['file_type'],
                                    $upload_result['file_size']
                                );
                                $stmt_file->execute();
                                $stmt_file->close();
                            }
                        }
                    }
                }
                
                log_activity($conn, 'update', 'lessons', $lesson_id, "แก้ไขบทเรียน: {$lesson_title}");
                set_alert('success', 'แก้ไขบทเรียนเรียบร้อยแล้ว');
                redirect('index.php');
            } else {
                set_alert('danger', 'เกิดข้อผิดพลาด: ' . $conn->error);
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
	<style type="text/css">
		.file-card {
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}
	</style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include '../includes/topbar.php'; ?>
            
            <div class="admin-content">
                <?php show_alert(); ?>
                
                <div class="page-header">
                    <h1 class="page-title">แก้ไขบทเรียน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการบทเรียน</a></li>
                        <li>แก้ไขบทเรียน</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">ระดับชั้น <span style="color: red;">*</span></label>
                            <select name="level_id" id="level_id" class="form-control" required onchange="loadSubjects(this.value, <?php echo $lesson['subject_id']; ?>)">
                                <option value="">-- เลือกระดับชั้น --</option>
                                <?php 
                                $levels->data_seek(0);
                                while ($lv = $levels->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $lv['level_id']; ?>" <?php echo ($lesson['level_id'] == $lv['level_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($lv['level_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">รายวิชา <span style="color: red;">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-control" required>
                                <?php while ($subj = $subjects->fetch_assoc()): ?>
                                    <option value="<?php echo $subj['subject_id']; ?>" <?php echo ($lesson['subject_id'] == $subj['subject_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($subj['subject_code'] . ' - ' . $subj['subject_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อบทเรียน <span style="color: red;">*</span></label>
                            <input type="text" name="lesson_title" class="form-control" required value="<?php echo e($lesson['lesson_title']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">เนื้อหา</label>
                            <textarea name="lesson_content" id="summernote"><?php echo $lesson['lesson_content']; ?></textarea>
                        </div>
                        
                        <!-- Existing Files with Editable Display Names -->
                        <?php if ($attachments->num_rows > 0): ?>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-paperclip"></i> ไฟล์แนบปัจจุบัน 
                                    <span class="badge badge-info" style="margin-left: 8px;"><?php echo $attachments->num_rows; ?> ไฟล์</span>
                                </label>
                                <div style="display: grid; gap: 16px; margin-top: 12px;">
                                    <?php while ($file = $attachments->fetch_assoc()): ?>
                                        <div class="file-card" id="attachment-<?php echo $file['attachment_id']; ?>" style="background: #f8f9fa; border-radius: 20px; padding: 20px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <!-- Header: ข้อมูลไฟล์ + ปุ่มจัดการ -->
                                            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                                                <!-- Icon -->
                                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(30, 64, 175, 0.1), rgba(59, 130, 246, 0.1)); border-radius: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i class="fas fa-file-alt" style="font-size: 24px; color: var(--primary-color);"></i>
                                                </div>
                                                
                                                <!-- File Info -->
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="font-weight: 600; font-size: 15px; color: var(--text-primary); margin-bottom: 6px; word-break: break-word;">
                                                        <?php echo e($file['file_original_name']); ?>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                        <span style="font-size: 13px; color: var(--text-secondary);">
                                                            <i class="fas fa-hdd" style="margin-right: 4px;"></i>
                                                            <?php echo format_file_size($file['file_size']); ?>
                                                        </span>
                                                        <span style="font-size: 13px; color: var(--text-secondary);">
                                                            <i class="fas fa-clock" style="margin-right: 4px;"></i>
                                                            <?php echo thai_date($file['uploaded_at'], 'd/m/Y H:i'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                                                    <a href="<?php echo UPLOAD_URL . $file['file_name']; ?>" 
                                                       target="_blank" 
                                                       class="btn-icon btn-view" 
                                                       title="ดาวน์โหลด"
                                                       style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn-icon btn-delete" 
                                                            onclick="deleteAttachment(<?php echo $file['attachment_id']; ?>, <?php echo $lesson_id; ?>)" 
                                                            title="ลบ"
                                                            style="width: 40px; height: 40px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Edit Display Name -->
                                            <div style="background: white; padding: 16px; border-radius: 15px; border: 1px solid #e9ecef;">
                                                <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                                    <i class="fas fa-tag"></i> ชื่อไฟล์ที่แสดงผล
                                                </label>
                                                <input type="text" 
                                                       name="attachment_display_names[<?php echo $file['attachment_id']; ?>]" 
                                                       class="form-control" 
                                                       value="<?php echo e($file['display_name'] ?? $file['file_original_name']); ?>"
                                                       placeholder="ชื่อที่จะแสดงให้ผู้เรียนเห็น"
                                                       style="font-size: 14px; border-radius: 30px;">
                                                <small class="form-text" style="margin-top: 6px; display: block;">
                                                    <i class="fas fa-info-circle"></i> ชื่อนี้จะแสดงแทนชื่อไฟล์จริงเมื่อผู้เรียนดาวน์โหลด
                                                </small>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Upload New Files -->
                        <div class="form-group">
                            <label class="form-label">เพิ่มไฟล์แนบใหม่</label>
                            <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>คลิกเพื่ออัปโหลดไฟล์</p>
                            </div>
                            <input type="file" id="fileInput" name="attachments[]" multiple style="display: none;" onchange="displayNewFiles(this.files)" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt">
                            <div id="newFileList" class="file-list"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $lesson['sort_order']; ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-control">
                                <option value="published" <?php echo ($lesson['status'] == 'published') ? 'selected' : ''; ?>>เผยแพร่</option>
                                <option value="draft" <?php echo ($lesson['status'] == 'draft') ? 'selected' : ''; ?>>ร่าง</option>
                            </select>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 400,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
        
        /**
         * Display new files with editable names
         */
        let newFiles = [];
        
        function displayNewFiles(files) {
            const fileList = document.getElementById('newFileList');
            
            // เก็บไฟล์ใหม่
            for (let i = 0; i < files.length; i++) {
                newFiles.push(files[i]);
            }
            
            // แสดงผล
            renderNewFileList();
        }
        
        function renderNewFileList() {
            const fileList = document.getElementById('newFileList');
            fileList.innerHTML = '';
            
            newFiles.forEach((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                const fileName = file.name.replace(/\.[^/.]+$/, '');
                
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.style.display = 'flex';
                fileItem.style.flexDirection = 'column';
                fileItem.style.gap = '8px';
                fileItem.style.marginBottom = '12px';
                fileItem.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="file-info" style="flex: 1;">
                            <i class="fas fa-file"></i>
                            <div>
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${fileSize}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-icon btn-delete" onclick="removeNewFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="padding-left: 40px;">
                        <label style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">ชื่อไฟล์ที่จะแสดง:</label>
                        <input type="text" 
                               name="new_file_display_names[]" 
                               class="form-control" 
                               placeholder="ระบุชื่อไฟล์" 
                               value="${fileName}"
                               style="font-size: 14px;">
                        <small class="form-text">ชื่อนี้จะแสดงให้ผู้เรียนเห็น</small>
                    </div>
                `;
                fileList.appendChild(fileItem);
            });
            
            // Update input
            updateNewFileInput();
        }
        
        function removeNewFile(index) {
            newFiles.splice(index, 1);
            renderNewFileList();
        }
        
        function updateNewFileInput() {
            const fileInput = document.getElementById('fileInput');
            const dataTransfer = new DataTransfer();
            
            newFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            fileInput.files = dataTransfer.files;
        }
    </script>
</body>
</html>