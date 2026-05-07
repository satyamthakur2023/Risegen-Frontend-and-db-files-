<?php
// ========================================================
// RiseGen Admin Dashboard (admin_dashboard.php) - Logic Block
// ========================================================

// --- TEMPORARY ERROR REPORTING (Remove in production) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --------------------------------------------------------

// 1. Configuration & Initialization
// Assumes config.php defines $conn (mysqli object), $db_error (bool), and $error_message (string).
require_once 'config.php';

// Defensive initialization (copied from user_management for robustness)
$conn = $conn ?? null;
$db_error = $db_error ?? true;
$error_message = $error_message ?? "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4' role='alert'>❌ Database configuration missing or connection status unknown.</div>";

// Initialize Data Containers
$total_users = 0;
$total_admins = 0;
$active_users_7d = 0;
$login_data_chart = [];
$recent_users = [];


// 2. FETCH DASHBOARD METRICS
if (!$db_error && $conn) {
    // --- 2.1 Get Total Users and Admins (assuming 'role' column exists in 'users') ---
    $users_sql = "SELECT 
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins,
                    COUNT(id) AS total_users
                  FROM users";
    
    if ($result = $conn->query($users_sql)) {
        if ($row = $result->fetch_assoc()) {
            $total_users = (int)$row['total_users'];
            $total_admins = (int)$row['total_admins'];
        }
        $result->free();
    } else {
        $db_error = true;
        $error_message .= "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mt-4' role='alert'>⚠️ User count error: " . htmlspecialchars($conn->error) . "</div>";
    }

    // --- 2.2 Get 7-Day Active Users (Requires 'user_logins' table with 'user_id' and a timestamp column) ---
    // FIXED: Changed 'ul.created_at' to 'ul.login_time' to resolve the 'Unknown column' error.
    // NOTE: If 'login_time' is incorrect, replace it with the actual timestamp column name from your 'user_logins' table.
    $active_users_sql = "SELECT COUNT(DISTINCT ul.user_id) AS active_users 
                         FROM user_logins ul 
                         WHERE ul.login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

    if ($result = $conn->query($active_users_sql)) {
        if ($row = $result->fetch_assoc()) {
            $active_users_7d = (int)$row['active_users'];
        }
        $result->free();
    } else {
        $db_error = true;
        $error_message .= "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mt-4' role='alert'>⚠️ Active user count error: " . htmlspecialchars($conn->error) . "</div>";
    }


    // --- 2.3 Get Login Trend Data (Last 7 days) ---
    // FIXED: Using 'login_time' here as well.
    $login_trend_sql = "SELECT 
                          DATE(login_time) AS login_date, 
                          COUNT(DISTINCT user_id) AS logins 
                        FROM user_logins
                        WHERE login_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                        GROUP BY login_date 
                        ORDER BY login_date ASC"; // ASC for correct chart order

    if ($result = $conn->query($login_trend_sql)) {
        while ($row = $result->fetch_assoc()) {
            $login_data_chart[] = $row;
        }
        $result->free();
    } else {
        $db_error = true;
        $error_message .= "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mt-4' role='alert'>⚠️ Login trend error: " . htmlspecialchars($conn->error) . "</div>";
    }

    // --- 2.4 Get Recent Users ---
    // FIXED: Removed 'last_login' from the SELECT statement.
    $recent_users_sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5";
    
    if ($result = $conn->query($recent_users_sql)) {
        while ($row = $result->fetch_assoc()) {
            $recent_users[] = $row;
        }
        $result->free();
    } else {
        $db_error = true;
        $error_message .= "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mt-4' role='alert'>⚠️ Recent users error: " . htmlspecialchars($conn->error) . "</div>";
    }

} else {
    // Database connection failed, error message is already set by config.php.
}

// 3. Close DB Connection (Best Practice)
if ($conn) {
    $conn->close();
}

