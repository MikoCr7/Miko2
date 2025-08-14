<?php
header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>251KENO - Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #1a1a1a;
            color: white;
        }
        .setup-section {
            background: #2a2a2a;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #007acc;
        }
        .success {
            border-left-color: #28a745;
        }
        .error {
            border-left-color: #dc3545;
        }
        button {
            background: #007acc;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        button:hover {
            background: #005fa3;
        }
        .code {
            background: #3a3a3a;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>🚀 251KENO - Quick Setup</h1>
    
    <div class="setup-section">
        <h2>📁 Directory Setup</h2>
        <p>This will create the necessary directories for your Keno game:</p>
        
        <?php
        if (isset($_POST['create_dirs'])) {
            $directories = ['logs', 'backups'];
            $created = [];
            $errors = [];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    if (mkdir($dir, 0755, true)) {
                        $created[] = $dir;
                    } else {
                        $errors[] = $dir;
                    }
                } else {
                    $created[] = "$dir (already exists)";
                }
            }
            
            if (!empty($created)) {
                echo '<div class="success">';
                echo '<strong>✅ Successfully created/verified directories:</strong><ul>';
                foreach ($created as $dir) {
                    echo "<li>$dir</li>";
                }
                echo '</ul></div>';
            }
            
            if (!empty($errors)) {
                echo '<div class="error">';
                echo '<strong>❌ Failed to create directories:</strong><ul>';
                foreach ($errors as $dir) {
                    echo "<li>$dir</li>";
                }
                echo '</ul></div>';
            }
            
            // Test log writing
            if (is_dir('logs') && is_writable('logs')) {
                $testLog = date('Y-m-d H:i:s') . " - Setup completed successfully\n";
                if (file_put_contents('logs/activity.log', $testLog, FILE_APPEND | LOCK_EX)) {
                    echo '<div class="success"><strong>✅ Logging system is working!</strong></div>';
                } else {
                    echo '<div class="error"><strong>❌ Failed to write to log file</strong></div>';
                }
            }
            
            echo '<div class="code">Setup completed! You can now:</div>';
            echo '<ul>';
            echo '<li><a href="test-database.php" style="color: #007acc;">Test Database Connection</a></li>';
            echo '<li><a href="index.html" style="color: #007acc;">Play the Game</a></li>';
            echo '<li><a href="admin-modern.php" style="color: #007acc;">Access Admin Panel</a></li>';
            echo '</ul>';
        } else {
            echo '<form method="post">';
            echo '<button type="submit" name="create_dirs">🏗️ Create Directories</button>';
            echo '</form>';
            echo '<p><small>This will create:</small></p>';
            echo '<ul>';
            echo '<li><code>logs/</code> - For activity logging</li>';
            echo '<li><code>backups/</code> - For database backups</li>';
            echo '</ul>';
        }
        ?>
    </div>
    
    <div class="setup-section">
        <h2>🔧 File Permissions</h2>
        <p>Current directory permissions:</p>
        <div class="code">
            <?php
            $currentDir = getcwd();
            echo "Current directory: $currentDir<br>";
            echo "Is writable: " . (is_writable($currentDir) ? "✅ Yes" : "❌ No") . "<br>";
            
            if (is_dir('logs')) {
                echo "logs/ is writable: " . (is_writable('logs') ? "✅ Yes" : "❌ No") . "<br>";
            }
            
            if (is_dir('backups')) {
                echo "backups/ is writable: " . (is_writable('backups') ? "✅ Yes" : "❌ No") . "<br>";
            }
            ?>
        </div>
    </div>
    
    <div class="setup-section">
        <h2>📋 Quick Test</h2>
        <p>Test if the registration API works now:</p>
        
        <?php if (isset($_POST['test_quick'])): ?>
            <div class="code">
                <?php
                // Test registration without the warning
                $testData = [
                    'action' => 'register',
                    'name' => 'Quick Test ' . rand(1000, 9999),
                    'phone' => '0913' . rand(100000, 999999)
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api.php');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                echo "HTTP Status: $httpCode<br>";
                echo "Raw Response: " . htmlspecialchars($response) . "<br><br>";
                
                // Try to extract JSON from response
                $jsonStart = strpos($response, '{');
                if ($jsonStart !== false) {
                    $jsonResponse = substr($response, $jsonStart);
                    echo "JSON Part: " . htmlspecialchars($jsonResponse) . "<br>";
                    
                    $result = json_decode($jsonResponse, true);
                    if ($result && $result['success']) {
                        echo '<span style="color: #28a745;">✅ Registration is working!</span><br>';
                    } else {
                        echo '<span style="color: #dc3545;">❌ Registration failed</span><br>';
                    }
                } else {
                    echo '<span style="color: #dc3545;">❌ No JSON response found</span><br>';
                }
                ?>
            </div>
        <?php else: ?>
            <form method="post">
                <button type="submit" name="test_quick">🧪 Quick Test Registration</button>
            </form>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 30px; color: #666;">
        <p>251KENO - Setup Tool v1.0</p>
    </div>
</body>
</html>
