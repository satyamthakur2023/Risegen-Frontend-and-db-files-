<?php
// ========================================================
// RiseGen Student Dashboard (v8.0 - Unified & Secure)
// ========================================================

// --------------------------------------------------------
// 1. Configuration & Session Management
// --------------------------------------------------------
session_start();

// Security Headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// --- SIMULATED LOGIN/DB DATA START ---
// Set 'premium' to 'free' to see locked state
$_SESSION['student_id'] = 102;
$_SESSION['access_level'] = 'premium'; // Change to 'free' to test locked features
$_SESSION['username'] = "Maria Rodriguez";
$student_name = $_SESSION['username'] . ($_SESSION['access_level'] === 'premium' ? " (Premium)" : " (Free Tier)");
// --- SIMULATED LOGIN/DB DATA END ---

// Security check: If not logged in (Uncomment in production)
if (!isset($_SESSION['student_id'])) {
    // header('Location: login.php');
    // exit;
}

// Function to check access level for gating
function is_premium() {
    return (isset($_SESSION['access_level']) && $_SESSION['access_level'] === 'premium');
}

// --------------------------------------------------------
// 2. Advanced Data Simulation Layer
// --------------------------------------------------------
$app_version = "8.0.0";
$username = htmlspecialchars($student_name);
$user_id = $_SESSION['student_id'];
$is_premium_user = is_premium();

// Helper functions (initials, etc.)
function get_initials(string $name): string {
    $parts = explode(' ', trim($name));
    $initials = '';
    if (count($parts) > 0) {
        $initials .= strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
        }
    }
    return $initials;
}

// SIMULATE LAST LOGIN DATA - Integrated Security Check Data
function get_last_login_details(): array {
    // Mock data for the *previous* successful login
    return [
        'timestamp' => '2025-10-16 23:45:12', // Yesterday's date
        'ip_address' => '103.45.18.22',
        'location' => 'Greater Noida, India',
        'device' => 'Windows 10 / Chrome',
    ];
}

$initials = get_initials($_SESSION['username']);
$premium_badge = $is_premium_user ? '<span class="ml-2 inline-flex items-center rounded-full bg-secondary px-3 py-0.5 text-xs font-medium text-white shadow-sm">PRO</span>' : '';
$last_login = get_last_login_details();

// Simulated data structures (Courses)
$courses_data = [
    [
        'id' => 1, 
        'name' => 'Advanced Web Dev', 
        'current_grade' => 'A-', 
        'progress' => 85,
        'announcements' => ['Project 3 due Friday.', 'Office hours canceled Tuesday.'],
        'locked_feature' => 'Detailed Rubric Analysis'
    ],
    [
        'id' => 2, 
        'name' => 'Data Structures & Algo', 
        'current_grade' => 'B', 
        'progress' => 60,
        'announcements' => ['Midterm results posted.'],
        'locked_feature' => 'AI Topic Reviewer'
    ],
];

// Simulated data structures (Gamification)
$gamification_data = [
    ['name' => 'Completed 5/10 Assignments', 'icon' => 'list-end', 'color' => 'text-primary'],
    ['name' => 'Perfect Attendance', 'icon' => 'check-circle', 'color' => 'text-secondary'],
    ['name' => '100 Study Hours Logged', 'icon' => 'clock', 'color' => 'text-yellow-500', 'premium_lock' => true],
];

