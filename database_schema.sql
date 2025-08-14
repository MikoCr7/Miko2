-- Kenooo Database Schema
-- Ethiopian Keno Game Database Structure

-- Create database (uncomment if you want to create a new database)
-- CREATE DATABASE IF NOT EXISTS keno_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE keno_game;

-- Players table - stores user account information
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(15) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    total_winnings DECIMAL(15,2) DEFAULT 0.00,
    total_bets DECIMAL(15,2) DEFAULT 0.00,
    games_played INT DEFAULT 0,
    games_won INT DEFAULT 0,
    status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    can_deposit BOOLEAN DEFAULT TRUE,
    can_withdraw BOOLEAN DEFAULT TRUE,
    last_active TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_username (username),
    INDEX idx_balance (balance),
    INDEX idx_created_at (created_at),
    INDEX idx_can_deposit (can_deposit),
    INDEX idx_can_withdraw (can_withdraw)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game sessions table - stores active game state
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    selected_numbers JSON NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    game_state ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player_id (player_id),
    INDEX idx_game_state (game_state),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game rounds table - stores round information
CREATE TABLE IF NOT EXISTS game_rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_number INT NOT NULL UNIQUE,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    drawn_numbers JSON NULL,
    status ENUM('active', 'drawing', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_round_number (round_number),
    INDEX idx_status (status),
    INDEX idx_start_time (start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game tickets table - stores individual bets/tickets
CREATE TABLE IF NOT EXISTS game_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    round_id INT NOT NULL,
    selected_numbers JSON NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    win_amount DECIMAL(10,2) DEFAULT 0.00,
    match_count INT DEFAULT 0,
    status ENUM('pending', 'won', 'lost') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES game_rounds(id) ON DELETE CASCADE,
    INDEX idx_player_id (player_id),
    INDEX idx_round_id (round_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game history table - stores completed game results (updated for round-based system)
CREATE TABLE IF NOT EXISTS game_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    round_id INT NOT NULL,
    ticket_id INT NOT NULL,
    selected_numbers JSON NOT NULL,
    drawn_numbers JSON NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    win_amount DECIMAL(10,2) DEFAULT 0.00,
    match_count INT NOT NULL,
    game_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES game_rounds(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES game_tickets(id) ON DELETE CASCADE,
    INDEX idx_player_id (player_id),
    INDEX idx_round_id (round_id),
    INDEX idx_game_date (game_date),
    INDEX idx_win_amount (win_amount),
    INDEX idx_match_count (match_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions table - stores financial transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal', 'win', 'loss', 'bet') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance_before DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    description TEXT,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player_id (player_id),
    INDEX idx_type (type),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_amount (amount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (optional)
-- INSERT INTO players (phone, username, balance) VALUES ('+251900000000', 'admin', 10000.00);

-- Create views for easier data access (optional)
CREATE OR REPLACE VIEW player_stats AS
SELECT 
    p.id,
    p.username,
    p.phone,
    p.balance,
    p.total_winnings,
    p.total_bets,
    p.games_played,
    p.games_won,
    CASE 
        WHEN p.games_played > 0 THEN ROUND((p.games_won / p.games_played) * 100, 2)
        ELSE 0 
    END as win_percentage,
    p.created_at,
    p.updated_at
FROM players p;

CREATE OR REPLACE VIEW recent_games AS
SELECT 
    gh.id,
    p.username,
    gh.bet_amount,
    gh.win_amount,
    gh.match_count,
    gh.game_date
FROM game_history gh
JOIN players p ON gh.player_id = p.id
ORDER BY gh.game_date DESC;

-- Sample data insertion (optional - for testing)
-- INSERT INTO players (phone, username, balance) VALUES 
-- ('+251911111111', 'player1', 1000.00),
-- ('+251922222222', 'player2', 500.00),
-- ('+251933333333', 'player3', 750.00);

-- Notes:
-- 1. All monetary values are stored as DECIMAL(15,2) for precision
-- 2. Phone numbers are stored as VARCHAR(15) to accommodate international formats
-- 3. JSON fields store arrays of numbers for game selections and results
-- 4. Foreign key constraints ensure data integrity
-- 5. Indexes are created for frequently queried columns
-- 6. Timestamps track creation and update times automatically
-- 7. The schema supports the Ethiopian Birr (ETB) currency
