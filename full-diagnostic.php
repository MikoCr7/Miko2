<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>🔍 251KENO - Complete Diagnostic</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #1a1a1a;
            color: white;
            line-height: 1.6;
        }
        .diagnostic-header {
            text-align: center;
            background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(142, 45, 226, 0.3);
        }
        .section {
            background: #2a2a2a;
            margin: 20px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .section-header {
            background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%);
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-content {
            padding: 20px;
        }
        .test-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            margin: 8px 0;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-left: 4px solid #666;
        }
        .test-item.success {
            border-left-color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
        }
        .test-item.error {
            border-left-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }
        .test-item.warning {
            border-left-color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
        }
        .status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.success { background: #22c55e; color: white; }
        .status.error { background: #ef4444; color: white; }
        .status.warning { background: #f59e0b; color: white; }
        .code-block {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
            border: 1px solid #333;
        }
        .button {
            background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(142, 45, 226, 0.4);
        }
        .api-test-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #00B4DB;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 6px;
            background: #333;
            color: white;
            font-size: 16px;
        }
        .api-response {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border: 1px solid #333;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            border: 2px solid #00B4DB;
            background: transparent;
            color: #00B4DB;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-btn.active, .tab-btn:hover {
            background: #00B4DB;
            color: white;
        }
    </style>
    <script>
        function testAPI(action, formId) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);
            data.action = action;
            
            const responseDiv = document.getElementById(action + '-response');
            responseDiv.innerHTML = '🔄 Testing...';
            
            fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.text())
            .then(text => {
                // Try to extract JSON from response
                let jsonStart = text.indexOf('{');
                if (jsonStart !== -1) {
                    let jsonText = text.substring(jsonStart);
                    try {
                        let jsonData = JSON.parse(jsonText);
                        responseDiv.innerHTML = 
                            'Raw Response:\n' + text + '\n\n' +
                            'JSON Response:\n' + JSON.stringify(jsonData, null, 2);
                        responseDiv.style.color = jsonData.success ? '#22c55e' : '#ef4444';
                    } catch (e) {
                        responseDiv.innerHTML = 'Raw Response:\n' + text + '\n\nJSON Parse Error: ' + e.message;
                        responseDiv.style.color = '#ef4444';
                    }
                } else {
                    responseDiv.innerHTML = 'No JSON found in response:\n' + text;
                    responseDiv.style.color = '#ef4444';
                }
            })
            .catch(error => {
                responseDiv.innerHTML = 'Network Error: ' + error.message;
                responseDiv.style.color = '#ef4444';
            });
            
            return false;
        }

        function generateRandomPhone() {
            return '0913' + Math.floor(Math.random() * 900000 + 100000);
        }

        function generateRandomName() {
            const names = ['Abel Tesfaye', 'Meron Bekele', 'Dawit Assefa', 'Ruth Haile', 'Samuel Desta'];
            return names[Math.floor(Math.random() * names.length)] + ' ' + Math.floor(Math.random() * 1000);
        }

        function fillRandomData(prefix) {
            document.getElementById(prefix + '-name').value = generateRandomName();
            document.getElementById(prefix + '-phone').value = generateRandomPhone();
        }
    </script>
