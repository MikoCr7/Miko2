<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }

    switch ($action) {
        case 'dashboard_stats':
            handleDashboardStats($pdo);
            break;
        case 'get_games':
            handleGetGames($pdo);
            break;
        case 'get_transactions':
            handleGetTransactions($pdo);
            break;
        case 'get_leaderboard':
            handleGetLeaderboard($pdo);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleDashboardStats($pdo) {
    try {
        $stats = [];
        
        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) FROM players");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Active users (last 24 hours based on game activity)
        $stmt = $pdo->query("
            SELECT COUNT(DISTINCT player_id) 
            FROM game_history 
            WHERE game_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stats['active_users'] = $stmt->fetchColumn();
        
        // Total balance
        $stmt = $pdo->query("SELECT SUM(balance) FROM players");
        $stats['total_balance'] = floatval($stmt->fetchColumn() ?: 0);
        
        // Today's revenue (bets - wins)
        $stmt = $pdo->query("
            SELECT 
                COALESCE(SUM(bet_amount), 0) - COALESCE(SUM(win_amount), 0) as revenue
            FROM game_history 
            WHERE DATE(game_date) = CURDATE()
        ");
        $stats['today_revenue'] = floatval($stmt->fetchColumn() ?: 0);
        
        // Games today
        $stmt = $pdo->query("
            SELECT COUNT(*) 
            FROM game_history 
            WHERE DATE(game_date) = CURDATE()
        ");
        $stats['games_today'] = $stmt->fetchColumn();
        
        // Pending withdrawals
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM withdrawal_requests WHERE status = 'pending'");
            $stats['pending_withdrawals'] = $stmt->fetchColumn();
        } catch (PDOException $e) {
            $stats['pending_withdrawals'] = 0;
        }
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load stats: ' . $e->getMessage()]);
    }
}

function handleGetGames($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                gh.*,
                p.username,
                p.phone
            FROM game_history gh
            LEFT JOIN players p ON gh.player_id = p.id
            ORDER BY gh.game_date DESC
            LIMIT 500
        ");
        $stmt->execute();
        $games = $stmt->fetchAll();
        
        // Ensure all games have required fields
        foreach ($games as &$game) {
            $game['username'] = $game['username'] ?: 'Unknown Player';
            $game['phone'] = $game['phone'] ?: 'N/A';
            $game['selected_numbers'] = $game['selected_numbers'] ?: '[]';
            $game['drawn_numbers'] = $game['drawn_numbers'] ?: '[]';
            $game['bet_amount'] = $game['bet_amount'] ?: 0;
            $game['win_amount'] = $game['win_amount'] ?: 0;
            $game['match_count'] = $game['match_count'] ?: 0;
        }
        
        echo json_encode([
            'success' => true,
            'games' => $games
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load games: ' . $e->getMessage()]);
    }
}

function handleGetTransactions($pdo) {
    try {
        // Check if transactions table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'transactions'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("
                SELECT 
                    t.*,
                    p.username
                FROM transactions t
                LEFT JOIN players p ON t.player_id = p.id
                ORDER BY t.transaction_date DESC
                LIMIT 200
            ");
            $stmt->execute();
            $transactions = $stmt->fetchAll();
        } else {
            // Generate transactions from game history
            $stmt = $pdo->prepare("
                SELECT 
                    gh.id,
                    gh.player_id,
                    p.username,
                    gh.bet_amount,
                    gh.win_amount,
                    gh.game_date
                FROM game_history gh
                LEFT JOIN players p ON gh.player_id = p.id
                ORDER BY gh.game_date DESC
                LIMIT 100
            ");
            $stmt->execute();
            $games = $stmt->fetchAll();
            
            $transactions = [];
            foreach ($games as $game) {
                // Bet transaction
                $transactions[] = [
                    'id' => 'bet_' . $game['id'],
                    'player_id' => $game['player_id'],
                    'username' => $game['username'] ?: 'Unknown',
                    'type' => 'bet',
                    'amount' => $game['bet_amount'],
                    'balance_before' => 0,
                    'balance_after' => 0,
                    'transaction_date' => $game['game_date'],
                    'description' => 'Keno game bet'
                ];
                
                // Win transaction if applicable
                if (floatval($game['win_amount']) > 0) {
                    $transactions[] = [
                        'id' => 'win_' . $game['id'],
                        'player_id' => $game['player_id'],
                        'username' => $game['username'] ?: 'Unknown',
                        'type' => 'win',
                        'amount' => $game['win_amount'],
                        'balance_before' => 0,
                        'balance_after' => 0,
                        'transaction_date' => $game['game_date'],
                        'description' => 'Keno game win'
                    ];
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'transactions' => $transactions
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load transactions: ' . $e->getMessage()]);
    }
}

function handleGetLeaderboard($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                username,
                total_winnings,
                games_played,
                games_won,
                CASE 
                    WHEN games_played > 0 THEN ROUND((games_won / games_played) * 100, 2)
                    ELSE 0 
                END as win_percentage
            FROM players 
            WHERE games_played > 0
            ORDER BY total_winnings DESC
            LIMIT 10
        ");
        $stmt->execute();
        $leaderboard = $stmt->fetchAll();
        
        // Ensure all fields have default values
        foreach ($leaderboard as &$player) {
            $player['total_winnings'] = $player['total_winnings'] ?: 0;
            $player['games_played'] = $player['games_played'] ?: 0;
            $player['games_won'] = $player['games_won'] ?: 0;
            $player['win_percentage'] = $player['win_percentage'] ?: 0;
        }
        
        echo json_encode([
            'success' => true,
            'leaderboard' => $leaderboard
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load leaderboard: ' . $e->getMessage()]);
    }
}
?>