// Prepare data for Chart.js
$chart_labels = json_encode(array_column($login_data_chart, 'login_date'));
$chart_data = json_encode(array_column($login_data_chart, 'logins'));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | RiseGen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        tailwind.config = { theme: { extend: { colors: { primary: '#4f46e5', secondary: '#10b981' } } } };
        body { font-family: 'Inter', sans-serif; min-height: 100vh; background: #f3f4f6; }
        .card-shadow { border-radius: 1rem; box-shadow: 0 15px 40px rgba(0,0,0,0.08); background: #fff; }
        @media (max-width: 640px) {
            .grid-stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="text-gray-800">

<header class="fixed w-full bg-gray-800 text-white shadow z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center h-16 px-4 sm:px-6 lg:px-8">
        <div class="text-2xl font-extrabold text-primary">RiseGen<span class="text-white font-light">Admin</span></div>
        <nav class="hidden md:flex space-x-4 items-center">
            <a href="admin_dashboard.php" class="flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">Dashboard</a>
            <a href="user_management.php" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-sm font-medium">User Management</a>
            <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 p-2 rounded-lg" title="Logout"><i data-lucide="log-out" class="w-5 h-5"></i></a>
        </nav>
        <div class="md:hidden">
            <button id="mobile-menu-toggle" type="button" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700"><i data-lucide="menu" class="h-6 w-6"></i></button>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-8 pt-24 md:pt-20">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Admin Dashboard</h1>
    <p class="text-gray-500 mb-8">Welcome back to the RiseGen control center.</p>

    <!-- Error/Status Display -->
    <?= $db_error ? $error_message : '' ?>

    <!-- 3. STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 grid-stats">
        
        <!-- Card 1: Total Users -->
        <div class="card-shadow p-6 flex justify-between items-center border-b-4 border-primary/70">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($total_users) ?></p>
            </div>
            <div class="p-3 bg-primary/10 rounded-full text-primary">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2: 7-Day Active Users -->
        <div class="card-shadow p-6 flex justify-between items-center border-b-4 border-secondary/70">
            <div>
                <p class="text-sm font-medium text-gray-500">Active (Last 7d)</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($active_users_7d) ?></p>
            </div>
            <div class="p-3 bg-secondary/10 rounded-full text-secondary">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
        </div>
        
        <!-- Card 3: Administrators -->
        <div class="card-shadow p-6 flex justify-between items-center border-b-4 border-indigo-500/70">
            <div>
                <p class="text-sm font-medium text-gray-500">Administrators</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($total_admins) ?></p>
            </div>
            <div class="p-3 bg-indigo-500/10 rounded-full text-indigo-500">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: User Activity Chart -->
        <div class="lg:col-span-2">
            <div class="card-shadow p-6 h-full">
                <h2 class="text-xl font-bold text-gray-700 mb-4"><i data-lucide="bar-chart-2" class="w-5 h-5 inline mr-2 text-primary"></i> Daily Active Users (7-Day Trend)</h2>
                <div>
                    <canvas id="loginChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Recent Registrations -->
        <div class="lg:col-span-1">
            <div class="card-shadow p-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4"><i data-lucide="user-plus" class="w-5 h-5 inline mr-2 text-primary"></i> Recent Registrations</h2>
                <ul class="divide-y divide-gray-200">
                    <?php if (empty($recent_users)): ?>
                        <li class="py-3 text-center text-gray-500">No recent users found.</li>
                    <?php else: ?>
                        <?php foreach ($recent_users as $user): ?>
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($user['username']) ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                </div>
                                <span class="text-xs text-gray-400">
                                    <?= (new DateTime($user['created_at']))->format('M d') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <a href="user_management.php" class="mt-4 block text-center text-sm font-medium text-primary hover:text-indigo-700 transition">
                    View Full Directory &rarr;
                </a>
            </div>
        </div>
    </div>
</main>

<footer class="mt-12 p-4 text-center text-gray-500 border-t border-gray-200">
    <p class="text-sm">
        &copy; <?= date("Y"); ?> RiseGen Admin Panel. Built with PHP and Tailwind CSS.
    </p>
</footer>

<script>
    // 1. Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // 2. Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const nav = document.querySelector('header nav');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                nav.classList.toggle('hidden');
                nav.classList.toggle('flex');
            });
        }

        // 3. Initialize Chart.js
        const ctx = document.getElementById('loginChart');
        
        if (ctx) {
            const labels = <?= $chart_labels ?>;
            const data = <?= $chart_data ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Unique Logins',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Unique Users'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>
</body>
</html>
