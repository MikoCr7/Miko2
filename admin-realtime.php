<?php
// Real-time admin data stream using Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Cache-Control');

require_once 'config.php';

// Function to send SSE data
function sendSSE($data, $event = 'message') {
    echo "event: $event\n";
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Function to get dashboard stats
function getDashboardStats($pdo) {
    try {
        $stats = [];

        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) FROM players");
        $stats['total_users'] = intval($stmt->fetchColumn());

        // Active users (played in last 24 hours)
        $stmt = $pdo->query("SELECT COUNT(DISTINCT player_id) FROM game_history WHERE game_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stats['active_users'] = intval($stmt->fetchColumn());

        // Total balance
        $stmt = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM players");
        $stats['total_balance'] = floatval($stmt->fetchColumn());

        // Today's revenue (total bets - total wins)
        $stmt = $pdo->query("SELECT COALESCE(SUM(bet_amount), 0) - COALESCE(SUM(win_amount), 0) as revenue FROM game_history WHERE DATE(game_date) = CURDATE()");
        $stats['today_revenue'] = floatval($stmt->fetchColumn());

        // Games played today
        $stmt = $pdo->query("SELECT COUNT(*) FROM game_history WHERE DATE(game_date) = CURDATE()");
        $stats['games_today'] = intval($stmt->fetchColumn());

        // Pending withdrawals
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM withdrawal_requests WHERE status = 'pending'");
            $stats['pending_withdrawals'] = intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            $stats['pending_withdrawals'] = 0;
        }

        return $stats;
    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        return [
            'total_users' => 0,
            'active_users' => 0,
            'total_balance' => 0,
            'today_revenue' => 0,
            'games_today' => 0,
            'pending_withdrawals' => 0
        ];
    }
}

// Function to get recent activities
function getRecentActivities($pdo, $lastId = 0) {
    try {
        $activities = [];

        // Recent games - get latest games regardless of lastId for initial load
        $stmt = $pdo->prepare("
            SELECT gh.id, COALESCE(p.username, 'Unknown Player') as username,
                   gh.bet_amount, gh.win_amount, gh.game_date, 'game' as type
            FROM game_history gh
            LEFT JOIN players p ON gh.player_id = p.id
            ORDER BY gh.game_date DESC
            LIMIT 15
        ");
        $stmt->execute();
        $games = $stmt->fetchAll();

        foreach ($games as $game) {
            $isWin = floatval($game['win_amount']) > 0;
            $activities[] = [
                'id' => intval($game['id']),
                'type' => 'game',
                'message' => "{$game['username']} played Keno - Bet: " . number_format($game['bet_amount'], 2) . " ETB" .
                           ($isWin ? " - Won: " . number_format($game['win_amount'], 2) . " ETB" : " - Lost"),
                'timestamp' => $game['game_date'],
                'data' => $game
            ];
        }

        // Try to get transactions if table exists
        try {
            $stmt = $pdo->prepare("
                SELECT t.id, COALESCE(p.username, 'Unknown Player') as username,
                       t.type, t.amount, t.transaction_date
                FROM transactions t
                LEFT JOIN players p ON t.player_id = p.id
                ORDER BY t.transaction_date DESC
                LIMIT 5
            ");
            $stmt->execute();
            $transactions = $stmt->fetchAll();

            foreach ($transactions as $trans) {
                $activities[] = [
                    'id' => 'trans_' . $trans['id'],
                    'type' => 'transaction',
                    'message' => "{$trans['username']} {$trans['type']}: " . number_format($trans['amount'], 2) . " ETB",
                    'timestamp' => $trans['transaction_date'],
                    'data' => $trans
                ];
            }
        } catch (PDOException $e) {
            // Transactions table doesn't exist, skip
        }

        // Sort by timestamp
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($activities, 0, 10);
    } catch (PDOException $e) {
        error_log("Activities error: " . $e->getMessage());
        return [];
    }
}

// Main SSE loop
$pdo = getDBConnection();
if (!$pdo) {
    sendSSE(['error' => 'Database connection failed'], 'error');
    exit;
}

$lastStatsTime = 0;
$lastActivityId = 0;

while (true) {
    $currentTime = time();
    
    // Send dashboard stats every 5 seconds
    if ($currentTime - $lastStatsTime >= 5) {
        $stats = getDashboardStats($pdo);
        if ($stats) {
            sendSSE($stats, 'stats');
        }
        $lastStatsTime = $currentTime;
    }
    
    // Send new activities every 2 seconds
    $activities = getRecentActivities($pdo, $lastActivityId);
    if (!empty($activities)) {
        sendSSE($activities, 'activities');
        $lastActivityId = max(array_column($activities, 'id'));
    }
    
    // Send heartbeat every 30 seconds
    if ($currentTime % 30 === 0) {
        sendSSE(['timestamp' => $currentTime], 'heartbeat');
    }
    
    sleep(2);
    
    // Check if client disconnected
    if (connection_aborted()) {
        break;
    }
}
?>
