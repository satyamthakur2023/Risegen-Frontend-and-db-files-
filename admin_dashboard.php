<?php
/**
 * RiseGen Admin Dashboard - Advanced Refactor
 */

// 1. Security & Session Management
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// 2. Optimization: Single Database Connection Logic
$conn = $conn ?? null;
if (!$conn || $conn->connect_error) {
    die("Connection failed: " . ($conn->connect_error ?? "Unknown error"));
}

/**
 * Helper to fetch a single value
 */
function get_scalar($conn, $sql, $params = [], $types = "") {
    $stmt = $conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_row();
    return $row ? $row[0] : 0;
}

// 3. Metrics Aggregation
// Total Users and Admins in one go
$stats = $conn->query("SELECT 
    COUNT(id) as total, 
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins 
    FROM users")->fetch_assoc();

$total_users = $stats['total'] ?? 0;
$total_admins = $stats['admins'] ?? 0;

// Active users last 7 days (Prepared Statement)
$active_7d_sql = "SELECT COUNT(DISTINCT user_id) FROM user_logins WHERE login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$active_users_7d = get_scalar($conn, $active_7d_sql);

// 4. Chart Data Preparation (Handling Missing Dates)
$login_trend_sql = "SELECT DATE(login_time) as date, COUNT(DISTINCT user_id) as count 
                    FROM user_logins 
                    WHERE login_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                    GROUP BY DATE(login_time) ORDER BY date ASC";
$res = $conn->query($login_trend_sql);
$raw_chart_data = [];
while ($row = $res->fetch_assoc()) {
    $raw_chart_data[$row['date']] = $row['count'];
}

// Fill gaps for the last 7 days so the chart is continuous
$chart_final = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_final[$date] = $raw_chart_data[$date] ?? 0;
}

$chart_labels = json_encode(array_keys($chart_final));
$chart_values = json_encode(array_values($chart_final));

// 5. Recent Users
$recent_users = $conn->query("SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <title>RiseGen | Advanced Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(229, 231, 235, 1); }
    </style>
</head>
<body class="h-full">

<div class="min-h-full">
    <nav class="bg-white border-b border-gray-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-indigo-600 p-1.5 rounded-lg">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">RiseGen<span class="text-indigo-600">Pro</span></span>
                </div>
                <div class="flex items-center gap-4">
                    <button class="text-gray-400 hover:text-gray-500"><i data-lucide="bell" class="w-5 h-5"></i></button>
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">A</div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:letter-spacing-tight">System Overview</h2>
                    <p class="text-sm text-gray-500 mt-1">Real-time analytics and user management.</p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <button class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Export Report</button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <?php
                $metrics = [
                    ['Total Users', $total_users, 'users', 'text-blue-600', 'bg-blue-50'],
                    ['Active (7d)', $active_users_7d, 'activity', 'text-emerald-600', 'bg-emerald-50'],
                    ['System Admins', $total_admins, 'shield', 'text-purple-600', 'bg-purple-50']
                ];
                foreach ($metrics as $m): ?>
                <div class="glass-card overflow-hidden rounded-2xl p-6 transition-all hover:shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-xl p-3 <?= $m[4] ?> <?= $m[3] ?>">
                            <i data-lucide="<?= $m[2] ?>" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-gray-500"><?= $m[0] ?></dt>
                                <dd class="text-2xl font-bold text-gray-900"><?= number_format($m[1]) ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Engagement Trend</h3>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Live Updates</span>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">New Arrivals</h3>
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            <?php foreach ($recent_users as $user): ?>
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                        <?= strtoupper($user['username'][0]) ?>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></p>
                                        <p class="truncate text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <?= date('M d', strtotime($user['created_at'])) ?>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href="user_management.php" class="mt-6 block w-full text-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-100 transition">Manage All Users</a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    const ctx = document.getElementById('engagementChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: 'Unique Logins',
                data: <?= $chart_values ?>,
                borderColor: '#4f46e5',
                borderWidth: 3,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHitRadius: 30,
                pointHoverBackgroundColor: '#4f46e5',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                y: { grid: { display: false }, border: { display: false }, ticks: { stepSize: 1 } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
</script>
</body>
</html>