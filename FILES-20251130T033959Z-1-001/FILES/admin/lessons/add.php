<?php
/**
 * =================================================
 * ไฟล์: admin/lessons/add.php
 * หน้าที่: เพิ่มบทเรียนพร้อม Summernote + ตั้งชื่อไฟล์ได้
 * =================================================
 */

require_once '../includes/auth.php';

$page_title = 'สร้างบทเรียน';

// Get levels
$levels = $conn->query("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $lesson_title = clean_input($_POST['lesson_title']);
    $lesson_content = $_POST['lesson_content']; // Don't clean HTML content
    $sort_order = intval($_POST['sort_order']);
    $status = clean_input($_POST['status']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        set_alert('danger', 'Invalid request');
    } else {
        if (empty($lesson_title) || $subject_id == 0) {
            set_alert('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
        } else {
            // Insert lesson
            $stmt = $conn->prepare("INSERT INTO lessons (subject_id, lesson_title, lesson_content, sort_order, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $user_id = get_user_id();
            $stmt->bind_param("issisi", $subject_id, $lesson_title, $lesson_content, $sort_order, $status, $user_id);
            
            if ($stmt->execute()) {
                $lesson_id = $stmt->insert_id;
                
                // Handle file uploads with display names
                if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
                    $upload_errors = [];
                    $files_count = count($_FILES['attachments']['name']);
                    $display_names = $_POST['file_display_names'] ?? [];
                    
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
                                $display_name = !empty($display_names[$i]) ? clean_input($display_names[$i]) : $upload_result['original_name'];
                                
                                // Insert attachment record with display_name
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
                            } else {
                                $upload_errors[] = $upload_result['message'];
                            }
                        }
                    }
                    
                    if (!empty($upload_errors)) {
                        set_alert('warning', 'บทเรียนถูกสร้างแล้ว แต่บางไฟล์ไม่สามารถอัปโหลดได้: ' . implode(', ', $upload_errors));
                    }
                }
                
                log_activity($conn, 'create', 'lessons', $lesson_id, "สร้างบทเรียน: {$lesson_title}");
                set_alert('success', 'สร้างบทเรียนเรียบร้อยแล้ว');
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
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        .file-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        

        .file-card .btn-icon {
            transition: all 0.3s ease;
        }
        

        .file-upload-area {
            transition: all 0.3s ease;
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
                    <h1 class="page-title">สร้างบทเรียน</h1>
                    <ul class="page-breadcrumb">
                        <li><a href="../index.php">แดชบอร์ด</a></li>
                        <li><a href="index.php">จัดการบทเรียน</a></li>
                        <li>สร้างบทเรียน</li>
                    </ul>
                </div>
                
                <div class="card">
                    <form method="POST" action="" enctype="multipart/form-data" id="lessonForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <div class="form-group">
                            <label class="form-label">ระดับชั้น <span style="color: red;">*</span></label>
                            <select name="level_id" id="level_id" class="form-control" required onchange="loadSubjects(this.value)">
                                <option value="">-- เลือกระดับชั้น --</option>
                                <?php while ($lv = $levels->fetch_assoc()): ?>
                                    <option value="<?php echo $lv['level_id']; ?>"><?php echo e($lv['level_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">รายวิชา <span style="color: red;">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-control" required>
                                <option value="">-- เลือกระดับชั้นก่อน --</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ชื่อบทเรียน <span style="color: red;">*</span></label>
                            <input type="text" name="lesson_title" class="form-control" required placeholder="เช่น บทที่ 1 แนะนำ">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">เนื้อหา</label>
                            <textarea name="lesson_content" id="summernote"></textarea>
                        </div>
                        
                        <!-- Upload Files with Display Names -->
                        <div class="form-group">
                            <label class="form-label">ไฟล์แนบ</label>
                            <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</p>
                                <small>รองรับ: PDF, Images, Documents (สูงสุด 10MB/ไฟล์)</small>
                            </div>
                            <input type="file" id="fileInput" name="attachments[]" multiple style="display: none;" onchange="displayFilesWithNames(this.files)" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt">
                            <div id="fileList" class="file-list"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">ลำดับการแสดงผล</label>
                            <input type="number" name="sort_order" class="form-control" value="1" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-control">
                                <option value="published">เผยแพร่</option>
                                <option value="draft">ร่าง</option>
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
    
<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    <script>
        // Initialize Summernote
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
         * Display selected files with beautiful card layout
         */
        let selectedFiles = [];
        
        function displayFilesWithNames(files) {
            const fileList = document.getElementById('fileList');
            
            // เก็บไฟล์ใหม่
            for (let i = 0; i < files.length; i++) {
                selectedFiles.push(files[i]);
            }
            
            // แสดงผลทั้งหมด
            renderFileList();
        }
        
        function renderFileList() {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            
            if (selectedFiles.length === 0) {
                return;
            }
            
            // แสดง Badge จำนวนไฟล์
            const headerDiv = document.createElement('div');
            headerDiv.style.marginBottom = '12px';
            headerDiv.innerHTML = `
                <label class="form-label" style="margin: 0;">
                    <i class="fas fa-paperclip"></i> ไฟล์ที่เลือก 
                    <span class="badge badge-success" style="margin-left: 8px;">${selectedFiles.length} ไฟล์</span>
                </label>
            `;
            fileList.appendChild(headerDiv);
            
            // Container สำหรับ cards
            const cardsContainer = document.createElement('div');
            cardsContainer.style.display = 'grid';
            cardsContainer.style.gap = '16px';
            
            selectedFiles.forEach((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                const fileName = file.name.replace(/\.[^/.]+$/, ''); // ชื่อไฟล์ไม่มีนามสกุล
                const fileExt = file.name.split('.').pop().toUpperCase();
                
                // เลือก icon ตามประเภทไฟล์
                let fileIcon = 'fa-file';
                let iconColor = '#4A90E2';
                if (['JPG', 'JPEG', 'PNG', 'GIF'].includes(fileExt)) {
                    fileIcon = 'fa-file-image';
                    iconColor = '#48bb78';
                } else if (['PDF'].includes(fileExt)) {
                    fileIcon = 'fa-file-pdf';
                    iconColor = '#f56565';
                } else if (['DOC', 'DOCX'].includes(fileExt)) {
                    fileIcon = 'fa-file-word';
                    iconColor = '#4299e1';
                } else if (['XLS', 'XLSX'].includes(fileExt)) {
                    fileIcon = 'fa-file-excel';
                    iconColor = '#48bb78';
                } else if (['PPT', 'PPTX'].includes(fileExt)) {
                    fileIcon = 'fa-file-powerpoint';
                    iconColor = '#ed8936';
                }
                
                const fileCard = document.createElement('div');
                fileCard.className = 'file-card';
                fileCard.style.cssText = `
                    background: #f8f9fa;
                    border-radius: 20px;
                    padding: 20px;
                    border: 2px solid #e9ecef;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                `;
                
                fileCard.innerHTML = `
                    <!-- Header: ข้อมูลไฟล์ + ปุ่มลบ -->
                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                        <!-- Icon -->
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, ${iconColor}15, ${iconColor}25); border-radius: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas ${fileIcon}" style="font-size: 24px; color: ${iconColor};"></i>
                        </div>
                        
                        <!-- File Info -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 15px; color: var(--text-primary); margin-bottom: 6px; word-break: break-word;">
                                ${file.name}
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: white; border-radius: 20px; font-size: 12px; font-weight: 600; color: ${iconColor};">
                                    ${fileExt}
                                </span>
                                <span style="font-size: 13px; color: var(--text-secondary);">
                                    <i class="fas fa-hdd" style="margin-right: 4px;"></i>
                                    ${fileSize}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Remove Button -->
                        <button type="button" 
                                class="btn-icon btn-delete" 
                                onclick="removeFileItem(${index})" 
                                title="ลบไฟล์นี้"
                                style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- Edit Display Name -->
                    <div style="background: white; padding: 16px; border-radius: 15px; border: 1px solid #e9ecef;">
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-tag"></i> ชื่อไฟล์ที่จะแสดงผล
                        </label>
                        <input type="text" 
                               name="file_display_names[]" 
                               class="form-control" 
                               placeholder="ตั้งชื่อไฟล์ที่ต้องการแสดง" 
                               value="${fileName}"
                               style="font-size: 14px; border-radius: 30px;">
                        <small class="form-text" style="margin-top: 6px; display: block;">
                            <i class="fas fa-info-circle"></i> ชื่อนี้จะแสดงให้ผู้เรียนเห็นเมื่อดาวน์โหลดไฟล์
                        </small>
                    </div>
                `;
                
                // Add hover effect
                fileCard.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 16px rgba(0, 0, 0, 0.1)';
                    this.style.borderColor = iconColor;
                });
                
                fileCard.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                    this.style.borderColor = '#e9ecef';
                });
                
                cardsContainer.appendChild(fileCard);
            });
            
            fileList.appendChild(cardsContainer);
            
            // Update file input
            updateFileInput();
        }
        
        function removeFileItem(index) {
            selectedFiles.splice(index, 1);
            renderFileList();
        }
        
        function updateFileInput() {
            const fileInput = document.getElementById('fileInput');
            const dataTransfer = new DataTransfer();
            
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            fileInput.files = dataTransfer.files;
        }
    </script>
</body>
</html>
