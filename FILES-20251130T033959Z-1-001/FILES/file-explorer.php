<?php
/**
 * Simple File Lister - แสดงรายการไฟล์แบบง่ายๆ
 * Path: /show-files.php
 * เข้าดูที่: http://bigzdemo17.live/show-files.php
 */

// โฟลเดอร์ที่จะซ่อน
$exclude = ['.git', 'node_modules', 'vendor', '.DS_Store'];

function listFiles($dir, $prefix = '') {
    $items = scandir($dir);
    $output = '';
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $GLOBALS['exclude'])) {
            continue;
        }
        
        $path = $dir . '/' . $item;
        
        if (is_dir($path)) {
            $output .= $prefix . '📁 ' . $item . "/\n";
            $output .= listFiles($path, $prefix . '│   ');
        } else {
            $output .= $prefix . '📄 ' . $item . "\n";
        }
    }
    
    return $output;
}

$rootPath = __DIR__;
$structure = listFiles($rootPath);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #252526;
            border-radius: 8px;
            padding: 20px;
        }
        h1 {
            color: #4fc3f7;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .info {
            color: #888;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .actions {
            margin-bottom: 15px;
        }
        button {
            background: #0e639c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        button:hover {
            background: #1177bb;
        }
        .success {
            background: #4caf50;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }
        pre {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 4px;
            overflow-x: auto;
            line-height: 1.6;
            font-size: 14px;
            border: 1px solid #3e3e3e;
        }
        .stats {
            background: #2d2d2d;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .stat {
            color: #4fc3f7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📂 Project Structure</h1>
        <div class="info">Root Path: <?php echo $rootPath; ?></div>
        
        <div class="stats">
            <div class="stat">📄 Files: <?php echo substr_count($structure, '📄'); ?></div>
            <div class="stat">📁 Folders: <?php echo substr_count($structure, '📁'); ?></div>
        </div>
        
        <div class="actions">
            <button onclick="copyToClipboard()">📋 Copy All</button>
            <button onclick="downloadAsText()">💾 Download as .txt</button>
        </div>
        
        <div class="success" id="successMsg">✅ Copied to clipboard!</div>
        
        <pre id="fileList"><?php echo htmlspecialchars($structure); ?></pre>
    </div>
    
    <script>
        function copyToClipboard() {
            const text = document.getElementById('fileList').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const msg = document.getElementById('successMsg');
                msg.style.display = 'block';
                setTimeout(() => {
                    msg.style.display = 'none';
                }, 2000);
            });
        }
        
        function downloadAsText() {
            const text = document.getElementById('fileList').textContent;
            const blob = new Blob([text], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'unispaze-structure.txt';
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>