</head>
<body>
    <div class="diagnostic-header">
        <h1>🔍 251KENO Complete System Diagnostic</h1>
        <p>Comprehensive testing of all system components</p>
    </div>

    <!-- SYSTEM CHECK SECTION -->
    <div class="section">
        <div class="section-header">
            <span>⚙️</span>
            <span>System Environment Check</span>
        </div>
        <div class="section-content">
            <?php
            $checks = [
                ['PHP Version', phpversion(), version_compare(phpversion(), '7.4', '>=')],
                ['PDO Extension', extension_loaded('pdo'), extension_loaded('pdo')],
                ['PDO MySQL', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql')],
                ['JSON Extension', extension_loaded('json'), extension_loaded('json')],
                ['Logs Directory', 'logs/', is_dir('logs') && is_writable('logs')],
                ['Current Directory Writable', getcwd(), is_writable(getcwd())],
            ];
            
            foreach ($checks as $check) {
                $status = $check[2] ? 'success' : 'error';
                $statusText = $check[2] ? 'PASS' : 'FAIL';
                echo "<div class='test-item $status'>";
                echo "<span><strong>{$check[0]}:</strong> {$check[1]}</span>";
                echo "<span class='status $status'>$statusText</span>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- DATABASE CHECK SECTION -->
    <div class="section">
        <div class="section-header">
            <span>🗄️</span>
            <span>Database Connection & Structure</span>
        </div>
        <div class="section-content">
            <?php
            echo "<div class='code-block'>Database: " . DB_NAME . "\nHost: " . DB_HOST . "\nUser: " . DB_USER . "</div>";
            
            $dbTests = [];
            
            // Test connection
            $pdo = getDBConnection();
            if ($pdo) {
                $dbTests[] = ['Database Connection', 'Connected', true];
                
                // Test required tables
                $requiredTables = ['players', 'game_history', 'transactions', 'game_sessions'];
                foreach ($requiredTables as $table) {
                    try {
                        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                        $stmt->execute([$table]);
                        $exists = $stmt->fetch() !== false;
                        $dbTests[] = ["Table: $table", $exists ? 'EXISTS' : 'MISSING', $exists];
                    } catch (Exception $e) {
                        $dbTests[] = ["Table: $table", 'ERROR: ' . $e->getMessage(), false];
                    }
                }
                
                // Test players table structure
                try {
                    $stmt = $pdo->query("DESCRIBE players");
                    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $requiredColumns = ['id', 'phone', 'username', 'balance', 'created_at'];
                    $hasAllColumns = count(array_intersect($requiredColumns, $columns)) === count($requiredColumns);
                    $dbTests[] = ['Players table structure', implode(', ', $columns), $hasAllColumns];
                } catch (Exception $e) {
                    $dbTests[] = ['Players table structure', 'ERROR: ' . $e->getMessage(), false];
                }
                
                // Count existing records
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) FROM players");
                    $count = $stmt->fetchColumn();
                    $dbTests[] = ['Existing players', $count . ' records', true];
                } catch (Exception $e) {
                    $dbTests[] = ['Existing players', 'ERROR: ' . $e->getMessage(), false];
                }
                
            } else {
                $dbTests[] = ['Database Connection', 'FAILED', false];
            }
            
            foreach ($dbTests as $test) {
                $status = $test[2] ? 'success' : 'error';
                $statusText = $test[2] ? 'PASS' : 'FAIL';
                echo "<div class='test-item $status'>";
                echo "<span><strong>{$test[0]}:</strong> {$test[1]}</span>";
                echo "<span class='status $status'>$statusText</span>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- FILE CHECK SECTION -->
    <div class="section">
        <div class="section-header">
            <span>📁</span>
            <span>File System Check</span>
        </div>
        <div class="section-content">
            <?php
            $requiredFiles = [
                'index.html' => 'Main game file',
                'api.php' => 'API endpoint',
                'config.php' => 'Configuration file',
                'database_schema.sql' => 'Database schema',
            ];
            
            foreach ($requiredFiles as $file => $description) {
                $exists = file_exists($file);
                $size = $exists ? filesize($file) : 0;
                $status = $exists ? 'success' : 'error';
                $statusText = $exists ? 'EXISTS (' . number_format($size) . ' bytes)' : 'MISSING';
                
                echo "<div class='test-item $status'>";
                echo "<span><strong>$file:</strong> $description</span>";
                echo "<span class='status $status'>$statusText</span>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- API TESTING SECTION -->
    <div class="section">
        <div class="section-header">
            <span>🚀</span>
            <span>API Testing</span>
        </div>
        <div class="section-content">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="showTab('register-tab-content')">Test Registration</button>
                <button class="tab-btn" onclick="showTab('login-tab-content')">Test Login</button>
            </div>
            
            <!-- Registration Test -->
            <div id="register-tab-content">
                <div class="api-test-form">
                    <h3>📝 Registration API Test</h3>
                    <form id="register-form" onsubmit="return testAPI('register', 'register-form')">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" id="register-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone (09xxxxxxxx):</label>
                            <input type="tel" id="register-phone" name="phone" pattern="09[0-9]{8}" maxlength="10" required>
                        </div>
                        <button type="button" onclick="fillRandomData('register')" class="button">🎲 Random Data</button>
                        <button type="submit" class="button">🧪 Test Register</button>
                    </form>
                    <div class="api-response" id="register-response">Click "Test Register" to see API response</div>
                </div>
            </div>
            
            <!-- Login Test -->
            <div id="login-tab-content" style="display:none;">
                <div class="api-test-form">
                    <h3>🔐 Login API Test</h3>
                    <form id="login-form" onsubmit="return testAPI('login', 'login-form')">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" id="login-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone (09xxxxxxxx):</label>
                            <input type="tel" id="login-phone" name="phone" pattern="09[0-9]{8}" maxlength="10" required>
                        </div>
                        <button type="button" onclick="fillRandomData('login')" class="button">🎲 Random Data</button>
                        <button type="submit" class="button">🧪 Test Login</button>
                    </form>
                    <div class="api-response" id="login-response">Click "Test Login" to see API response</div>
                </div>
            </div>
        </div>
    </div>

    <!-- BROWSER CONSOLE CHECK -->
    <div class="section">
        <div class="section-header">
            <span>🌐</span>
            <span>Frontend Debugging Instructions</span>
        </div>
        <div class="section-content">
            <p><strong>To debug the frontend login/register buttons:</strong></p>
            <ol>
                <li>Open your game at <code>index.html</code></li>
                <li>Press <code>F12</code> to open Browser Developer Tools</li>
                <li>Go to the <strong>Console</strong> tab</li>
                <li>Try to register/login and watch for error messages</li>
                <li>Check the <strong>Network</strong> tab to see if API calls are being made</li>
            </ol>
            
            <div class="code-block">
// You can also test the JavaScript functions directly in console:
handleRegister();
handleLogin();

// Or check if the functions exist:
typeof handleLogin;   // should return "function"
typeof handleRegister; // should return "function"
            </div>
            
            <p><strong>Common issues to check:</strong></p>
            <ul>
                <li>Are there any JavaScript errors in console?</li>
                <li>Do the API calls reach the server?</li>
                <li>Are there any CORS errors?</li>
                <li>Do the input fields have the correct IDs?</li>
            </ul>
        </div>
    </div>

    <!-- QUICK LINKS -->
    <div class="section">
        <div class="section-header">
            <span>🔗</span>
            <span>Quick Access</span>
        </div>
        <div class="section-content" style="text-align: center;">
            <a href="index.html" class="button">🎮 Play Game</a>
            <a href="test-database.php" class="button">🗄️ Database Test</a>
            <a href="setup.php" class="button">⚙️ Setup Tool</a>
            <a href="admin-modern.php" class="button">👨‍💼 Admin Panel</a>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.getElementById('register-tab-content').style.display = 'none';
            document.getElementById('login-tab-content').style.display = 'none';
            
            // Show selected tab
            document.getElementById(tabId).style.display = 'block';
            
            // Update active button
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