// --------------------------------------------------------
// 3. HTML Presentation (View) - Tailwind
// --------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiseGen Advanced Dashboard v<?php echo $app_version; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        // --- Tailwind Configuration ---
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5', // Indigo-600
                        secondary: '#10b981', // Emerald-500
                        darkbg: '#0f172a', // Slate-900
                    },
                    boxShadow: {
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        /* --- Custom Styles --- */
        body { font-family: 'Inter', sans-serif; transition: background-color 0.3s, color 0.3s; }
        body:not(.dark) { background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%); }
        .dark body { background: #0f172a; }
        .card-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .dark .card-glass {
            background: rgba(30, 41, 59, 0.8);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .active-link {
            background-color: #4f46e5;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
        }
        .locked-overlay {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            color: white;
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 5;
            padding: 20px;
            border-radius: 1rem;
        }
        .progress-bar-fill {
            transition: width 1s ease-in-out;
        }
    </style>
</head>

<body class="text-gray-800 dark:text-gray-100 min-h-screen">

<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

<div class="flex h-screen overflow-hidden">

    <aside id="sidebar" class="card-glass fixed md:static inset-y-0 left-0 w-64 p-6 flex flex-col justify-between z-40 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
        <div>
            <div class="text-center mb-10">
                <i data-lucide="graduation-cap" class="w-10 h-10 mx-auto text-primary mb-2"></i> 
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">RiseGen</h1>
            </div>

           <nav class="space-y-2">
            <a href="#" class="flex items-center px-4 py-3 rounded-xl active-link">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="ml-4">Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50 text-gray-700 dark:text-gray-300">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span class="ml-4">My Courses</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50 text-gray-700 dark:text-gray-300">
                <i data-lucide="bell-dot" class="w-5 h-5"></i>
                <span class="ml-4">Notifications</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50 text-gray-700 dark:text-gray-300">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span class="ml-4">Security & Profile</span>
            </a>
        </nav>
        </div>

        <div class="text-center border-t border-gray-200/50 dark:border-slate-700 pt-4 text-sm text-gray-500 dark:text-gray-400">
            <p>v<?php echo $app_version; ?></p>
            <a href="logout.php" class="inline-block mt-2 text-red-500 hover:text-red-400 font-medium transition">Logout ↗</a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">

        <header class="flex justify-between items-center px-6 py-4 bg-white/90 dark:bg-darkbg/90 backdrop-blur-md shadow-lg sticky top-0 z-20">
            <div class="flex items-center">
                <button id="menuToggle" onclick="toggleSidebar()" class="md:hidden mr-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Hello, <?php echo $_SESSION['username']; ?>! <?php echo $premium_badge; ?>
                </h1>
            </div>
            <div class="flex items-center space-x-3">
                <a href="upgrade.php" class="hidden sm:inline-block bg-secondary text-white font-semibold py-2 px-4 rounded-full text-sm hover:bg-emerald-600 transition shadow-md">
                    Upgrade Now
                </a>
                <button id="themeToggle" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                    <i data-lucide="sun" class="w-6 h-6"></i> 
                </button>
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold uppercase shadow-md">
                    <?php echo $initials; ?>
                </div>
            </div>
        </header>

        <section class="p-4 sm:p-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="card-glass p-6 rounded-2xl shadow-card md:col-span-1 border-l-4 border-secondary/50 hover:border-secondary transition-all cursor-pointer">
                    <h5 class="text-lg font-bold mb-2 flex items-center text-gray-800 dark:text-gray-100">
                        <i data-lucide="lock-keyhole" class="w-5 h-5 mr-2 text-secondary"></i> Security Summary
                    </h5>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        Last login was **<?php echo htmlspecialchars($last_login['location']); ?>** on **<?php echo date('M d, H:i A', strtotime($last_login['timestamp'])); ?>**.
                    </p>
                    <div class="flex justify-between items-center text-xs font-medium text-gray-500 dark:text-gray-400">
                        <span class="flex items-center"><i data-lucide="globe" class="w-3 h-3 mr-1"></i> <?php echo htmlspecialchars($last_login['ip_address']); ?></span>
                        <a href="#" class="text-red-500 hover:text-red-400 font-semibold flex items-center">
                            Review Activity <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="card-glass p-6 rounded-2xl shadow-card md:col-span-1 border-l-4 border-yellow-500/50">
                    <h5 class="text-lg font-bold mb-2 flex items-center text-gray-800 dark:text-gray-100">
                        <i data-lucide="list-todo" class="w-5 h-5 mr-2 text-yellow-600"></i> Open To-Dos
                    </h5>
                    <p class="text-3xl font-extrabold text-yellow-600 dark:text-yellow-500 mb-1">
                        7
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Assignments and readings due this week.</p>
                </div>

                <div class="card-glass p-6 rounded-2xl shadow-card md:col-span-1 border-l-4 border-blue-500/50">
                    <h5 class="text-lg font-bold mb-2 flex items-center text-gray-800 dark:text-gray-100">
                        <i data-lucide="zap" class="w-5 h-5 mr-2 text-blue-600"></i> Weekly Focus
                    </h5>
                    <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-500 mb-1">
                        8 Hrs <span class="text-sm font-medium text-gray-500 dark:text-gray-400">/ 15 Goal</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Time logged since Sunday.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 pt-2">My Active Courses</h2>
                    <?php foreach ($courses_data as $course): ?>
                    <div class="card-glass p-6 rounded-2xl shadow-card relative">
                        <div class="flex justify-between items-start mb-3">
                            <h5 class="text-2xl font-extrabold text-gray-800 dark:text-gray-100"><?php echo htmlspecialchars($course['name']); ?></h5>
                            <span class="bg-indigo-600 text-white text-lg font-bold px-4 py-1 rounded-full shadow-lg">
                                <?php echo htmlspecialchars($course['current_grade']); ?>
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Course Completion: <?php echo $course['progress']; ?>%</p>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5">
                                <div class="progress-bar-fill bg-primary h-2.5 rounded-full" style="width: <?php echo $course['progress']; ?>%;"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 border-gray-200 dark:border-slate-700">
                            <div class="md:col-span-2">
                                <h6 class="font-semibold flex items-center mb-2 text-indigo-700 dark:text-indigo-400">
                                    <i data-lucide="megaphone" class="w-4 h-4 mr-2"></i> Latest Announcements
                                </h6>
                                <ul class="text-sm list-disc list-inside space-y-1 text-gray-600 dark:text-gray-300">
                                    <?php foreach ($course['announcements'] as $announcement): ?>
                                        <li><?php echo htmlspecialchars($announcement); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="md:col-span-1 border-l pl-4 border-gray-200 dark:border-slate-700">
                                <h6 class="font-semibold mb-2 text-red-500 dark:text-red-400">
                                    <i data-lucide="lock" class="w-4 h-4 mr-1 inline"></i> Exclusive Tool
                                </h6>
                                <?php if ($is_premium_user): ?>
                                    <p class="text-secondary text-sm">✅ **<?php echo $course['locked_feature']; ?>** is unlocked.</p>
                                    <a href="#" class="text-primary text-sm hover:underline">Access Tool</a>
                                <?php else: ?>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Unlock **<?php echo $course['locked_feature']; ?>** with Premium.</p>
                                    <a href="upgrade.php" class="text-red-500 text-sm hover:underline">See how to upgrade</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    
                    <div class="card-glass p-6 rounded-2xl shadow-card relative overflow-hidden">
                        <h5 class="text-xl font-bold mb-4 flex items-center text-yellow-600">
                            <i data-lucide="trophy" class="w-6 h-6 mr-3"></i> Progress & Achievements
                        </h5>
                        
                        <div class="space-y-4">
                            <?php foreach ($gamification_data as $achievement): ?>
                                <?php 
                                // Check if feature is locked for non-premium users
                                $is_locked_achievement = (isset($achievement['premium_lock']) && $achievement['premium_lock'] && !$is_premium_user); 
                                ?>
                                
                                <div class="p-3 rounded-lg flex items-center justify-between relative 
                                    <?php echo $is_locked_achievement ? 'bg-gray-100 dark:bg-slate-700 opacity-50' : 'bg-white dark:bg-slate-800 border border-secondary/50'; ?>">
                                    
                                    <div class="flex items-center">
                                        <i data-lucide="<?php echo $is_locked_achievement ? 'lock' : $achievement['icon']; ?>" 
                                           class="w-5 h-5 mr-3 <?php echo $is_locked_achievement ? 'text-red-500' : $achievement['color']; ?>"></i>
                                           
                                        <p class="font-medium text-sm 
                                            <?php echo $is_locked_achievement ? 'text-gray-700 dark:text-gray-300' : 'text-gray-900 dark:text-gray-100'; ?>">
                                            <?php echo htmlspecialchars($achievement['name']); ?>
                                        </p>
                                    </div>
                                    
                                    <?php if ($is_locked_achievement): ?>
                                        <span class="text-xs text-red-500 font-bold">PRO</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card-glass p-6 rounded-2xl shadow-card relative overflow-hidden">
                        <?php if (!$is_premium_user): ?>
                            <div class="locked-overlay rounded-2xl">
                                <i data-lucide="zap" class="w-12 h-12 mb-3 text-yellow-400 animate-pulse"></i>
                                <h3 class="text-3xl font-bold mb-2">AI TOOLS LOCKED</h3>
                                <p class="text-lg mb-4 text-gray-200">Access your **AI Study Coach** and Essay Checker instantly!</p>
                                <a href="upgrade.php" class="bg-secondary hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-full text-base transition shadow-lg shadow-secondary/50">
                                    Activate Premium
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="text-xl font-bold mb-4 flex items-center text-blue-500">
                            <i data-lucide="cpu" class="w-6 h-6 mr-3"></i> AI & Smart Tools
                        </h5>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-center"><i data-lucide="check-circle" class="w-4 h-4 mr-2 text-primary"></i> What-If Grade Calculator</li>
                            <li class="flex items-center"><i data-lucide="check-circle" class="w-4 h-4 mr-2 text-primary"></i> Plagiarism Checker</li>
                            <li class="flex items-center"><i data-lucide="check-circle" class="w-4 h-4 mr-2 text-primary"></i> Thesis Statement Generator</li>
                        </ul>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">All tools are powered by RiseGen's advanced learning model.</p>
                    </div>
                    
                </div>
            </div>
        </section>
        
        <footer class="p-4 sm:p-6 text-center mt-auto text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-slate-700 bg-white/50 dark:bg-darkbg/50 backdrop-blur-sm">
            <p class="text-sm">
                &copy; <?php echo date("Y"); ?> RiseGen. All rights reserved.
            </p>
        </footer>
    </main>
</div>


<script>
// --- Sidebar Toggle Function (Reused) ---
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    const isClosed = sidebar.classList.contains('-translate-x-full');

    if (isClosed) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

// --- Theme Toggle Function (Reused) ---
function toggleTheme() {
    const html = document.documentElement;
    const isDarkMode = html.classList.toggle('dark');
    localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');

    const themeToggleIcon = document.querySelector('#themeToggle i');
    if (themeToggleIcon && typeof lucide !== 'undefined') {
        themeToggleIcon.setAttribute('data-lucide', isDarkMode ? 'moon' : 'sun');
        lucide.createIcons();
    }
}

// Initialize on window load (Improved Lucide initialization)
window.onload = function() {
    // Check stored theme preference
    const savedTheme = localStorage.getItem('theme');
    const isDarkMode = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    if (isDarkMode) {
        document.documentElement.classList.add('dark');
    }
    
    // Initialize Lucide Icons and set the correct theme toggle icon
    if (typeof lucide !== 'undefined') {
        const themeToggleIcon = document.querySelector('#themeToggle i');
        if (themeToggleIcon) {
            // Set the correct icon attribute first
            themeToggleIcon.setAttribute('data-lucide', isDarkMode ? 'moon' : 'sun');
        }
        
        // Call createIcons ONCE after setting the necessary attributes.
        lucide.createIcons();
    }
    
    // Attach toggle listener to the theme button
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);
};
</script>

</body>
</html>