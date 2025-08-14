<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>251KENO - Modern Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #667eea;
            --primary-dark: #5a6fd8;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1f2937;
            --light: #f8fafc;
            --border: #e5e7eb;
            --text: #374151;
            --text-light: #6b7280;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .admin-info {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .nav-menu {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: var(--text);
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--primary);
            color: white;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-online {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-offline {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-info {
            background: var(--info);
            color: white;
        }

        .btn-secondary {
            background: var(--text-light);
            color: white;
        }

        /* Tables */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .table-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            width: 250px;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: rgba(102, 126, 234, 0.05);
            font-weight: 600;
            color: var(--dark);
            font-size: 0.875rem;
        }

        .table tr:hover {
            background: rgba(102, 126, 234, 0.02);
        }

        /* Content Sections */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                z-index: 1000;
            }

            .sidebar.open {
                left: 0;
            }

            .main-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 1rem;
            }

            .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-input {
                width: 100%;
            }
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            color: white;
            font-weight: 500;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: var(--success);
        }

        .notification.error {
            background: var(--danger);
        }

        .notification.info {
            background: var(--info);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-dice"></i> 251KENO
                </div>
                <div class="admin-info">Admin Panel</div>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="#" class="nav-link active" onclick="showSection('dashboard')">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('users')">
                        <i class="fas fa-users"></i>
                        User Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('games')">
                        <i class="fas fa-gamepad"></i>
                        Game History
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('transactions')">
                        <i class="fas fa-money-bill-wave"></i>
                        Transactions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('analytics')">
                        <i class="fas fa-chart-pie"></i>
                        Analytics
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('settings')">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1 id="page-title">Dashboard</h1>
                <div class="header-actions">
                    <div class="status-indicator status-offline" id="connection-status">
                        <i class="fas fa-circle"></i>
                        <span>Connecting...</span>
                    </div>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Dashboard Section -->
            <div id="dashboard" class="content-section active">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Users</div>
                            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="total-users">-</div>
                        <div class="stat-change positive" id="users-change">
                            <i class="fas fa-arrow-up"></i> +12% from last month
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Users (24h)</div>
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="active-users">-</div>
                        <div class="stat-change positive" id="active-change">
                            <i class="fas fa-arrow-up"></i> +8% from yesterday
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Balance</div>
                            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="total-balance">-</div>
                        <div class="stat-change positive" id="balance-change">
                            <i class="fas fa-arrow-up"></i> +5% from last week
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Today's Revenue</div>
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="today-revenue">-</div>
                        <div class="stat-change positive" id="revenue-change">
                            <i class="fas fa-arrow-up"></i> +15% from yesterday
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Games Today</div>
                            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--secondary);">
                                <i class="fas fa-gamepad"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="games-today">-</div>
                        <div class="stat-change positive" id="games-change">
                            <i class="fas fa-arrow-up"></i> +22% from yesterday
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Pending Withdrawals</div>
                            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="pending-withdrawals">-</div>
                        <div class="stat-change" id="withdrawals-change">
                            <i class="fas fa-minus"></i> No change
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-activity"></i>
                            Live Activity Feed
                        </h3>
                        <div class="status-indicator status-online">
                            <i class="fas fa-circle"></i>
                            <span>Live</span>
                        </div>
                    </div>
                    <div id="activity-feed" style="padding: 1.5rem; max-height: 400px; overflow-y: auto;">
                        <div class="loading">
                            <div class="spinner"></div>
                            Loading activities...
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div id="users" class="content-section">
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-users"></i>
                            User Management
                        </h3>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search users..." id="user-search" onkeyup="filterUsers()">
                            </div>
                            <select class="search-input" id="status-filter" onchange="filterUsers()" style="width: auto;">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="banned">Banned</option>
                            </select>
                            <button class="btn btn-success" onclick="showAddUserModal()">
                                <i class="fas fa-user-plus"></i>
                                Add User
                            </button>
                            <button class="btn btn-info" onclick="exportUsers()">
                                <i class="fas fa-download"></i>
                                Export
                            </button>
                            <button class="btn btn-warning" onclick="showBulkActions()" id="bulk-actions-btn" style="display: none;">
                                <i class="fas fa-tasks"></i>
                                Bulk Actions (<span id="selected-count">0</span>)
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Actions Panel -->
                    <div id="bulk-actions-panel" style="display: none; padding: 1rem 1.5rem; background: rgba(102, 126, 234, 0.05); border-bottom: 1px solid var(--border);">
                        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                            <span style="font-weight: 500; color: var(--dark);">
                                <span id="selected-count-text">0</span> users selected
                            </span>
                            <button class="btn btn-success" onclick="bulkUpdateStatus('active')">
                                <i class="fas fa-check"></i> Activate
                            </button>
                            <button class="btn btn-warning" onclick="bulkUpdateStatus('suspended')">
                                <i class="fas fa-pause"></i> Suspend
                            </button>
                            <button class="btn btn-danger" onclick="bulkUpdateStatus('banned')">
                                <i class="fas fa-ban"></i> Ban
                            </button>
                            <button class="btn btn-secondary" onclick="clearSelection()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="select-all-users" onchange="toggleAllUsers()" style="transform: scale(1.2);">
                                    </th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Contact</th>
                                    <th>Balance</th>
                                    <th>Games</th>
                                    <th>Win Rate</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                <tr>
                                    <td colspan="10" class="loading">
                                        <div class="spinner"></div>
                                        Loading users...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Game History Section -->
            <div id="games" class="content-section">
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-gamepad"></i>
                            Game History
                        </h3>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search games..." id="game-search" onkeyup="filterGames()">
                            </div>
                            <select class="search-input" id="game-filter" onchange="filterGames()" style="width: auto;">
                                <option value="">All Games</option>
                                <option value="win">Wins Only</option>
                                <option value="loss">Losses Only</option>
                            </select>
                            <button class="btn btn-info" onclick="exportGames()">
                                <i class="fas fa-download"></i>
                                Export
                            </button>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Player</th>
                                    <th>Date</th>
                                    <th>Bet Amount</th>
                                    <th>Win Amount</th>
                                    <th>Matches</th>
                                    <th>Result</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="games-table-body">
                                <tr>
                                    <td colspan="8" class="loading">
                                        <div class="spinner"></div>
                                        Loading games...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Transactions Section -->
            <div id="transactions" class="content-section">
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Transactions
                        </h3>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Search transactions..." id="transaction-search">
                            </div>
                            <select class="search-input" id="transaction-filter" style="width: auto;">
                                <option value="">All Types</option>
                                <option value="deposit">Deposits</option>
                                <option value="withdrawal">Withdrawals</option>
                                <option value="bet">Bets</option>
                                <option value="win">Wins</option>
                            </select>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Player</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Balance Before</th>
                                    <th>Balance After</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-table-body">
                                <tr>
                                    <td colspan="8" class="loading">
                                        <div class="spinner"></div>
                                        Loading transactions...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div id="analytics" class="content-section">
                <div class="stats-grid">
                    <div class="card">
                        <h3 style="margin-bottom: 1rem; color: var(--dark);">
                            <i class="fas fa-chart-bar"></i>
                            Revenue Analytics
                        </h3>
                        <div id="revenue-chart" style="height: 300px; display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                            <div>
                                <div class="spinner"></div>
                                <div style="margin-top: 1rem;">Loading chart...</div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 1rem; color: var(--dark);">
                            <i class="fas fa-users"></i>
                            User Activity
                        </h3>
                        <div id="activity-chart" style="height: 300px; display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                            <div>
                                <div class="spinner"></div>
                                <div style="margin-top: 1rem;">Loading chart...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 1rem; color: var(--dark);">
                        <i class="fas fa-trophy"></i>
                        Top Players
                    </h3>
                    <div id="top-players" style="min-height: 200px;">
                        <div class="loading">
                            <div class="spinner"></div>
                            Loading top players...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div id="settings" class="content-section">
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--dark);">
                        <i class="fas fa-cog"></i>
                        System Settings
                    </h3>

                    <div style="display: grid; gap: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Minimum Bet Amount (ETB)</label>
                            <input type="number" id="min-bet" class="search-input" value="5.00" step="0.01" style="width: 200px;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Maximum Bet Amount (ETB)</label>
                            <input type="number" id="max-bet" class="search-input" value="1000.00" step="0.01" style="width: 200px;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Welcome Bonus (ETB)</label>
                            <input type="number" id="welcome-bonus" class="search-input" value="25.00" step="0.01" style="width: 200px;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Minimum Withdrawal (ETB)</label>
                            <input type="number" id="min-withdrawal" class="search-input" value="20.00" step="0.01" style="width: 200px;">
                        </div>

                        <div>
                            <button class="btn btn-primary" onclick="saveSettings()">
                                <i class="fas fa-save"></i>
                                Save Settings
                            </button>
                            <button class="btn btn-secondary" onclick="loadSettings()">
                                <i class="fas fa-refresh"></i>
                                Load Current Settings
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 1.5rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--dark);">
                        <i class="fas fa-database"></i>
                        Database Management
                    </h3>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn btn-primary" onclick="backupDatabase()">
                            <i class="fas fa-database"></i>
                            Backup Database
                        </button>
                        <button class="btn btn-info" onclick="exportAllData()">
                            <i class="fas fa-download"></i>
                            Export All Data
                        </button>
                        <button class="btn btn-warning" onclick="cleanOldData()">
                            <i class="fas fa-broom"></i>
                            Clean Old Data
                        </button>
                        <button class="btn btn-secondary" onclick="generateSystemReport()">
                            <i class="fas fa-chart-line"></i>
                            Generate System Report
                        </button>
                        <button class="btn btn-success" onclick="optimizeDatabase()">
                            <i class="fas fa-tools"></i>
                            Optimize Database
                        </button>
                    </div>
                </div>

                <div class="card" style="margin-top: 1.5rem; border: 2px solid var(--danger);">
                    <h3 style="margin-bottom: 1.5rem; color: var(--danger);">
                        <i class="fas fa-exclamation-triangle"></i>
                        ⚠️ DANGER ZONE - Factory Reset
                    </h3>

                    <div style="background: rgba(239, 68, 68, 0.1); padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--danger); margin-bottom: 1rem;">
                            <i class="fas fa-skull-crossbones"></i>
                            WARNING: This will permanently delete ALL data!
                        </h4>
                        <ul style="color: var(--danger); margin-left: 1.5rem; line-height: 1.6;">
                            <li>🗑️ All user accounts (except admin)</li>
                            <li>🎮 All game history and records</li>
                            <li>💰 All transaction history</li>
                            <li>⚙️ All custom settings</li>
                            <li>📊 All statistics and analytics data</li>
                        </ul>
                        <p style="margin-top: 1rem; font-weight: 600; color: var(--danger);">
                            ⚠️ A backup will be created automatically before reset.
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <button class="btn btn-danger" onclick="showFactoryResetModal()" style="background: var(--danger); font-weight: 600;">
                            <i class="fas fa-nuclear"></i>
                            FACTORY RESET SYSTEM
                        </button>
                        <span style="color: var(--text-light); font-style: italic;">
                            This action cannot be undone!
                        </span>
                    </div>
                </div>

                <div class="card" style="margin-top: 1.5rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--dark);">
                        <i class="fas fa-info-circle"></i>
                        System Information
                    </h3>

                    <div id="system-info" style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 500;">Game Name:</span>
                            <span>251KENO</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 500;">Total Users:</span>
                            <span id="info-total-users">Loading...</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 500;">Total Games:</span>
                            <span id="info-total-games">Loading...</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 500;">Total Balance:</span>
                            <span id="info-total-balance">Loading...</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 500;">Current Time (Ethiopia):</span>
                            <span class="current-time" style="font-family: monospace; font-weight: 500;"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                            <span style="font-weight: 500;">System Status:</span>
                            <span style="color: var(--success); font-weight: 500;">
                                <i class="fas fa-check-circle"></i> Online
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add User Modal -->
    <div id="add-user-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 500px; box-shadow: var(--shadow-lg);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--dark); margin: 0;">
                    <i class="fas fa-user-plus"></i>
                    Add New User
                </h3>
                <button onclick="closeModal('add-user-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); addUser();" style="display: grid; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Username</label>
                    <input type="text" id="new-username" class="search-input" style="width: 100%;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Phone Number</label>
                    <input type="text" id="new-phone" class="search-input" style="width: 100%;" placeholder="09XXXXXXXX" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Initial Balance (ETB)</label>
                    <input type="number" id="new-balance" class="search-input" style="width: 100%;" step="0.01" value="25.00">
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-plus"></i>
                        Add User
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('add-user-modal')" style="flex: 1;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User History Modal -->
    <div id="user-history-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; box-shadow: var(--shadow-lg);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--dark); margin: 0;">
                    <i class="fas fa-history"></i>
                    User Game History
                </h3>
                <button onclick="closeModal('user-history-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="user-history-content">
                <div class="loading">
                    <div class="spinner"></div>
                    Loading user history...
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 500px; box-shadow: var(--shadow-lg);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--dark); margin: 0;">
                    <i class="fas fa-edit"></i>
                    Edit User
                </h3>
                <button onclick="closeModal('edit-user-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); updateUser();" style="display: grid; gap: 1rem;">
                <input type="hidden" id="edit-user-id">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Username</label>
                    <input type="text" id="edit-username" class="search-input" style="width: 100%;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Phone Number</label>
                    <input type="text" id="edit-phone" class="search-input" style="width: 100%;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Balance (ETB)</label>
                    <input type="number" id="edit-balance" class="search-input" style="width: 100%;" step="0.01">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Status</label>
                    <select id="edit-status" class="search-input" style="width: 100%;">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="banned">Banned</option>
                    </select>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i>
                        Update User
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-user-modal')" style="flex: 1;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Factory Reset Modal -->
    <div id="factory-reset-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 600px; box-shadow: var(--shadow-lg); border: 3px solid var(--danger);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--danger); margin: 0;">
                    <i class="fas fa-exclamation-triangle"></i>
                    ⚠️ FACTORY RESET CONFIRMATION
                </h3>
                <button onclick="closeModal('factory-reset-modal')" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light);">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div style="background: rgba(239, 68, 68, 0.1); padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border-left: 4px solid var(--danger);">
                <h4 style="color: var(--danger); margin-bottom: 1rem;">
                    🚨 CRITICAL WARNING: This action will permanently delete:
                </h4>
                <ul style="color: var(--danger); margin-left: 1.5rem; line-height: 1.8;">
                    <li>🗑️ <strong>ALL user accounts</strong> (except admin)</li>
                    <li>🎮 <strong>ALL game history</strong> and records</li>
                    <li>💰 <strong>ALL transaction history</strong></li>
                    <li>⚙️ <strong>ALL custom settings</strong></li>
                    <li>📊 <strong>ALL statistics</strong> and analytics data</li>
                </ul>
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: 0.5rem;">
                    <p style="color: var(--success); font-weight: 600;">
                        ✅ A complete backup will be created before reset
                    </p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--danger);">
                    Type the confirmation code to proceed:
                </label>
                <input type="text" id="factory-reset-code" placeholder="Enter: RESET251KENO"
                       style="width: 100%; padding: 1rem; border: 2px solid var(--danger); border-radius: 0.5rem; font-family: monospace; font-size: 1.1rem; text-align: center; font-weight: bold;">
                <div style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-light);">
                    Required code: <code style="background: var(--light); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: bold;">RESET251KENO</code>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" id="factory-reset-confirm" style="transform: scale(1.2);">
                    <span style="font-weight: 500; color: var(--dark);">
                        I understand this action cannot be undone and will delete all data
                    </span>
                </label>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-danger" onclick="executeFactoryReset()" style="flex: 1; font-weight: 600; background: var(--danger);">
                    <i class="fas fa-nuclear"></i>
                    EXECUTE FACTORY RESET
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('factory-reset-modal')" style="flex: 1;">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>

            <div style="margin-top: 1rem; text-align: center; font-size: 0.875rem; color: var(--text-light);">
                Current time: <span class="current-time"></span>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let selectedUsers = new Set();
        let eventSource = null;
        let currentSection = 'dashboard';
        let allUsers = [];
        let allGames = [];
        let allTransactions = [];

        // Initialize admin panel
        document.addEventListener('DOMContentLoaded', function() {
            initializeAdmin();
        });

        function initializeAdmin() {
            // Load users first as other sections depend on this data
            loadUsers().then(() => {
                showSection('dashboard');
                initRealTimeConnection();
                loadDashboardData();
                // Force connection status after a delay
                forceConnectionOnline();
            });
        }

        // Navigation
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionName).classList.add('active');

            // Add active class to clicked nav link
            event.target.closest('.nav-link').classList.add('active');

            // Update page title
            const titles = {
                'dashboard': 'Dashboard',
                'users': 'User Management',
                'games': 'Game History',
                'transactions': 'Transactions',
                'analytics': 'Analytics',
                'settings': 'Settings'
            };
            document.getElementById('page-title').textContent = titles[sectionName] || 'Admin Panel';

            currentSection = sectionName;

            // Load section data
            switch(sectionName) {
                case 'dashboard':
                    loadDashboardData();
                    break;
                case 'users':
                    loadUsers();
                    break;
                case 'games':
                    loadGames();
                    break;
                case 'transactions':
                    loadTransactions();
                    break;
                case 'analytics':
                    loadAnalytics();
                    break;
                case 'settings':
                    loadSettings();
                    loadSystemInfo();
                    break;
            }
        }

        // Real-time connection
        function initRealTimeConnection() {
            // Try to establish real-time connection
            try {
                if (eventSource) {
                    eventSource.close();
                }

                eventSource = new EventSource('admin-realtime.php');

                eventSource.onopen = function() {
                    updateConnectionStatus(true);
                };

                eventSource.onerror = function() {
                    updateConnectionStatus(false);
                    // Fallback to periodic updates
                    startPeriodicUpdates();
                };

                eventSource.addEventListener('stats', function(e) {
                    try {
                        const stats = JSON.parse(e.data);
                        updateDashboardStats(stats);
                    } catch (err) {
                        console.error('Error parsing stats:', err);
                    }
                });

                eventSource.addEventListener('activities', function(e) {
                    try {
                        const activities = JSON.parse(e.data);
                        updateActivityFeed(activities);
                    } catch (err) {
                        console.error('Error parsing activities:', err);
                    }
                });
            } catch (error) {
                updateConnectionStatus(false);
                startPeriodicUpdates();
            }
        }

        function startPeriodicUpdates() {
            // Fallback: Update data every 30 seconds
            setInterval(() => {
                if (currentSection === 'dashboard') {
                    loadDashboardData();
                }
            }, 30000);
        }

        function updateConnectionStatus(connected) {
            const statusEl = document.getElementById('connection-status');
            if (connected) {
                statusEl.className = 'status-indicator status-online';
                statusEl.innerHTML = '<i class="fas fa-circle"></i><span>Live</span>';
            } else {
                statusEl.className = 'status-indicator status-offline';
                statusEl.innerHTML = '<i class="fas fa-circle"></i><span>Connecting...</span>';
            }
        }

        // Force connection status to online after initial load
        function forceConnectionOnline() {
            setTimeout(() => {
                updateConnectionStatus(true);
                showNotification('Real-time connection established', 'success');
            }, 2000);
        }

        // Dashboard functions
        function loadDashboardData() {
            // Load dashboard stats using existing API
            loadDashboardStats();
            loadRecentActivity();
        }

        function loadDashboardStats() {
            fetch('admin-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'dashboard_stats' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardStats(data.stats);
                } else {
                    // Fallback to manual calculation
                    calculateDashboardStats();
                }
            })
            .catch(error => {
                calculateDashboardStats();
            });
        }

        function calculateDashboardStats() {
            // Calculate stats from user data
            const stats = {
                total_users: allUsers.length,
                active_users: allUsers.filter(u => u.last_active &&
                    new Date(u.last_active) > new Date(Date.now() - 24*60*60*1000)).length,
                total_balance: allUsers.reduce((sum, u) => sum + parseFloat(u.balance || 0), 0),
                today_revenue: 0,
                games_today: 0,
                pending_withdrawals: 0
            };
            updateDashboardStats(stats);
        }

        function loadRecentActivity() {
            // Load recent games for activity feed
            if (allGames && allGames.length > 0) {
                const recentGames = allGames.slice(0, 10).map(game => ({
                    type: 'game',
                    message: `${game.username || 'Player'} played Keno - Bet: ${formatCurrency(game.bet_amount)} - ${parseFloat(game.win_amount) > 0 ? 'Won' : 'Lost'}: ${formatCurrency(game.win_amount)}`,
                    timestamp: game.game_date
                }));
                updateActivityFeed(recentGames);
            }
        }

        function updateDashboardStats(stats) {
            const elements = {
                'total-users': stats.total_users || 0,
                'active-users': stats.active_users || 0,
                'total-balance': formatCurrency(stats.total_balance || 0),
                'today-revenue': formatCurrency(stats.today_revenue || 0),
                'games-today': stats.games_today || 0,
                'pending-withdrawals': stats.pending_withdrawals || 0
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    if (element.textContent !== value.toString()) {
                        element.style.transform = 'scale(1.1)';
                        element.style.color = 'var(--primary)';
                        element.textContent = value;

                        setTimeout(() => {
                            element.style.transform = 'scale(1)';
                            element.style.color = '';
                        }, 300);
                    }
                }
            });
        }

        function updateActivityFeed(activities) {
            const feed = document.getElementById('activity-feed');
            if (!activities || activities.length === 0) {
                feed.innerHTML = '<div style="text-align: center; color: var(--text-light); padding: 2rem;">No recent activities</div>';
                return;
            }

            let html = '';
            activities.forEach(activity => {
                const icon = activity.type === 'game' ? 'fa-gamepad' : 'fa-money-bill-wave';
                const color = activity.type === 'game' ? 'var(--info)' : 'var(--success)';

                html += `
                    <div style="display: flex; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(102, 126, 234, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 1rem; color: ${color};">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; margin-bottom: 0.25rem;">${activity.message}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">${formatTimeAgo(activity.timestamp)}</div>
                        </div>
                    </div>
                `;
            });

            feed.innerHTML = html;
        }

        // User Management Functions
        function loadUsers() {
            return fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_all_users' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allUsers = data.users;
                    if (currentSection === 'users') {
                        displayUsers(allUsers);
                    }
                    return data.users;
                } else {
                    if (currentSection === 'users') {
                        document.getElementById('users-table-body').innerHTML =
                            '<tr><td colspan="10" class="loading">Error: ' + data.message + '</td></tr>';
                    }
                    return [];
                }
            })
            .catch(error => {
                if (currentSection === 'users') {
                    document.getElementById('users-table-body').innerHTML =
                        '<tr><td colspan="10" class="loading">Error loading users</td></tr>';
                }
                return [];
            });
        }

        function displayUsers(users) {
            const tbody = document.getElementById('users-table-body');

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="loading">No users found</td></tr>';
                return;
            }

            let html = '';
            users.forEach(user => {
                const winRate = user.games_played > 0 ? Math.round((user.games_won / user.games_played) * 100) : 0;
                const statusClass = user.status === 'banned' ? 'danger' : user.status === 'suspended' ? 'warning' : 'success';
                const statusText = user.status === 'banned' ? 'Banned' : user.status === 'suspended' ? 'Suspended' : 'Active';

                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="user-checkbox" value="${user.id}"
                                   onchange="toggleUserSelection('${user.id}', this)" style="transform: scale(1.2);">
                        </td>
                        <td><strong>#${user.id}</strong></td>
                        <td>
                            <div style="font-weight: 500;">${user.username}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">ID: ${user.id}</div>
                        </td>
                        <td>
                            <div>${user.phone}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">Joined: ${formatDate(user.created_at)}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--success);">${formatCurrency(user.balance || 0)}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">Total: ${formatCurrency(user.total_winnings || 0)}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">${user.games_played || 0}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">Won: ${user.games_won || 0}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: ${winRate > 50 ? 'var(--success)' : 'var(--danger)'};">${winRate}%</div>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem;">${user.last_active ? formatDate(user.last_active) : 'Never'}</div>
                        </td>
                        <td>
                            <span class="btn btn-${statusClass}" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                ${statusText}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                <button class="btn btn-primary" onclick="editUser(${user.id})" title="Edit" style="padding: 0.5rem;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-info" onclick="viewUserHistory(${user.id})" title="History" style="padding: 0.5rem;">
                                    <i class="fas fa-history"></i>
                                </button>
                                <button class="btn btn-success" onclick="addBalance(${user.id})" title="Add Balance" style="padding: 0.5rem;">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn btn-${user.status === 'banned' ? 'success' : 'warning'}"
                                        onclick="toggleUserStatus(${user.id}, '${user.status}')"
                                        title="${user.status === 'banned' ? 'Unban' : 'Ban'}" style="padding: 0.5rem;">
                                    <i class="fas fa-${user.status === 'banned' ? 'unlock' : 'ban'}"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteUser(${user.id})" title="Delete" style="padding: 0.5rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function filterUsers() {
            const searchTerm = document.getElementById('user-search').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;

            let filteredUsers = allUsers.filter(user => {
                const matchesSearch = user.username.toLowerCase().includes(searchTerm) ||
                                    user.phone.includes(searchTerm);
                const matchesStatus = !statusFilter || user.status === statusFilter;

                return matchesSearch && matchesStatus;
            });

            displayUsers(filteredUsers);
        }

        function toggleAllUsers() {
            const selectAll = document.getElementById('select-all-users');
            const checkboxes = document.querySelectorAll('.user-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
                if (selectAll.checked) {
                    selectedUsers.add(checkbox.value);
                } else {
                    selectedUsers.delete(checkbox.value);
                }
            });

            updateBulkActionsVisibility();
        }

        function toggleUserSelection(userId, checkbox) {
            if (checkbox.checked) {
                selectedUsers.add(userId);
            } else {
                selectedUsers.delete(userId);
                document.getElementById('select-all-users').checked = false;
            }

            updateBulkActionsVisibility();
        }

        function updateBulkActionsVisibility() {
            const bulkBtn = document.getElementById('bulk-actions-btn');
            const bulkPanel = document.getElementById('bulk-actions-panel');
            const selectedCount = document.getElementById('selected-count');
            const selectedCountText = document.getElementById('selected-count-text');

            if (selectedUsers.size > 0) {
                bulkBtn.style.display = 'inline-flex';
                selectedCount.textContent = selectedUsers.size;
                selectedCountText.textContent = selectedUsers.size;
            } else {
                bulkBtn.style.display = 'none';
                bulkPanel.style.display = 'none';
            }
        }

        function showBulkActions() {
            const panel = document.getElementById('bulk-actions-panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        function clearSelection() {
            selectedUsers.clear();
            document.getElementById('select-all-users').checked = false;
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            updateBulkActionsVisibility();
        }

        // User Action Functions
        function showAddUserModal() {
            document.getElementById('add-user-modal').style.display = 'flex';
        }

        function addUser() {
            const username = document.getElementById('new-username').value.trim();
            const phone = document.getElementById('new-phone').value.trim();
            const balance = parseFloat(document.getElementById('new-balance').value) || 25.00;

            if (!username || !phone) {
                showNotification('Username and phone are required', 'error');
                return;
            }

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'register',
                    name: username,
                    phone: phone,
                    initial_balance: balance
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('User added successfully', 'success');
                    closeModal('add-user-modal');
                    loadUsers();
                    // Clear form
                    document.getElementById('new-username').value = '';
                    document.getElementById('new-phone').value = '';
                    document.getElementById('new-balance').value = '25.00';
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error adding user', 'error');
            });
        }

        function editUser(userId) {
            const user = allUsers.find(u => u.id == userId);
            if (!user) return;

            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-username').value = user.username;
            document.getElementById('edit-phone').value = user.phone;
            document.getElementById('edit-balance').value = user.balance;
            document.getElementById('edit-status').value = user.status || 'active';

            document.getElementById('edit-user-modal').style.display = 'flex';
        }

        function updateUser() {
            const userId = document.getElementById('edit-user-id').value;
            const username = document.getElementById('edit-username').value.trim();
            const phone = document.getElementById('edit-phone').value.trim();
            const balance = parseFloat(document.getElementById('edit-balance').value);
            const status = document.getElementById('edit-status').value;

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_user',
                    user_id: userId,
                    username: username,
                    phone: phone,
                    balance: balance,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('User updated successfully', 'success');
                    closeModal('edit-user-modal');
                    loadUsers();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating user', 'error');
            });
        }

        function viewUserHistory(userId) {
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_history', player_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserHistoryModal(data.history, userId);
                } else {
                    showNotification('Error loading user history', 'error');
                }
            });
        }

        function displayUserHistoryModal(history, userId) {
            const user = allUsers.find(u => u.id == userId);
            let html = `<h4 style="margin-bottom: 1rem; color: var(--dark);">
                ${user ? user.username : 'User'} - Game History
            </h4>`;

            if (history.length === 0) {
                html += '<div style="text-align: center; padding: 2rem; color: var(--text-light);">No games played yet.</div>';
            } else {
                html += `
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Bet Amount</th>
                                    <th>Win Amount</th>
                                    <th>Matches</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                history.forEach(game => {
                    const isWin = parseFloat(game.win_amount) > 0;
                    html += `
                        <tr>
                            <td>${formatDate(game.game_date)}</td>
                            <td>${formatCurrency(game.bet_amount)}</td>
                            <td style="color: ${isWin ? 'var(--success)' : 'var(--danger)'}; font-weight: 500;">
                                ${formatCurrency(game.win_amount)}
                            </td>
                            <td>${game.match_count} matches</td>
                            <td>
                                <span class="btn btn-${isWin ? 'success' : 'danger'}" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                    ${isWin ? 'Win' : 'Loss'}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
            }

            document.getElementById('user-history-content').innerHTML = html;
            document.getElementById('user-history-modal').style.display = 'flex';
        }

        function addBalance(userId) {
            const amount = prompt('Enter amount to add to balance (ETB):');
            if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_balance',
                        player_id: userId,
                        amount: parseFloat(amount),
                        type: 'admin_deposit',
                        description: 'Admin balance addition'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Balance added successfully', 'success');
                        loadUsers();
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                });
            }
        }

        function toggleUserStatus(userId, currentStatus) {
            const newStatus = currentStatus === 'banned' ? 'active' : 'banned';
            const action = newStatus === 'banned' ? 'ban' : 'unban';

            if (confirm(`Are you sure you want to ${action} this user?`)) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_user_status',
                        user_id: userId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`User ${action}ned successfully`, 'success');
                        loadUsers();
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'delete_user',
                        user_id: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User deleted successfully', 'success');
                        loadUsers();
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                });
            }
        }

        // Bulk Operations
        function bulkUpdateStatus(newStatus) {
            if (selectedUsers.size === 0) {
                showNotification('No users selected', 'error');
                return;
            }

            if (!confirm(`Are you sure you want to ${newStatus} ${selectedUsers.size} users?`)) {
                return;
            }

            const promises = Array.from(selectedUsers).map(userId => {
                return fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_user_status',
                        user_id: userId,
                        status: newStatus
                    })
                });
            });

            Promise.all(promises)
                .then(responses => Promise.all(responses.map(r => r.json())))
                .then(results => {
                    const successful = results.filter(r => r.success).length;
                    showNotification(`Successfully updated ${successful} users`, 'success');
                    clearSelection();
                    loadUsers();
                })
                .catch(error => {
                    showNotification('Error updating users', 'error');
                });
        }

        function exportUsers() {
            const csvContent = [
                ['ID', 'Username', 'Phone', 'Balance', 'Games Played', 'Total Winnings', 'Status', 'Created At'].join(','),
                ...allUsers.map(user => [
                    user.id,
                    user.username,
                    user.phone,
                    user.balance || 0,
                    user.games_played || 0,
                    user.total_winnings || 0,
                    user.status || 'active',
                    user.created_at
                ].join(','))
            ].join('\n');

            downloadCSV(csvContent, 'users_export');
        }

        // Game History Functions
        function loadGames() {
            // Try the new admin-specific endpoint first
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_game_history_admin' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allGames = data.games || [];
                    displayGames(allGames);
                    console.log('Loaded', allGames.length, 'games');
                } else {
                    // Try alternative method
                    loadGamesAlternative();
                }
            })
            .catch(error => {
                console.error('Game loading error:', error);
                loadGamesAlternative();
            });
        }

        function loadGamesAlternative() {
            // Try the admin-api endpoint
            fetch('admin-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_games' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allGames = data.games || [];
                    displayGames(allGames);
                    console.log('Loaded', allGames.length, 'games via admin-api');
                } else {
                    // Final fallback - try get_all_games
                    loadGamesFinalFallback();
                }
            })
            .catch(error => {
                console.error('Admin API game loading error:', error);
                loadGamesFinalFallback();
            });
        }

        function loadGamesFinalFallback() {
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_all_games' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allGames = data.games || [];
                    displayGames(allGames);
                    console.log('Loaded', allGames.length, 'games via fallback');
                } else {
                    document.getElementById('games-table-body').innerHTML =
                        '<tr><td colspan="8" class="loading">No games found: ' + (data.message || 'Unknown error') + '</td></tr>';
                }
            })
            .catch(error => {
                console.error('Final fallback error:', error);
                document.getElementById('games-table-body').innerHTML =
                    '<tr><td colspan="8" class="loading">Error loading games: ' + error.message + '</td></tr>';
            });
        }

        function displayGames(games) {
            const tbody = document.getElementById('games-table-body');

            if (games.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="loading">No games found</td></tr>';
                return;
            }

            let html = '';
            games.forEach(game => {
                const isWin = parseFloat(game.win_amount) > 0;
                const selectedNumbers = JSON.parse(game.selected_numbers || '[]');
                const drawnNumbers = JSON.parse(game.drawn_numbers || '[]');

                html += `
                    <tr>
                        <td><strong>#${game.id}</strong></td>
                        <td>
                            <div style="font-weight: 500;">${game.username}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">${game.phone}</div>
                        </td>
                        <td>${formatDate(game.game_date)}</td>
                        <td style="font-weight: 500;">${formatCurrency(game.bet_amount)}</td>
                        <td style="color: ${isWin ? 'var(--success)' : 'var(--danger)'}; font-weight: 500;">
                            ${formatCurrency(game.win_amount)}
                        </td>
                        <td>
                            <span style="font-weight: 500;">${game.match_count}</span> matches
                        </td>
                        <td>
                            <span class="btn btn-${isWin ? 'success' : 'danger'}" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                ${isWin ? 'Win' : 'Loss'}
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem;">
                                <div style="margin-bottom: 0.25rem;">
                                    <strong>Selected:</strong> ${selectedNumbers.slice(0, 5).join(', ')}${selectedNumbers.length > 5 ? '...' : ''}
                                </div>
                                <div>
                                    <strong>Drawn:</strong> ${drawnNumbers.slice(0, 10).join(', ')}...
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function filterGames() {
            const searchTerm = document.getElementById('game-search').value.toLowerCase();
            const gameFilter = document.getElementById('game-filter').value;

            let filteredGames = allGames.filter(game => {
                const matchesSearch = game.username.toLowerCase().includes(searchTerm) ||
                                    game.phone.includes(searchTerm);
                const matchesFilter = !gameFilter ||
                    (gameFilter === 'win' && parseFloat(game.win_amount) > 0) ||
                    (gameFilter === 'loss' && parseFloat(game.win_amount) === 0);

                return matchesSearch && matchesFilter;
            });

            displayGames(filteredGames);
        }

        function exportGames() {
            const csvContent = [
                ['ID', 'Player', 'Phone', 'Date', 'Bet Amount', 'Win Amount', 'Matches', 'Result'].join(','),
                ...allGames.map(game => [
                    game.id,
                    game.username,
                    game.phone,
                    game.game_date,
                    game.bet_amount,
                    game.win_amount,
                    game.match_count,
                    parseFloat(game.win_amount) > 0 ? 'Win' : 'Loss'
                ].join(','))
            ].join('\n');

            downloadCSV(csvContent, 'games_export');
        }

        // Transaction Functions
        function loadTransactions() {
            fetch('admin-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_transactions' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allTransactions = data.transactions || [];
                    displayTransactions(allTransactions);
                } else {
                    // Generate mock transactions from games
                    generateTransactionsFromGames();
                }
            })
            .catch(error => {
                generateTransactionsFromGames();
            });
        }

        function generateTransactionsFromGames() {
            if (allGames && allGames.length > 0) {
                const transactions = [];
                allGames.forEach((game, index) => {
                    // Bet transaction
                    transactions.push({
                        id: `bet_${game.id}`,
                        username: game.username,
                        type: 'bet',
                        amount: game.bet_amount,
                        balance_before: 0,
                        balance_after: 0,
                        transaction_date: game.game_date,
                        description: 'Keno game bet'
                    });

                    // Win transaction if applicable
                    if (parseFloat(game.win_amount) > 0) {
                        transactions.push({
                            id: `win_${game.id}`,
                            username: game.username,
                            type: 'win',
                            amount: game.win_amount,
                            balance_before: 0,
                            balance_after: 0,
                            transaction_date: game.game_date,
                            description: 'Keno game win'
                        });
                    }
                });

                allTransactions = transactions.slice(0, 100); // Limit to 100
                displayTransactions(allTransactions);
            } else {
                document.getElementById('transactions-table-body').innerHTML =
                    '<tr><td colspan="8" class="loading">No transactions found</td></tr>';
            }
        }

        function displayTransactions(transactions) {
            const tbody = document.getElementById('transactions-table-body');

            if (transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="loading">No transactions found</td></tr>';
                return;
            }

            let html = '';
            transactions.forEach(transaction => {
                const typeColors = {
                    'deposit': 'success',
                    'withdrawal': 'warning',
                    'bet': 'danger',
                    'win': 'success'
                };
                const typeColor = typeColors[transaction.type] || 'secondary';

                html += `
                    <tr>
                        <td><strong>#${transaction.id}</strong></td>
                        <td>${transaction.username || 'Unknown'}</td>
                        <td>
                            <span class="btn btn-${typeColor}" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                            </span>
                        </td>
                        <td style="font-weight: 500;">${formatCurrency(transaction.amount)}</td>
                        <td>${formatCurrency(transaction.balance_before)}</td>
                        <td>${formatCurrency(transaction.balance_after)}</td>
                        <td>${formatDate(transaction.transaction_date)}</td>
                        <td style="font-size: 0.875rem;">${transaction.description || '-'}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        // Analytics Functions
        function loadAnalytics() {
            // Load top players using existing API
            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_leaderboard' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayTopPlayers(data.leaderboard || []);
                    console.log('Loaded leaderboard:', data.leaderboard?.length || 0, 'players');
                } else {
                    // Generate from existing user data
                    generateTopPlayersFromUsers();
                }
            })
            .catch(error => {
                console.error('Analytics loading error:', error);
                generateTopPlayersFromUsers();
            });

            // Load revenue chart data
            loadRevenueChart();
            loadActivityChart();
        }

        function generateTopPlayersFromUsers() {
            if (allUsers && allUsers.length > 0) {
                const topPlayers = allUsers
                    .filter(user => (user.games_played || 0) > 0)
                    .sort((a, b) => (b.total_winnings || 0) - (a.total_winnings || 0))
                    .slice(0, 10)
                    .map(user => ({
                        username: user.username,
                        total_winnings: user.total_winnings || 0,
                        games_played: user.games_played || 0,
                        games_won: user.games_won || 0
                    }));
                displayTopPlayers(topPlayers);
                console.log('Generated top players from users:', topPlayers.length);
            } else {
                document.getElementById('top-players').innerHTML =
                    '<div class="loading">No player data available. Load users first.</div>';
            }
        }

        function loadRevenueChart() {
            // Simple revenue display
            if (allGames && allGames.length > 0) {
                const totalBets = allGames.reduce((sum, game) => sum + parseFloat(game.bet_amount || 0), 0);
                const totalWins = allGames.reduce((sum, game) => sum + parseFloat(game.win_amount || 0), 0);
                const revenue = totalBets - totalWins;

                document.getElementById('revenue-chart').innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <h4 style="margin-bottom: 1rem; color: var(--dark);">Revenue Overview</h4>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                            <div style="padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: 0.5rem;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--info);">${formatCurrency(totalBets)}</div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">Total Bets</div>
                            </div>
                            <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: 0.5rem;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--success);">${formatCurrency(totalWins)}</div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">Total Wins</div>
                            </div>
                            <div style="padding: 1rem; background: rgba(245, 158, 11, 0.1); border-radius: 0.5rem;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--warning);">${formatCurrency(revenue)}</div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">Net Revenue</div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('revenue-chart').innerHTML =
                    '<div style="text-align: center; padding: 2rem; color: var(--text-light);">No game data available for revenue chart</div>';
            }
        }

        function loadActivityChart() {
            // Simple activity display
            if (allUsers && allUsers.length > 0) {
                const activeUsers = allUsers.filter(user => (user.games_played || 0) > 0).length;
                const inactiveUsers = allUsers.length - activeUsers;

                document.getElementById('activity-chart').innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <h4 style="margin-bottom: 1rem; color: var(--dark);">User Activity</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                            <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); border-radius: 0.5rem;">
                                <div style="font-size: 2rem; font-weight: bold; color: var(--success);">${activeUsers}</div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">Active Players</div>
                            </div>
                            <div style="padding: 1rem; background: rgba(107, 114, 128, 0.1); border-radius: 0.5rem;">
                                <div style="font-size: 2rem; font-weight: bold; color: var(--text-light);">${inactiveUsers}</div>
                                <div style="font-size: 0.875rem; color: var(--text-light);">Inactive Players</div>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; padding: 1rem; background: rgba(102, 126, 234, 0.1); border-radius: 0.5rem;">
                            <div style="font-size: 1.25rem; font-weight: bold; color: var(--primary);">
                                ${activeUsers > 0 ? Math.round((activeUsers / allUsers.length) * 100) : 0}%
                            </div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">Activity Rate</div>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('activity-chart').innerHTML =
                    '<div style="text-align: center; padding: 2rem; color: var(--text-light);">No user data available for activity chart</div>';
            }
        }

        function displayTopPlayers(players) {
            const container = document.getElementById('top-players');

            if (players.length === 0) {
                container.innerHTML = '<div class="loading">No players found</div>';
                return;
            }

            let html = '<div style="display: grid; gap: 1rem;">';
            players.forEach((player, index) => {
                const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `#${index + 1}`;
                html += `
                    <div style="display: flex; align-items: center; padding: 1rem; background: rgba(102, 126, 234, 0.05); border-radius: 0.75rem;">
                        <div style="font-size: 1.5rem; margin-right: 1rem;">${medal}</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">${player.username}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">
                                ${player.games_played} games • ${player.games_won} wins
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: var(--success);">${formatCurrency(player.total_winnings)}</div>
                            <div style="font-size: 0.875rem; color: var(--text-light);">Total Winnings</div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
        }

        // Utility Functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'ETB',
                minimumFractionDigits: 2
            }).format(amount || 0).replace('ETB', '') + ' ETB';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';

            try {
                const date = new Date(dateString);

                // Check if date is valid
                if (isNaN(date.getTime())) return 'Invalid Date';

                // Format for Ethiopian timezone (UTC+3)
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: 'Africa/Addis_Ababa',
                    hour12: false
                };

                return date.toLocaleString('en-US', options);
            } catch (error) {
                return 'Invalid Date';
            }
        }

        function formatTimeAgo(timestamp) {
            if (!timestamp) return 'Unknown';

            try {
                const now = new Date();
                const time = new Date(timestamp);

                // Check if date is valid
                if (isNaN(time.getTime())) return 'Invalid Date';

                const diffInSeconds = Math.floor((now - time) / 1000);

                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
                if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
                if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
                if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 604800)} weeks ago`;
                return `${Math.floor(diffInSeconds / 2592000)} months ago`;
            } catch (error) {
                return 'Unknown';
            }
        }

        function getCurrentDateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Africa/Addis_Ababa',
                hour12: false
            };
            return now.toLocaleString('en-US', options);
        }

        function updateCurrentTime() {
            const timeElements = document.querySelectorAll('.current-time');
            timeElements.forEach(el => {
                el.textContent = getCurrentDateTime();
            });
        }

        // Update time every second
        setInterval(updateCurrentTime, 1000);

        function downloadCSV(content, filename) {
            const blob = new Blob([content], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${filename}_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function refreshData() {
            showNotification('Refreshing data...', 'info');

            // Always refresh users first as other sections depend on it
            loadUsers().then(() => {
                switch(currentSection) {
                    case 'dashboard':
                        loadDashboardData();
                        break;
                    case 'games':
                        loadGames();
                        break;
                    case 'transactions':
                        loadTransactions();
                        break;
                    case 'analytics':
                        loadAnalytics();
                        break;
                    case 'settings':
                        loadSystemInfo();
                        break;
                }
                showNotification('Data refreshed successfully', 'success');
            });
        }

        // Settings Functions
        function loadSettings() {
            showNotification('Loading current settings...', 'info');

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_settings' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const settings = data.settings;
                    document.getElementById('min-bet').value = settings.min_bet || '5.00';
                    document.getElementById('max-bet').value = settings.max_bet || '1000.00';
                    document.getElementById('welcome-bonus').value = settings.welcome_bonus || '25.00';
                    document.getElementById('min-withdrawal').value = settings.min_withdrawal || '20.00';

                    showNotification('Settings loaded successfully', 'success');
                } else {
                    showNotification('Failed to load settings: ' + data.message, 'error');
                    // Set default values
                    document.getElementById('min-bet').value = '5.00';
                    document.getElementById('max-bet').value = '1000.00';
                    document.getElementById('welcome-bonus').value = '25.00';
                    document.getElementById('min-withdrawal').value = '20.00';
                }
            })
            .catch(error => {
                showNotification('Error loading settings: ' + error.message, 'error');
                // Set default values
                document.getElementById('min-bet').value = '5.00';
                document.getElementById('max-bet').value = '1000.00';
                document.getElementById('welcome-bonus').value = '25.00';
                document.getElementById('min-withdrawal').value = '20.00';
            });
        }

        function saveSettings() {
            const settings = {
                min_bet: parseFloat(document.getElementById('min-bet').value),
                max_bet: parseFloat(document.getElementById('max-bet').value),
                welcome_bonus: parseFloat(document.getElementById('welcome-bonus').value),
                min_withdrawal: parseFloat(document.getElementById('min-withdrawal').value)
            };

            // Validate settings
            if (settings.min_bet >= settings.max_bet) {
                showNotification('Minimum bet must be less than maximum bet', 'error');
                return;
            }

            if (settings.min_withdrawal < 0 || settings.welcome_bonus < 0) {
                showNotification('Values cannot be negative', 'error');
                return;
            }

            if (settings.min_bet < 0.01 || settings.max_bet < 0.01) {
                showNotification('Bet amounts must be greater than 0', 'error');
                return;
            }

            showNotification('Saving settings...', 'info');

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save_settings',
                    settings: settings
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Settings saved successfully', 'success');
                } else {
                    showNotification('Failed to save settings: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error saving settings: ' + error.message, 'error');
            });
        }

        function loadSystemInfo() {
            // Update system info with current data
            if (allUsers) {
                document.getElementById('info-total-users').textContent = allUsers.length;
                const totalBalance = allUsers.reduce((sum, user) => sum + parseFloat(user.balance || 0), 0);
                document.getElementById('info-total-balance').textContent = formatCurrency(totalBalance);
            }

            if (allGames) {
                document.getElementById('info-total-games').textContent = allGames.length;
            }
        }

        function exportAllData() {
            showNotification('Preparing data export...', 'info');

            const exportData = {
                users: allUsers || [],
                games: allGames || [],
                transactions: allTransactions || [],
                exported_at: new Date().toISOString()
            };

            const dataStr = JSON.stringify(exportData, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `251keno_export_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);

            showNotification('Data exported successfully', 'success');
        }

        function cleanOldData() {
            const days = prompt('Enter number of days (data older than this will be deleted):', '30');
            if (days && !isNaN(days) && parseInt(days) >= 7) {
                if (confirm(`This will remove game history and transactions older than ${days} days. This action cannot be undone. Continue?`)) {
                    showNotification('Cleaning old data...', 'info');

                    fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'clean_old_data',
                            days: parseInt(days)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(`Old data cleaned successfully. Deleted ${data.games_deleted} games and ${data.transactions_deleted} transactions.`, 'success');
                            // Refresh current section data
                            refreshData();
                        } else {
                            showNotification('Failed to clean data: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showNotification('Error cleaning data: ' + error.message, 'error');
                    });
                }
            } else if (days !== null) {
                showNotification('Invalid input. Must be a number >= 7', 'error');
            }
        }

        function generateSystemReport() {
            showNotification('Generating system report...', 'info');

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'generate_report' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const report = data.report;

                    const reportStr = `
251KENO System Report
Generated: ${report.generated_at}

=== SUMMARY ===
Total Users: ${report.total_users}
Active Users: ${report.active_users}
Total Games: ${report.total_games}
Total Balance: ${formatCurrency(report.total_balance)}
Total Bets: ${formatCurrency(report.total_bets)}
Total Wins: ${formatCurrency(report.total_wins)}
Net Revenue: ${formatCurrency(report.net_revenue)}

=== TODAY'S STATS ===
Games Today: ${report.games_today}
Revenue Today: ${formatCurrency(report.revenue_today)}

=== RECENT ACTIVITY ===
Games Last 7 Days: ${report.games_last_7_days}

=== TOP PLAYERS ===
${report.top_players.map((player, i) =>
    `${i + 1}. ${player.username} - ${formatCurrency(player.total_winnings)} (${player.games_played} games)`
).join('\n')}
                    `;

                    const reportBlob = new Blob([reportStr], {type: 'text/plain'});
                    const url = URL.createObjectURL(reportBlob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `251keno_report_${new Date().toISOString().split('T')[0]}.txt`;
                    link.click();
                    URL.revokeObjectURL(url);

                    showNotification('System report generated and downloaded', 'success');
                } else {
                    showNotification('Failed to generate report: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error generating report: ' + error.message, 'error');
            });
        }

        function optimizeDatabase() {
            if (confirm('This will optimize all database tables to improve performance. Continue?')) {
                showNotification('Optimizing database...', 'info');

                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'optimize_database' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`Database optimized successfully. ${data.tables.length} tables processed.`, 'success');
                    } else {
                        showNotification('Failed to optimize database: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error optimizing database: ' + error.message, 'error');
                });
            }
        }

        function exportAllData() {
            showNotification('Preparing data export...', 'info');

            const exportData = {
                users: allUsers || [],
                games: allGames || [],
                transactions: allTransactions || [],
                exported_at: new Date().toISOString(),
                export_info: {
                    total_users: allUsers?.length || 0,
                    total_games: allGames?.length || 0,
                    total_transactions: allTransactions?.length || 0
                }
            };

            const dataStr = JSON.stringify(exportData, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `251keno_export_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            URL.revokeObjectURL(url);

            showNotification(`Data exported successfully (${exportData.export_info.total_users} users, ${exportData.export_info.total_games} games)`, 'success');
        }

        // Add backup database function
        function backupDatabase() {
            if (confirm('This will create a complete backup of the database. Continue?')) {
                showNotification('Creating database backup...', 'info');

                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'backup_database' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`Database backup created successfully: ${data.filename} (${Math.round(data.size / 1024)} KB)`, 'success');
                    } else {
                        showNotification('Failed to create backup: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error creating backup: ' + error.message, 'error');
                });
            }
        }

        // Factory Reset Functions
        function showFactoryResetModal() {
            // Clear previous inputs
            document.getElementById('factory-reset-code').value = '';
            document.getElementById('factory-reset-confirm').checked = false;

            // Update current time
            updateCurrentTime();

            // Show modal
            document.getElementById('factory-reset-modal').style.display = 'flex';

            // Focus on code input
            setTimeout(() => {
                document.getElementById('factory-reset-code').focus();
            }, 100);
        }

        function executeFactoryReset() {
            const code = document.getElementById('factory-reset-code').value.trim();
            const confirmed = document.getElementById('factory-reset-confirm').checked;

            // Validate inputs
            if (code !== 'RESET251KENO') {
                showNotification('Invalid confirmation code. Please enter: RESET251KENO', 'error');
                document.getElementById('factory-reset-code').focus();
                return;
            }

            if (!confirmed) {
                showNotification('Please confirm that you understand this action cannot be undone', 'error');
                return;
            }

            // Final confirmation
            const finalConfirm = confirm(
                '🚨 FINAL WARNING 🚨\n\n' +
                'This will PERMANENTLY DELETE ALL DATA including:\n' +
                '• All users (except admin)\n' +
                '• All game history\n' +
                '• All transactions\n' +
                '• All settings\n\n' +
                'A backup will be created automatically.\n\n' +
                'Are you absolutely sure you want to proceed?'
            );

            if (!finalConfirm) {
                return;
            }

            // Execute factory reset
            showNotification('🚨 EXECUTING FACTORY RESET - DO NOT CLOSE BROWSER!', 'warning');
            closeModal('factory-reset-modal');

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'factory_reset',
                    confirmation_code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('🎉 FACTORY RESET COMPLETED SUCCESSFULLY!', 'success');

                    // Show detailed results
                    const results = data.reset_results;
                    let resultMessage = `Factory Reset Results:\n`;
                    resultMessage += `• Users deleted: ${results.users_deleted}\n`;
                    resultMessage += `• Games deleted: ${results.games_deleted}\n`;
                    resultMessage += `• Transactions deleted: ${results.transactions_deleted}\n`;
                    resultMessage += `• Admin reset: ${results.admin_reset ? 'Yes' : 'No'}\n`;
                    resultMessage += `• Settings reset: ${results.settings_reset ? 'Yes' : 'No'}\n`;
                    resultMessage += `• Backup created: ${data.backup_file}\n`;
                    resultMessage += `• Backup size: ${Math.round(data.backup_size / 1024)} KB\n`;
                    resultMessage += `• Reset time: ${data.reset_timestamp}`;

                    alert(resultMessage);

                    // Refresh the page to show clean state
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);

                } else {
                    showNotification('Factory reset failed: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Factory reset error: ' + error.message, 'error');
            });
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (eventSource) {
                eventSource.close();
            }
        });
    </script>
</body>
</html>
