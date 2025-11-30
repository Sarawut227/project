/**
 * Admin JavaScript
 * LMS System
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
        }
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้')) {
                e.preventDefault();
            }
        });
    });
    
    // Table search
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
});

/**
 * Load subjects based on level (for cascading dropdown)
 */
function loadSubjects(levelId, selectedSubjectId = null) {
    const subjectSelect = document.getElementById('subject_id');
    if (!subjectSelect) return;
    
    // Clear current options
    subjectSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
    
    // Fetch subjects
    fetch(`../api/get_subjects.php?level_id=${levelId}`)
        .then(response => response.json())
        .then(data => {
            subjectSelect.innerHTML = '<option value="">-- เลือกรายวิชา --</option>';
            data.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.subject_id;
                option.textContent = subject.subject_code + ' - ' + subject.subject_name;
                if (selectedSubjectId && subject.subject_id == selectedSubjectId) {
                    option.selected = true;
                }
                subjectSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
            subjectSelect.innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
        });
}

/**
 * Delete file attachment
 */
function deleteAttachment(attachmentId, lessonId) {
    if (!confirm('ต้องการลบไฟล์นี้?')) {
        return;
    }
    
    fetch('../api/delete_attachment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `attachment_id=${attachmentId}&lesson_id=${lessonId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('attachment-' + attachmentId).remove();
            alert('ลบไฟล์เรียบร้อย');
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการลบไฟล์');
    });
}