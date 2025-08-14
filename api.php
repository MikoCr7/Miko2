<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

switch ($action) {
    case 'register':
        handleRegister($pdo, $input);
        break;
    case 'login':
        handleLogin($pdo, $input);
        break;
    case 'get_game_history':
        handleGetGameHistory($pdo, $input);
        break;
    case 'load_game':
        handleLoadGame($pdo, $input);
        break;
    case 'save_history':
        handleSaveHistory($pdo, $input);
        break;
    case 'get_history':
        handleGetHistory($pdo, $input);
        break;
    case 'update_balance':
        handleUpdateBalance($pdo, $input);
        break;
    case 'set_balance':
        handleSetBalance($pdo, $input);
        break;
    case 'debug_balance':
        handleDebugBalance($pdo, $input);
        break;
    case 'submit_withdrawal':
        handleSubmitWithdrawal($pdo, $input);
        break;
    case 'get_leaderboard':
        handleGetLeaderboard($pdo, $input);
        break;
    case 'get_live_bets':
        handleGetLiveBets($pdo, $input);
        break;
    // Round-based game functions
    case 'get_current_round':
        handleGetCurrentRound($pdo);
        break;
    case 'place_ticket':
        handlePlaceTicket($pdo, $input);
        break;
    case 'get_round_tickets':
        handleGetRoundTickets($pdo, $input);
        break;
    case 'get_player_tickets':
        handleGetPlayerTickets($pdo, $input);
        break;
    case 'process_round':
        handleProcessRound($pdo, $input);
        break;
    case 'get_round_results':
        handleGetRoundResults($pdo, $input);
        break;
    // Admin functions
    case 'get_dashboard_stats':
        handleGetDashboardStats($pdo);
        break;
    case 'get_all_users':
        handleGetAllUsers($pdo);
        break;
    case 'get_user':
        handleGetUser($pdo, $input);
        break;
    case 'update_user':
        handleUpdateUser($pdo, $input);
        break;
    case 'update_user_status':
        handleUpdateUserStatus($pdo, $input);
        break;
    case 'get_withdrawal_requests':
        handleGetWithdrawalRequests($pdo);
        break;
    case 'get_withdrawal':
        handleGetWithdrawal($pdo, $input);
        break;
    case 'process_withdrawal':
        handleProcessWithdrawal($pdo, $input);
        break;
    case 'get_all_transactions':
        handleGetAllTransactions($pdo);
        break;
    case 'get_all_games':
        handleGetAllGames($pdo);
        break;
    case 'get_game_history_admin':
        handleGetGameHistoryAdmin($pdo);
        break;
    case 'get_settings':
        handleGetSettings($pdo);
        break;
    case 'save_settings':
        handleSaveSettings($pdo, $input);
        break;
    case 'backup_database':
        handleBackupDatabase($pdo);
        break;
    case 'clean_old_data':
        handleCleanOldData($pdo, $input);
        break;
    case 'optimize_database':
        handleOptimizeDatabase($pdo);
        break;
    case 'generate_report':
        handleGenerateReport($pdo);
        break;
    case 'factory_reset':
        handleFactoryReset($pdo, $input);
        break;
    case 'delete_user':
        handleDeleteUser($pdo, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleRegister($pdo, $input) {
    $name = sanitizeInput($input['name'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');

    // Enhanced validation
    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Name and phone are required']);
        return;
    }

    if (!validateUsername($name)) {
        echo json_encode(['success' => false, 'message' => 'Username must be 2-50 characters and contain only letters, numbers, spaces, and underscores']);
        return;
    }

    if (!validatePhoneNumber($phone)) {
        echo json_encode(['success' => false, 'message' => 'Phone number must be in format 09XXXXXXXX']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Check if phone number already exists
        $stmt = $pdo->prepare("SELECT id FROM players WHERE phone = ?");
        $stmt->execute([$phone]);
        $existingPhone = $stmt->fetch();
        
        if ($existingPhone) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Phone number already registered. Please login instead.']);
            return;
        }
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM players WHERE username = ?");
        $stmt->execute([$name]);
        $existingUsername = $stmt->fetch();
        
        if ($existingUsername) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Username already taken. Please choose a different name.']);
            return;
        }
        
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO players (phone, username, balance, status, created_at, updated_at) VALUES (?, ?, ?, 'active', NOW(), NOW())");
        $stmt->execute([$phone, $name, WELCOME_BONUS]);
        $userId = $pdo->lastInsertId();
        
        // Log welcome bonus transaction (check if transactions table exists)
        try {
            $stmt = $pdo->prepare("INSERT INTO transactions (player_id, type, amount, balance_before, balance_after, description) VALUES (?, 'deposit', ?, 0, ?, 'Welcome bonus')");
            $stmt->execute([$userId, WELCOME_BONUS, WELCOME_BONUS]);
        } catch (PDOException $e) {
            // Transactions table might not exist, continue without logging
        }
        
        $pdo->commit();
        
        // Log admin activity
        logActivity('player_registration', "New player registered: $name ($phone) with bonus " . WELCOME_BONUS . " ETB");
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Welcome bonus added.',
            'player' => [
                'id' => $userId,
                'username' => $name,
                'phone' => $phone,
                'balance' => WELCOME_BONUS
            ]
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Registration failed: Database error. Please try again.']);
    }
}

function handleLogin($pdo, $input) {
    $name = sanitizeInput($input['name'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');

    // Enhanced validation
    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Name and phone are required']);
        return;
    }

    if (!validateUsername($name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid username format']);
        return;
    }

    if (!validatePhoneNumber($phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
        return;
    }
    
    try {
        // Find player by phone number
        $stmt = $pdo->prepare("SELECT id, username, phone, balance, status, games_played, games_won, total_winnings FROM players WHERE phone = ?");
        $stmt->execute([$phone]);
        $player = $stmt->fetch();
        
        if (!$player) {
            echo json_encode(['success' => false, 'message' => 'Phone number not found. Please register first.']);
            return;
        }
        
        if ($player['status'] === 'banned') {
            echo json_encode(['success' => false, 'message' => 'Your account has been banned. Contact support.']);
            return;
        }
        
        if ($player['status'] === 'suspended') {
            echo json_encode(['success' => false, 'message' => 'Your account is temporarily suspended. Contact support.']);
            return;
        }
        
        // Verify that the name matches the registered username (case-insensitive)
        if (strcasecmp($player['username'], $name) !== 0) {
            echo json_encode(['success' => false, 'message' => 'Name does not match the registered username for this phone number.']);
            return;
        }
        
        // Update last_active timestamp
        try {
            $stmt = $pdo->prepare("UPDATE players SET last_active = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$player['id']]);
        } catch (PDOException $e) {
            // If last_active column doesn't exist, continue without updating
        }

        // Ensure all required fields have default values
        $player['games_played'] = $player['games_played'] ?? 0;
        $player['games_won'] = $player['games_won'] ?? 0;
        $player['total_winnings'] = $player['total_winnings'] ?? 0;
        
        logActivity('player_login', "Player logged in: {$player['username']} ($phone)");
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'player' => [
                'id' => $player['id'],
                'username' => $player['username'],
                'phone' => $player['phone'],
                'balance' => $player['balance'],
                'games_played' => $player['games_played'],
                'games_won' => $player['games_won'],
                'total_winnings' => $player['total_winnings'],
                'status' => $player['status']
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Login failed: Database connection error. Please try again.']);
    }
}

function handleGetGameHistory($pdo, $input) {
    $playerId = $input['player_id'] ?? 0;
    
    if (!$playerId) {
        echo json_encode(['success' => false, 'message' => 'Player ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM game_history WHERE player_id = ? ORDER BY game_date DESC LIMIT 50");
        $stmt->execute([$playerId]);
        $history = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load game history']);
    }
}

function handleUpdateBalance($pdo, $input) {
    $playerId = $input['player_id'] ?? 0;
    $amount = floatval($input['amount'] ?? 0);
    $type = $input['type'] ?? 'adjustment';

    // Enhanced validation
    if (!validatePlayerId($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid player ID']);
        return;
    }

    if ($amount == 0) {
        echo json_encode(['success' => false, 'message' => 'Amount cannot be zero']);
        return;
    }

    if ($amount < -1000000 || $amount > 1000000) {
        echo json_encode(['success' => false, 'message' => 'Amount is out of allowed range']);
        return;
    }

    $allowedTypes = ['deposit', 'withdrawal', 'win', 'loss', 'bet', 'adjustment', 'admin_adjustment'];
    if (!in_array($type, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid transaction type']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get current balance
        $stmt = $pdo->prepare("SELECT balance FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();
        
        if (!$player) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Player not found']);
            return;
        }
        
        $balanceBefore = $player['balance'];
        $balanceAfter = $balanceBefore + $amount;
        
        // Update balance
        $stmt = $pdo->prepare("UPDATE players SET balance = ? WHERE id = ?");
        $stmt->execute([$balanceAfter, $playerId]);
        
        // Log transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (player_id, type, amount, balance_before, balance_after, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$playerId, $type, $amount, $balanceBefore, $balanceAfter, $input['description'] ?? '']);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Balance updated successfully',
            'new_balance' => $balanceAfter
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to update balance']);
    }
}

// Set balance directly (for syncing from frontend) with admin override protection
function handleSetBalance($pdo, $input) {
    $playerId = $input['player_id'] ?? 0;
    $newBalance = floatval($input['balance'] ?? 0);
    $lastKnownBalance = floatval($input['last_known_balance'] ?? 0);

    // Enhanced validation
    if (!validatePlayerId($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid player ID']);
        return;
    }

    if ($newBalance < 0) {
        echo json_encode(['success' => false, 'message' => 'Balance cannot be negative']);
        return;
    }

    if ($newBalance > 1000000) {
        echo json_encode(['success' => false, 'message' => 'Balance is too high']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Get current balance and last admin update
        $stmt = $pdo->prepare("SELECT balance, updated_at FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();

        if (!$player) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Player not found']);
            return;
        }

        $currentDbBalance = floatval($player['balance']);
        $lastUpdated = $player['updated_at'];

        // Check if admin has modified balance recently (within last 5 minutes)
        $adminModifiedRecently = false;
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM transactions
                WHERE player_id = ?
                AND type IN ('admin_deposit', 'admin_withdrawal', 'adjustment')
                AND transaction_date > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ");
            $stmt->execute([$playerId]);
            $adminModifiedRecently = $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Transactions table might not exist
        }

        // If admin modified balance recently, don't override with frontend balance
        if ($adminModifiedRecently) {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Balance was recently modified by admin. Please refresh to get latest balance.',
                'admin_override' => true,
                'current_balance' => $currentDbBalance,
                'attempted_balance' => $newBalance
            ]);
            return;
        }

        // Check for balance conflicts (database balance differs significantly from expected)
        $expectedBalance = $lastKnownBalance;
        $balanceDifference = abs($currentDbBalance - $expectedBalance);

        // If there's a significant difference (more than 1 ETB), it might be an admin change
        if ($balanceDifference > 1.0 && $expectedBalance > 0) {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Balance conflict detected. Database balance differs from expected balance.',
                'balance_conflict' => true,
                'database_balance' => $currentDbBalance,
                'expected_balance' => $expectedBalance,
                'attempted_balance' => $newBalance
            ]);
            return;
        }

        $balanceBefore = $currentDbBalance;

        // Update balance directly
        $stmt = $pdo->prepare("UPDATE players SET balance = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newBalance, $playerId]);

        // Log the balance sync (only if there's a significant change)
        $difference = abs($newBalance - $balanceBefore);
        if ($difference > 0.01) { // Only log if difference is more than 1 cent
            try {
                $stmt = $pdo->prepare("INSERT INTO transactions (player_id, type, amount, balance_before, balance_after, description) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $playerId,
                    'sync',
                    $newBalance - $balanceBefore,
                    $balanceBefore,
                    $newBalance,
                    'Balance sync from frontend'
                ]);
            } catch (PDOException $e) {
                // If transactions table doesn't exist, continue without logging
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Balance synchronized successfully',
            'balance' => $newBalance,
            'previous_balance' => $balanceBefore
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to set balance: ' . $e->getMessage()]);
    }
}

// Debug balance - check what's happening with balance
function handleDebugBalance($pdo, $input) {
    $playerId = $input['player_id'] ?? 0;

    if (!validatePlayerId($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid player ID']);
        return;
    }

    try {
        // Get player data
        $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();

        if (!$player) {
            echo json_encode(['success' => false, 'message' => 'Player not found']);
            return;
        }

        // Get recent transactions
        $transactions = [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE player_id = ? ORDER BY transaction_date DESC LIMIT 10");
            $stmt->execute([$playerId]);
            $transactions = $stmt->fetchAll();
        } catch (PDOException $e) {
            // Transactions table might not exist
        }

        // Get recent games
        $stmt = $pdo->prepare("SELECT * FROM game_history WHERE player_id = ? ORDER BY game_date DESC LIMIT 5");
        $stmt->execute([$playerId]);
        $games = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'debug_info' => [
                'player' => $player,
                'recent_transactions' => $transactions,
                'recent_games' => $games,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Debug failed: ' . $e->getMessage()]);
    }
}

function handleSubmitWithdrawal($pdo, $input) {
    $playerId = $input['player_id'] ?? 0;
    $amount = floatval($input['amount'] ?? 0);
    $accountNumber = sanitizeInput($input['account_number'] ?? '');
    $accountType = sanitizeInput($input['account_type'] ?? 'telebirr');
    
    if (!$playerId || $amount < MIN_WITHDRAWAL || $amount > MAX_WITHDRAWAL) {
        echo json_encode(['success' => false, 'message' => 'Invalid withdrawal amount']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Check player balance
        $stmt = $pdo->prepare("SELECT balance, username, phone FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();
        
        if (!$player || $player['balance'] < $amount) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
            return;
        }
        
        // Create withdrawal request
        $stmt = $pdo->prepare("INSERT INTO withdrawal_requests (player_id, amount, account_number, account_type, account_name, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$playerId, $amount, $accountNumber, $accountType, $player['username']]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to submit withdrawal request']);
    }
}

function handleGetLeaderboard($pdo, $input) {
    try {
        $stmt = $pdo->prepare("
            SELECT username, total_winnings, games_played, games_won 
            FROM players 
            WHERE total_winnings > 0 
            ORDER BY total_winnings DESC 
            LIMIT 100
        ");
        $stmt->execute();
        $leaderboard = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'leaderboard' => $leaderboard
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load leaderboard']);
    }
}

function handleGetLiveBets($pdo, $input) {
    try {
        // Get recent games for live feed
        $stmt = $pdo->prepare("
            SELECT p.username, gh.bet_amount, gh.game_date
            FROM game_history gh
            JOIN players p ON gh.player_id = p.id
            ORDER BY gh.game_date DESC
            LIMIT 20
        ");
        $stmt->execute();
        $liveBets = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'live_bets' => $liveBets
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load live bets']);
    }
}

// Admin Functions
function handleGetDashboardStats($pdo) {
    try {
        // Total users
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM players");
        $stmt->execute();
        $totalUsers = $stmt->fetch()['total_users'];
        
        // Total balance
        $stmt = $pdo->prepare("SELECT SUM(balance) as total_balance FROM players");
        $stmt->execute();
        $totalBalance = $stmt->fetch()['total_balance'] ?? 0;
        
        // Pending withdrawals
        $stmt = $pdo->prepare("SELECT COUNT(*) as pending_withdrawals FROM withdrawal_requests WHERE status = 'pending'");
        $stmt->execute();
        $pendingWithdrawals = $stmt->fetch()['pending_withdrawals'];
        
        // Games today
        $stmt = $pdo->prepare("SELECT COUNT(*) as games_today FROM game_history WHERE DATE(game_date) = CURDATE()");
        $stmt->execute();
        $gamesToday = $stmt->fetch()['games_today'];
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'total_users' => $totalUsers,
                'total_balance' => $totalBalance,
                'pending_withdrawals' => $pendingWithdrawals,
                'games_today' => $gamesToday
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load dashboard stats']);
    }
}

function handleGetAllUsers($pdo) {
    try {
        // First check if last_active column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM players LIKE 'last_active'");
        $hasLastActive = $stmt->rowCount() > 0;

        if ($hasLastActive) {
            $stmt = $pdo->prepare("
                SELECT id, username, phone, balance, games_played, games_won, total_winnings, status, last_active, created_at
                FROM players
                ORDER BY created_at DESC
            ");
        } else {
            $stmt = $pdo->prepare("
                SELECT id, username, phone, balance, games_played, games_won, total_winnings, status, created_at,
                       NULL as last_active
                FROM players
                ORDER BY created_at DESC
            ");
        }

        $stmt->execute();
        $users = $stmt->fetchAll();

        // Ensure all required fields have default values
        foreach ($users as &$user) {
            $user['games_played'] = $user['games_played'] ?? 0;
            $user['games_won'] = $user['games_won'] ?? 0;
            $user['total_winnings'] = $user['total_winnings'] ?? 0;
            $user['status'] = $user['status'] ?? 'active';
            $user['last_active'] = $user['last_active'] ?? null;
        }

        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load users: ' . $e->getMessage()]);
    }
}

function handleGetUser($pdo, $input) {
    $userId = $input['user_id'] ?? 0;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load user']);
    }
}

function handleUpdateUser($pdo, $input) {
    $userId = $input['user_id'] ?? 0;
    $username = sanitizeInput($input['username'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');
    $balance = floatval($input['balance'] ?? 0);
    $status = sanitizeInput($input['status'] ?? 'active');
    $reason = sanitizeInput($input['reason'] ?? '');
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get current user data
        $stmt = $pdo->prepare("SELECT balance FROM players WHERE id = ?");
        $stmt->execute([$userId]);
        $currentUser = $stmt->fetch();
        
        if (!$currentUser) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        // Update user
        $stmt = $pdo->prepare("UPDATE players SET username = ?, phone = ?, balance = ?, status = ? WHERE id = ?");
        $stmt->execute([$username, $phone, $balance, $status, $userId]);
        
        // Log balance change if different
        if ($balance != $currentUser['balance']) {
            $balanceChange = $balance - $currentUser['balance'];
            $stmt = $pdo->prepare("INSERT INTO transactions (player_id, type, amount, balance_before, balance_after, description) VALUES (?, 'adjustment', ?, ?, ?, ?)");
            $stmt->execute([$userId, $balanceChange, $currentUser['balance'], $balance, $reason]);
        }
        
        // Log admin activity
        logActivity('admin_user_update', "Admin updated user $userId: $reason");
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
}

function handleUpdateUserStatus($pdo, $input) {
    $userId = $input['user_id'] ?? 0;
    $status = sanitizeInput($input['status'] ?? 'active');
    $reason = sanitizeInput($input['reason'] ?? '');
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE players SET status = ? WHERE id = ?");
        $stmt->execute([$status, $userId]);
        
        // Log admin activity
        logActivity('admin_user_status', "Admin changed user $userId status to $status: $reason");
        
        echo json_encode([
            'success' => true,
            'message' => 'User status updated successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
    }
}

function handleGetWithdrawalRequests($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT wr.*, p.username, p.phone
            FROM withdrawal_requests wr
            JOIN players p ON wr.player_id = p.id
            ORDER BY wr.created_at DESC
        ");
        $stmt->execute();
        $requests = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'requests' => $requests
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load withdrawal requests']);
    }
}

function handleGetWithdrawal($pdo, $input) {
    $withdrawalId = $input['withdrawal_id'] ?? 0;
    
    if (!$withdrawalId) {
        echo json_encode(['success' => false, 'message' => 'Withdrawal ID required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT wr.*, p.username, p.phone
            FROM withdrawal_requests wr
            JOIN players p ON wr.player_id = p.id
            WHERE wr.id = ?
        ");
        $stmt->execute([$withdrawalId]);
        $withdrawal = $stmt->fetch();
        
        if (!$withdrawal) {
            echo json_encode(['success' => false, 'message' => 'Withdrawal request not found']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'withdrawal' => $withdrawal
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load withdrawal request']);
    }
}

function handleProcessWithdrawal($pdo, $input) {
    $withdrawalId = $input['withdrawal_id'] ?? 0;
    $actionType = $input['action_type'] ?? '';
    $notes = sanitizeInput($input['notes'] ?? '');
    
    if (!$withdrawalId || !in_array($actionType, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get withdrawal request
        $stmt = $pdo->prepare("
            SELECT wr.*, p.username, p.balance
            FROM withdrawal_requests wr
            JOIN players p ON wr.player_id = p.id
            WHERE wr.id = ? AND wr.status = 'pending'
        ");
        $stmt->execute([$withdrawalId]);
        $withdrawal = $stmt->fetch();
        
        if (!$withdrawal) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Withdrawal request not found or already processed']);
            return;
        }
        
        $newStatus = $actionType === 'approve' ? 'approved' : 'rejected';
        
        // Update withdrawal status
        $stmt = $pdo->prepare("UPDATE withdrawal_requests SET status = ?, admin_notes = ?, processed_at = NOW() WHERE id = ?");
        $stmt->execute([$newStatus, $notes, $withdrawalId]);
        
        if ($actionType === 'approve') {
            // Deduct balance from player
            $newBalance = $withdrawal['balance'] - $withdrawal['amount'];
            $stmt = $pdo->prepare("UPDATE players SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $withdrawal['player_id']]);
            
            // Log transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (player_id, type, amount, balance_before, balance_after, description) VALUES (?, 'withdrawal', ?, ?, ?, ?)");
            $stmt->execute([$withdrawal['player_id'], -$withdrawal['amount'], $withdrawal['balance'], $newBalance, 'Withdrawal approved']);
        }
        
        // Log admin activity
        logActivity('admin_withdrawal', "Admin $actionType withdrawal $withdrawalId: $notes");
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Withdrawal $actionType successfully"
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to process withdrawal']);
    }
}

function handleGetAllTransactions($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT t.*, p.username
            FROM transactions t
            JOIN players p ON t.player_id = p.id
            ORDER BY t.transaction_date DESC
            LIMIT 1000
        ");
        $stmt->execute();
        $transactions = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load transactions']);
    }
}

function handleGetAllGames($pdo) {
    try {
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES LIKE 'game_history'");
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => true, 'games' => []]);
            return;
        }

        $stmt = $pdo->query("SHOW TABLES LIKE 'players'");
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => true, 'games' => []]);
            return;
        }

        $stmt = $pdo->prepare("
            SELECT gh.*, p.username, p.phone
            FROM game_history gh
            JOIN players p ON gh.player_id = p.id
            ORDER BY gh.game_date DESC
            LIMIT 1000
        ");
        $stmt->execute();
        $games = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'games' => $games
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load games: ' . $e->getMessage()]);
    }
}

function handleGetGameHistoryAdmin($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                gh.*,
                COALESCE(p.username, 'Unknown Player') as username,
                COALESCE(p.phone, 'N/A') as phone
            FROM game_history gh
            LEFT JOIN players p ON gh.player_id = p.id
            ORDER BY gh.game_date DESC
            LIMIT 500
        ");
        $stmt->execute();
        $games = $stmt->fetchAll();

        // Ensure all games have required fields
        foreach ($games as &$game) {
            $game['selected_numbers'] = $game['selected_numbers'] ?? '[]';
            $game['drawn_numbers'] = $game['drawn_numbers'] ?? '[]';
            $game['bet_amount'] = floatval($game['bet_amount'] ?? 0);
            $game['win_amount'] = floatval($game['win_amount'] ?? 0);
            $game['match_count'] = intval($game['match_count'] ?? 0);
        }

        echo json_encode([
            'success' => true,
            'games' => $games,
            'count' => count($games)
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load game history: ' . $e->getMessage()]);
    }
}

// Settings Management Functions
function handleGetSettings($pdo) {
    try {
        // Check if settings table exists, if not create it
        $stmt = $pdo->query("SHOW TABLES LIKE 'system_settings'");
        if ($stmt->rowCount() === 0) {
            // Create settings table
            $pdo->exec("
                CREATE TABLE system_settings (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    setting_key VARCHAR(50) UNIQUE NOT NULL,
                    setting_value TEXT NOT NULL,
                    description TEXT,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");

            // Insert default settings
            $defaultSettings = [
                ['min_bet', '5.00', 'Minimum bet amount in ETB'],
                ['max_bet', '1000.00', 'Maximum bet amount in ETB'],
                ['welcome_bonus', '25.00', 'Welcome bonus for new users in ETB'],
                ['min_withdrawal', '20.00', 'Minimum withdrawal amount in ETB'],
                ['max_numbers', '10', 'Maximum numbers a player can select'],
                ['min_numbers', '1', 'Minimum numbers a player can select'],
                ['game_enabled', '1', 'Whether the game is enabled (1=yes, 0=no)']
            ];

            $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
            foreach ($defaultSettings as $setting) {
                $stmt->execute($setting);
            }
        }

        // Get all settings
        $stmt = $pdo->query("SELECT setting_key, setting_value, description FROM system_settings");
        $settings = $stmt->fetchAll();

        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }

        echo json_encode([
            'success' => true,
            'settings' => $settingsArray
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load settings: ' . $e->getMessage()]);
    }
}

function handleSaveSettings($pdo, $input) {
    try {
        $settings = $input['settings'] ?? [];

        if (empty($settings)) {
            echo json_encode(['success' => false, 'message' => 'No settings provided']);
            return;
        }

        // Validate settings
        $validSettings = ['min_bet', 'max_bet', 'welcome_bonus', 'min_withdrawal', 'max_numbers', 'min_numbers', 'game_enabled'];

        $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        $updatedCount = 0;
        foreach ($settings as $key => $value) {
            if (in_array($key, $validSettings)) {
                $stmt->execute([$key, $value]);
                $updatedCount++;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Updated $updatedCount settings successfully"
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to save settings: ' . $e->getMessage()]);
    }
}

function handleBackupDatabase($pdo) {
    try {
        // Get database name from config
        $dbname = 'kenocobq_keno_db'; // You might want to get this from config

        // Create backup directory if it doesn't exist
        $backupDir = 'backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = $backupDir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';

        // Get all table names
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $backup = "-- 251KENO Database Backup\n";
        $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            // Get table structure
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch();
            $backup .= "-- Table structure for `$table`\n";
            $backup .= "DROP TABLE IF EXISTS `$table`;\n";
            $backup .= $row[1] . ";\n\n";

            // Get table data
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll();

            if (!empty($rows)) {
                $backup .= "-- Data for table `$table`\n";
                foreach ($rows as $row) {
                    $values = array_map(function($value) use ($pdo) {
                        return $value === null ? 'NULL' : $pdo->quote($value);
                    }, array_values($row));
                    $backup .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                }
                $backup .= "\n";
            }
        }

        file_put_contents($filename, $backup);

        echo json_encode([
            'success' => true,
            'message' => 'Database backup created successfully',
            'filename' => $filename,
            'size' => filesize($filename)
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()]);
    }
}

function handleCleanOldData($pdo, $input) {
    try {
        $days = intval($input['days'] ?? 30);

        if ($days < 7) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete data newer than 7 days']);
            return;
        }

        // Clean old game history
        $stmt = $pdo->prepare("DELETE FROM game_history WHERE game_date < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$days]);
        $gamesDeleted = $stmt->rowCount();

        // Clean old transactions if table exists
        $transactionsDeleted = 0;
        try {
            $stmt = $pdo->prepare("DELETE FROM transactions WHERE transaction_date < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            $transactionsDeleted = $stmt->rowCount();
        } catch (PDOException $e) {
            // Transactions table doesn't exist
        }

        echo json_encode([
            'success' => true,
            'message' => "Cleaned old data successfully",
            'games_deleted' => $gamesDeleted,
            'transactions_deleted' => $transactionsDeleted,
            'days' => $days
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to clean data: ' . $e->getMessage()]);
    }
}

function handleOptimizeDatabase($pdo) {
    try {
        // Get all table names
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $optimizedTables = [];
        foreach ($tables as $table) {
            $stmt = $pdo->query("OPTIMIZE TABLE `$table`");
            $result = $stmt->fetch();
            $optimizedTables[] = [
                'table' => $table,
                'status' => $result['Msg_text'] ?? 'OK'
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Database optimization completed',
            'tables' => $optimizedTables
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Optimization failed: ' . $e->getMessage()]);
    }
}

function handleGenerateReport($pdo) {
    try {
        // Generate comprehensive system report
        $report = [];

        // Basic stats
        $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM players");
        $report['total_users'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) as active_users FROM players WHERE games_played > 0");
        $report['active_users'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) as total_games FROM game_history");
        $report['total_games'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COALESCE(SUM(balance), 0) as total_balance FROM players");
        $report['total_balance'] = floatval($stmt->fetchColumn());

        $stmt = $pdo->query("SELECT COALESCE(SUM(bet_amount), 0) as total_bets FROM game_history");
        $report['total_bets'] = floatval($stmt->fetchColumn());

        $stmt = $pdo->query("SELECT COALESCE(SUM(win_amount), 0) as total_wins FROM game_history");
        $report['total_wins'] = floatval($stmt->fetchColumn());

        $report['net_revenue'] = $report['total_bets'] - $report['total_wins'];

        // Today's stats
        $stmt = $pdo->query("SELECT COUNT(*) as games_today FROM game_history WHERE DATE(game_date) = CURDATE()");
        $report['games_today'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COALESCE(SUM(bet_amount), 0) - COALESCE(SUM(win_amount), 0) as revenue_today FROM game_history WHERE DATE(game_date) = CURDATE()");
        $report['revenue_today'] = floatval($stmt->fetchColumn());

        // Top players
        $stmt = $pdo->query("SELECT username, total_winnings, games_played FROM players WHERE games_played > 0 ORDER BY total_winnings DESC LIMIT 5");
        $report['top_players'] = $stmt->fetchAll();

        // Recent activity
        $stmt = $pdo->query("SELECT COUNT(*) as games_last_7_days FROM game_history WHERE game_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $report['games_last_7_days'] = $stmt->fetchColumn();

        $report['generated_at'] = date('Y-m-d H:i:s');

        echo json_encode([
            'success' => true,
            'report' => $report
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Report generation failed: ' . $e->getMessage()]);
    }
}

function handleFactoryReset($pdo, $input) {
    try {
        // Verify confirmation code
        $confirmationCode = $input['confirmation_code'] ?? '';
        $expectedCode = 'RESET251KENO';

        if ($confirmationCode !== $expectedCode) {
            echo json_encode(['success' => false, 'message' => 'Invalid confirmation code. Factory reset cancelled.']);
            return;
        }

        // Create backup before reset
        $backupDir = 'backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupFile = $backupDir . '/factory_reset_backup_' . date('Y-m-d_H-i-s') . '.sql';

        // Get all table names
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $backup = "-- 251KENO Factory Reset Backup\n";
        $backup .= "-- Created before factory reset on: " . date('Y-m-d H:i:s') . "\n\n";

        // Backup all data
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch();
            $backup .= "-- Table structure for `$table`\n";
            $backup .= "DROP TABLE IF EXISTS `$table`;\n";
            $backup .= $row[1] . ";\n\n";

            $stmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll();

            if (!empty($rows)) {
                $backup .= "-- Data for table `$table`\n";
                foreach ($rows as $row) {
                    $values = array_map(function($value) use ($pdo) {
                        return $value === null ? 'NULL' : $pdo->quote($value);
                    }, array_values($row));
                    $backup .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                }
                $backup .= "\n";
            }
        }

        file_put_contents($backupFile, $backup);

        // Start factory reset
        $resetResults = [];

        // 1. Clear all game history
        $stmt = $pdo->query("DELETE FROM game_history");
        $resetResults['games_deleted'] = $stmt->rowCount();

        // 2. Clear all transactions (if table exists)
        try {
            $stmt = $pdo->query("DELETE FROM transactions");
            $resetResults['transactions_deleted'] = $stmt->rowCount();
        } catch (PDOException $e) {
            $resetResults['transactions_deleted'] = 0;
        }

        // 3. Reset all players (keep admin, reset others)
        $stmt = $pdo->prepare("DELETE FROM players WHERE username != 'admin'");
        $stmt->execute();
        $resetResults['users_deleted'] = $stmt->rowCount();

        // 4. Reset admin user
        $stmt = $pdo->prepare("
            UPDATE players
            SET balance = 10000.00,
                games_played = 0,
                games_won = 0,
                total_winnings = 0.00,
                total_bets = 0.00,
                status = 'active',
                last_active = NOW(),
                updated_at = NOW()
            WHERE username = 'admin'
        ");
        $stmt->execute();
        $resetResults['admin_reset'] = $stmt->rowCount() > 0;

        // 5. Reset system settings to defaults
        try {
            $stmt = $pdo->query("DROP TABLE IF EXISTS system_settings");
            $resetResults['settings_reset'] = true;
        } catch (PDOException $e) {
            $resetResults['settings_reset'] = false;
        }

        // 6. Reset auto-increment counters
        $pdo->exec("ALTER TABLE players AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE game_history AUTO_INCREMENT = 1");
        try {
            $pdo->exec("ALTER TABLE transactions AUTO_INCREMENT = 1");
        } catch (PDOException $e) {
            // Transactions table might not exist
        }

        // 7. Optimize all tables after reset
        foreach ($tables as $table) {
            try {
                $pdo->exec("OPTIMIZE TABLE `$table`");
            } catch (PDOException $e) {
                // Continue if optimization fails
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Factory reset completed successfully',
            'backup_file' => $backupFile,
            'backup_size' => filesize($backupFile),
            'reset_results' => $resetResults,
            'reset_timestamp' => date('Y-m-d H:i:s')
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Factory reset failed: ' . $e->getMessage()]);
    }
}

// Load game data for a specific player
function handleLoadGame($pdo, $input) {
    $playerId = $input['player_id'] ?? '';

    if (empty($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Player ID is required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, phone, balance, total_winnings, games_played, games_won FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();

        if (!$player) {
            echo json_encode(['success' => false, 'message' => 'Player not found']);
            return;
        }

        echo json_encode([
            'success' => true,
            'player' => $player
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load player data']);
    }
}

// Enhanced input validation functions
function validatePlayerId($playerId) {
    return is_numeric($playerId) && $playerId > 0;
}

function validateBetAmount($amount) {
    $amount = floatval($amount);
    return $amount >= MIN_BET && $amount <= MAX_BET;
}

function validateNumbers($numbers, $min = 1, $max = 10) {
    if (!is_array($numbers)) return false;
    if (count($numbers) < $min || count($numbers) > $max) return false;

    foreach ($numbers as $num) {
        if (!is_numeric($num) || $num < 1 || $num > 80) return false;
    }

    return true;
}

function validatePhoneNumber($phone) {
    return preg_match('/^09\d{8}$/', $phone);
}

function validateUsername($username) {
    return strlen($username) >= 2 && strlen($username) <= 50 && preg_match('/^[a-zA-Z0-9_\s]+$/', $username);
}

// Save game history
function handleSaveHistory($pdo, $input) {
    $playerId = $input['player_id'] ?? '';
    $selectedNumbers = $input['selected_numbers'] ?? [];
    $drawnNumbers = $input['drawn_numbers'] ?? [];
    $betAmount = floatval($input['bet_amount'] ?? 0);
    $winAmount = floatval($input['win_amount'] ?? 0);
    $matchCount = intval($input['match_count'] ?? 0);

    // Enhanced validation
    if (!validatePlayerId($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid player ID']);
        return;
    }

    if (!validateNumbers($selectedNumbers, 1, 10)) {
        echo json_encode(['success' => false, 'message' => 'Invalid selected numbers']);
        return;
    }

    if (!validateNumbers($drawnNumbers, 20, 20)) {
        echo json_encode(['success' => false, 'message' => 'Invalid drawn numbers']);
        return;
    }

    if (!validateBetAmount($betAmount)) {
        echo json_encode(['success' => false, 'message' => 'Invalid bet amount']);
        return;
    }

    if ($winAmount < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid win amount']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Save game history
        $stmt = $pdo->prepare("
            INSERT INTO game_history (player_id, selected_numbers, drawn_numbers, bet_amount, win_amount, match_count)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $playerId,
            json_encode($selectedNumbers),
            json_encode($drawnNumbers),
            $betAmount,
            $winAmount,
            $matchCount
        ]);

        // Update player statistics (don't modify balance here as frontend handles it)
        $stmt = $pdo->prepare("
            UPDATE players
            SET games_played = games_played + 1,
                games_won = games_won + ?,
                total_bets = total_bets + ?,
                total_winnings = total_winnings + ?
            WHERE id = ?
        ");
        $stmt->execute([
            $winAmount > 0 ? 1 : 0,
            $betAmount,
            $winAmount,
            $playerId
        ]);

        // Get current balance for response
        $stmt = $pdo->prepare("SELECT balance FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $currentBalance = $stmt->fetchColumn();

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Game saved successfully',
            'new_balance' => $currentBalance
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to save game data']);
    }
}

// Get game history for a specific player
function handleGetHistory($pdo, $input) {
    $playerId = $input['player_id'] ?? '';
    $limit = intval($input['limit'] ?? 0); // 0 means no limit

    if (empty($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Player ID is required']);
        return;
    }

    try {
        if ($limit > 0) {
            $stmt = $pdo->prepare("
                SELECT id, selected_numbers, drawn_numbers, bet_amount, win_amount, match_count, game_date
                FROM game_history
                WHERE player_id = ?
                ORDER BY game_date DESC
                LIMIT ?
            ");
            $stmt->execute([$playerId, $limit]);
        } else {
            // Get all history (lifetime)
            $stmt = $pdo->prepare("
                SELECT id, selected_numbers, drawn_numbers, bet_amount, win_amount, match_count, game_date
                FROM game_history
                WHERE player_id = ?
                ORDER BY game_date DESC
            ");
            $stmt->execute([$playerId]);
        }
        $history = $stmt->fetchAll();

        // Decode JSON fields
        foreach ($history as &$game) {
            $game['selected_numbers'] = json_decode($game['selected_numbers'], true);
            $game['drawn_numbers'] = json_decode($game['drawn_numbers'], true);
        }

        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load game history']);
    }
}

// Delete user function
function handleDeleteUser($pdo, $input) {
    $userId = $input['user_id'] ?? 0;

    if (!validatePlayerId($userId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Check if user exists
        $stmt = $pdo->prepare("SELECT username FROM players WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        // Delete user (cascade will handle related records)
        $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();

        // Log admin activity
        logActivity('user_deletion', "User deleted: {$user['username']} (ID: $userId)");

        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
}

// ==================== ROUND-BASED GAME FUNCTIONS ====================

function handleGetCurrentRound($pdo) {
    try {
        // Get or create current active round
        $stmt = $pdo->prepare("
            SELECT * FROM game_rounds
            WHERE status = 'active'
            ORDER BY round_number DESC
            LIMIT 1
        ");
        $stmt->execute();
        $round = $stmt->fetch();

        if (!$round) {
            // Create new round
            $roundNumber = getNextRoundNumber($pdo);
            $stmt = $pdo->prepare("
                INSERT INTO game_rounds (round_number, start_time, status)
                VALUES (?, NOW(), 'active')
            ");
            $stmt->execute([$roundNumber]);

            $roundId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM game_rounds WHERE id = ?");
            $stmt->execute([$roundId]);
            $round = $stmt->fetch();
        }

        // Calculate time remaining (60 seconds from start)
        $startTime = strtotime($round['start_time']);
        $currentTime = time();
        $elapsed = $currentTime - $startTime;
        $timeRemaining = max(0, 60 - $elapsed);

        // If time is up, mark round for processing
        if ($timeRemaining <= 0 && $round['status'] === 'active') {
            $stmt = $pdo->prepare("UPDATE game_rounds SET status = 'drawing' WHERE id = ?");
            $stmt->execute([$round['id']]);
            $round['status'] = 'drawing';
        }

        echo json_encode([
            'success' => true,
            'round' => [
                'id' => $round['id'],
                'round_number' => $round['round_number'],
                'status' => $round['status'],
                'time_remaining' => $timeRemaining,
                'start_time' => $round['start_time'],
                'drawn_numbers' => $round['drawn_numbers'] ? json_decode($round['drawn_numbers']) : null
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to get current round: ' . $e->getMessage()]);
    }
}

function handlePlaceTicket($pdo, $input) {
    $playerId = $input['player_id'] ?? null;
    $selectedNumbers = $input['selected_numbers'] ?? [];
    $betAmount = $input['bet_amount'] ?? 0;

    if (!$playerId || empty($selectedNumbers) || $betAmount < MIN_BET) {
        echo json_encode(['success' => false, 'message' => 'Invalid ticket data']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Get current active round
        $stmt = $pdo->prepare("
            SELECT * FROM game_rounds
            WHERE status = 'active'
            ORDER BY round_number DESC
            LIMIT 1
        ");
        $stmt->execute();
        $round = $stmt->fetch();

        if (!$round) {
            echo json_encode(['success' => false, 'message' => 'No active round available']);
            return;
        }

        // Check if round is still accepting bets (within 60 seconds)
        $startTime = strtotime($round['start_time']);
        $currentTime = time();
        $elapsed = $currentTime - $startTime;

        if ($elapsed >= 60) {
            echo json_encode(['success' => false, 'message' => 'Round betting has closed']);
            return;
        }

        // Check player balance
        $stmt = $pdo->prepare("SELECT balance FROM players WHERE id = ?");
        $stmt->execute([$playerId]);
        $player = $stmt->fetch();

        if (!$player || $player['balance'] < $betAmount) {
            echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
            return;
        }

        // Deduct bet amount from player balance
        $stmt = $pdo->prepare("UPDATE players SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$betAmount, $playerId]);

        // Create ticket
        $stmt = $pdo->prepare("
            INSERT INTO game_tickets (player_id, round_id, selected_numbers, bet_amount, status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$playerId, $round['id'], json_encode($selectedNumbers), $betAmount]);

        $ticketId = $pdo->lastInsertId();

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'ticket_id' => $ticketId,
            'message' => 'Ticket placed successfully'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to place ticket: ' . $e->getMessage()]);
    }
}

function handleGetPlayerTickets($pdo, $input) {
    $playerId = $input['player_id'] ?? null;
    $roundId = $input['round_id'] ?? null;

    if (!$playerId) {
        echo json_encode(['success' => false, 'message' => 'Player ID required']);
        return;
    }

    try {
        $query = "
            SELECT t.*, r.round_number, r.status as round_status, r.drawn_numbers
            FROM game_tickets t
            JOIN game_rounds r ON t.round_id = r.id
            WHERE t.player_id = ?
        ";
        $params = [$playerId];

        if ($roundId) {
            $query .= " AND t.round_id = ?";
            $params[] = $roundId;
        }

        $query .= " ORDER BY t.created_at DESC LIMIT 50";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        // Process tickets data
        foreach ($tickets as &$ticket) {
            $ticket['selected_numbers'] = json_decode($ticket['selected_numbers']);
            if ($ticket['drawn_numbers']) {
                $ticket['drawn_numbers'] = json_decode($ticket['drawn_numbers']);
            }
        }

        echo json_encode([
            'success' => true,
            'tickets' => $tickets
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to get tickets: ' . $e->getMessage()]);
    }
}

function handleGetRoundTickets($pdo, $input) {
    $roundId = $input['round_id'] ?? null;

    if (!$roundId) {
        echo json_encode(['success' => false, 'message' => 'Round ID required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT t.*, p.username
            FROM game_tickets t
            JOIN players p ON t.player_id = p.id
            WHERE t.round_id = ?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$roundId]);
        $tickets = $stmt->fetchAll();

        // Process tickets data
        foreach ($tickets as &$ticket) {
            $ticket['selected_numbers'] = json_decode($ticket['selected_numbers']);
        }

        echo json_encode([
            'success' => true,
            'tickets' => $tickets
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to get round tickets: ' . $e->getMessage()]);
    }
}

function handleProcessRound($pdo, $input) {
    $roundId = $input['round_id'] ?? null;

    if (!$roundId) {
        echo json_encode(['success' => false, 'message' => 'Round ID required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Get round details
        $stmt = $pdo->prepare("SELECT * FROM game_rounds WHERE id = ? AND status = 'drawing'");
        $stmt->execute([$roundId]);
        $round = $stmt->fetch();

        if (!$round) {
            echo json_encode(['success' => false, 'message' => 'Round not found or not ready for processing']);
            return;
        }

        // Generate winning numbers (20 numbers from 1-80)
        $allNumbers = range(1, 80);
        shuffle($allNumbers);
        $drawnNumbers = array_slice($allNumbers, 0, 20);
        sort($drawnNumbers);

        // Update round with drawn numbers
        $stmt = $pdo->prepare("
            UPDATE game_rounds
            SET drawn_numbers = ?, status = 'completed', end_time = NOW()
            WHERE id = ?
        ");
        $stmt->execute([json_encode($drawnNumbers), $roundId]);

        // Get all tickets for this round
        $stmt = $pdo->prepare("SELECT * FROM game_tickets WHERE round_id = ? AND status = 'pending'");
        $stmt->execute([$roundId]);
        $tickets = $stmt->fetchAll();

        $totalWinnings = 0;

        // Process each ticket
        foreach ($tickets as $ticket) {
            $selectedNumbers = json_decode($ticket['selected_numbers']);
            $matches = array_intersect($selectedNumbers, $drawnNumbers);
            $matchCount = count($matches);

            // Calculate winnings using existing payout system
            $winAmount = calculateKenoWinAmount($matchCount, count($selectedNumbers), $ticket['bet_amount']);

            $status = $winAmount > 0 ? 'won' : 'lost';

            // Update ticket
            $stmt = $pdo->prepare("
                UPDATE game_tickets
                SET match_count = ?, win_amount = ?, status = ?, processed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$matchCount, $winAmount, $status, $ticket['id']]);

            // Add winnings to player balance
            if ($winAmount > 0) {
                $stmt = $pdo->prepare("UPDATE players SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$winAmount, $ticket['player_id']]);
                $totalWinnings += $winAmount;
            }

            // Save to game history
            $stmt = $pdo->prepare("
                INSERT INTO game_history (player_id, round_id, ticket_id, selected_numbers, drawn_numbers, bet_amount, win_amount, match_count)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $ticket['player_id'],
                $roundId,
                $ticket['id'],
                json_encode($selectedNumbers),
                json_encode($drawnNumbers),
                $ticket['bet_amount'],
                $winAmount,
                $matchCount
            ]);
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'drawn_numbers' => $drawnNumbers,
            'tickets_processed' => count($tickets),
            'total_winnings' => $totalWinnings
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to process round: ' . $e->getMessage()]);
    }
}

function handleGetRoundResults($pdo, $input) {
    $roundId = $input['round_id'] ?? null;

    if (!$roundId) {
        echo json_encode(['success' => false, 'message' => 'Round ID required']);
        return;
    }

    try {
        // Get round details
        $stmt = $pdo->prepare("SELECT * FROM game_rounds WHERE id = ?");
        $stmt->execute([$roundId]);
        $round = $stmt->fetch();

        if (!$round) {
            echo json_encode(['success' => false, 'message' => 'Round not found']);
            return;
        }

        // Get all tickets for this round
        $stmt = $pdo->prepare("
            SELECT t.*, p.username
            FROM game_tickets t
            JOIN players p ON t.player_id = p.id
            WHERE t.round_id = ?
            ORDER BY t.win_amount DESC, t.created_at ASC
        ");
        $stmt->execute([$roundId]);
        $tickets = $stmt->fetchAll();

        // Process tickets data
        foreach ($tickets as &$ticket) {
            $ticket['selected_numbers'] = json_decode($ticket['selected_numbers']);
        }

        echo json_encode([
            'success' => true,
            'round' => [
                'id' => $round['id'],
                'round_number' => $round['round_number'],
                'status' => $round['status'],
                'drawn_numbers' => $round['drawn_numbers'] ? json_decode($round['drawn_numbers']) : null,
                'start_time' => $round['start_time'],
                'end_time' => $round['end_time']
            ],
            'tickets' => $tickets
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to get round results: ' . $e->getMessage()]);
    }
}

// Helper functions
function getNextRoundNumber($pdo) {
    $stmt = $pdo->query("SELECT MAX(round_number) as max_round FROM game_rounds");
    $result = $stmt->fetch();
    return ($result['max_round'] ?? 0) + 1;
}

function calculateKenoWinAmount($matches, $selectedCount, $betAmount) {
    // Keno payout table - same as in frontend
    $payoutTable = [
        1 => [null, 3],
        2 => [null, null, 10],
        3 => [null, null, 2, 20],
        4 => [null, null, null, 5, 50],
        5 => [null, null, null, 2, 15, 100],
        6 => [null, null, null, null, 5, 25, 200],
        7 => [null, null, null, null, 2, 10, 50, 500],
        8 => [null, null, null, null, null, 5, 20, 100, 1000],
        9 => [null, null, null, null, null, 2, 10, 50, 200, 2000],
        10 => [null, null, null, null, null, null, 5, 25, 100, 500, 5000]
    ];

    if (!isset($payoutTable[$selectedCount]) || !isset($payoutTable[$selectedCount][$matches])) {
        return 0;
    }

    $multiplier = $payoutTable[$selectedCount][$matches];
    return $multiplier ? $betAmount * $multiplier : 0;
}
