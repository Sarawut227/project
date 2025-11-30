<?php
/**
 * Frontend Lesson View
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$lesson_id = intval($_GET['id'] ?? 0);

// Get lesson with subject and level info
$stmt = $conn->prepare("
    SELECT l.*, s.subject_name, s.subject_code, s.subject_id, lv.level_name, lv.level_id
    FROM lessons l
    JOIN subjects s ON l.subject_id = s.subject_id
    JOIN levels lv ON s.level_id = lv.level_id
    WHERE l.lesson_id = ? AND l.status = 'published'
");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$lesson = $result->fetch_assoc();

// Update view count
$conn->query("UPDATE lessons SET view_count = view_count + 1 WHERE lesson_id = {$lesson_id}");

// Get attachments
$attachments = $conn->query("SELECT * FROM attachments WHERE lesson_id = {$lesson_id}");

// Get other lessons in same subject
$other_lessons = $conn->query("
    SELECT lesson_id, lesson_title, sort_order
    FROM lessons
    WHERE subject_id = {$lesson['subject_id']} AND status = 'published'
    ORDER BY sort_order ASC
");

$site_name = get_setting($conn, 'site_name', SITE_NAME);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($lesson['lesson_title']); ?> - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
		.lesson-layout {
			display: flex;
			gap: 24px;
			margin-top: 24px;
			min-height: 65vh;
		}
        .lesson-sidebar {
            width: 300px;
            position: sticky;
            top: 80px;
            height: fit-content;
        }
        .lesson-content {
            flex: 1;
        }
        .lesson-nav-item {
            padding: 12px 16px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            color: var(--text-primary);
            text-decoration: none;
            display: block;
            margin-bottom: 4px;
        }
        .lesson-nav-item:hover {
            background: var(--bg-main);
            border-left-color: var(--primary-color);
        }
        .lesson-nav-item.active {
            background: rgba(74, 144, 226, 0.1);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .lesson-layout {
                flex-direction: column;
            }
            .lesson-sidebar {
                width: 100%;
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-graduation-cap"></i> <?php echo $site_name; ?>
            </a>
            <ul class="navbar-menu">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>/login.php">เข้าสู่ระบบ</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Breadcrumb -->
    <div class="container" style="margin-top: 40px;">
        <nav style="color: var(--text-secondary); font-size: 14px;">
            <a href="index.php" style="color: var(--text-secondary);">หน้าแรก</a>
            <span> / </span>
            <a href="subjects.php?level=<?php echo $lesson['level_id']; ?>" style="color: var(--text-secondary);">
                <?php echo e($lesson['level_name']); ?>
            </a>
            <span> / </span>
            <a href="lessons.php?subject=<?php echo $lesson['subject_id']; ?>" style="color: var(--text-secondary);">
                <?php echo e($lesson['subject_name']); ?>
            </a>
            <span> / </span>
            <span><?php echo e($lesson['lesson_title']); ?></span>
        </nav>
    </div>
    
    <div class="container">
        <div class="lesson-layout">
            <!-- Sidebar Navigation -->
            <aside class="lesson-sidebar">
                <div class="card">
                    <h3 style="margin-bottom: 16px;">
                        <i class="fas fa-list"></i> สารบัญ
                    </h3>
                    <div>
                        <?php while ($other = $other_lessons->fetch_assoc()): ?>
                            <a href="lesson.php?id=<?php echo $other['lesson_id']; ?>" 
                               class="lesson-nav-item <?php echo ($other['lesson_id'] == $lesson_id) ? 'active' : ''; ?>">
                                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">
                                    บทที่ <?php echo $other['sort_order']; ?>
                                </div>
                                <div><?php echo e($other['lesson_title']); ?></div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="lesson-content">
                <div class="card">
                    <div style="    margin-bottom: 35px;    display: flex;    gap: 10px;">
                        <span class="badge badge-primary"><?php echo e($lesson['subject_code']); ?></span>
                        <span class="badge badge-info">บทที่ <?php echo $lesson['sort_order']; ?></span>
                    </div>
                    
                    <h1 style="margin-bottom: 16px;"><?php echo e($lesson['lesson_title']); ?></h1>
                    
                    <div style="padding-bottom: 16px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary);">
                        <i class="fas fa-eye"></i> เข้าชม <?php echo number_format($lesson['view_count']); ?> ครั้ง
                        &nbsp;|&nbsp;
                        <i class="fas fa-calendar"></i> <?php echo thai_date($lesson['updated_at'], 'd/m/Y H:i'); ?>
                    </div>
                    
                    <!-- Lesson Content -->
                    <div style="line-height: 1.8; color: var(--text-primary);">
                        <?php echo $lesson['lesson_content']; ?>
                    </div>
                </div>
                
				<!-- Attachments Section -->
				<?php if ($attachments->num_rows > 0): ?>
				<div class="card">
					<h3 style="margin-bottom: 20px;">
						<i class="fas fa-paperclip"></i> ไฟล์แนบ
					</h3>
					<div style="display: flex; flex-direction: column; gap: 12px;">
						<?php while ($file = $attachments->fetch_assoc()): ?>
							<a href="<?php echo UPLOAD_URL . $file['file_name']; ?>" 
							   class="file-item" 
							   download
							   style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-main); border-radius: 30px; text-decoration: none; transition: all 0.3s ease;">
								<div style="display: flex; align-items: center; gap: 12px;">
									<div style="width: 48px; height: 48px; background: linear-gradient(135deg, rgba(30, 64, 175, 0.1), rgba(59, 130, 246, 0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
										<i class="fas fa-file" style="color: var(--primary-color); font-size: 20px;"></i>
									</div>
									<div>
										<div style="font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">
											<?php 
											// แสดงชื่อที่กำหนด หรือชื่อเดิมถ้าไม่มี
											echo e($file['display_name'] ?? $file['file_original_name']); 
											?>
										</div>
										<div style="font-size: 13px; color: var(--text-secondary);">
											<?php echo format_file_size($file['file_size']); ?>
										</div>
									</div>
								</div>
								<div style="padding: 10px 20px; background: var(--primary-color); color: white; border-radius: 30px; font-size: 14px; font-weight: 600;">
									<i class="fas fa-download"></i> ดาวน์โหลด
								</div>
							</a>
						<?php endwhile; ?>
					</div>
				</div>
				<?php endif; ?>

            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>