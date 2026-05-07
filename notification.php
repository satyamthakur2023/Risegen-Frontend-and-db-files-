<?php
// ========================================================
// RiseGen Premium Dashboard (v6.1) - Last Login Notification Page
// ========================================================

// --------------------------------------------------------
// 1. Configuration & Security
// --------------------------------------------------------
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
$pdo = connectDatabase();

function get_user_data(): array {
    return [
        'username' => htmlspecialchars($_SESSION['username'] ?? 'RiseGen User'),
        'user_id'  => htmlspecialchars($_SESSION['user_id'] ?? ''),
    ];
}

function get_last_login_details($pdo, $user_id): array {
    $stmt = $pdo->prepare("SELECT last_login_time, last_login_ip FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return [
        'timestamp'  => $row['last_login_time'] ?? 'N/A',
        'ip_address' => $row['last_login_ip'] ?? 'N/A',
        'location'   => 'N/A',
        'device'     => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    ];
}

// Helper function to extract initials from a full name (for avatar)
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

// --------------------------------------------------------
// 3. Core Logic Execution
// --------------------------------------------------------

$app_version = "6.1.0";
$user_data   = get_user_data();
$last_login  = get_last_login_details($pdo, $_SESSION['user_id']);
$username    = $user_data['username'];
$user_id     = $user_data['user_id'];
$initials    = get_initials($username);

$page_title = "Security Check";

// --------------------------------------------------------
// 4. HTML Presentation (View)
// --------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - RiseGen</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        // --- Tailwind Configuration ---
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5', 
                        secondary: '#10b981', 
                        darkbg: '#0f172a', 
                        darkcard: 'rgba(30, 41, 59, 0.7)' 
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
        .glass {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .dark .glass {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .active-link {
            background: linear-gradient(90deg, #4f46e5, #6366f1);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>

<body class="text-gray-800 dark:text-gray-100 min-h-screen">

<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

<div class="flex h-screen overflow-hidden">

    <aside id="sidebar" class="glass fixed md:static inset-y-0 left-0 w-64 p-6 flex flex-col justify-between z-40 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
        <div>
            <div class="text-center mb-10">
                <i data-lucide="graduation-cap" class="w-8 h-8 mx-auto text-primary"></i> 
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">RiseGen</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Future-Proof Learning</p>
            </div>

           <nav class="space-y-2">
            <a href="welcome.php" class="flex items-center px-4 py-3 rounded-xl active-link">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="ml-4">Back to Dashboard</span>
            </a>
            <a href="Locked Features.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                <i data-lucide="lock" class="w-5 h-5"></i>
                <span class="ml-4">Locked Features</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                <i data-lucide="key" class="w-5 h-5"></i>
                <span class="ml-4">Security Settings</span>
            </a>
        </nav>
        </div>

        <div class="text-center border-t border-gray-200/50 dark:border-slate-700 pt-4 text-sm text-gray-500 dark:text-gray-400">
            <p>RiseGen v<?php echo $app_version; ?></p>
            <a href="logout.php" class="inline-block mt-2 text-red-500 hover:text-red-400 font-medium transition">Logout ↗</a>
            <p class="text-xs mt-1">User ID: <?php echo $user_id; ?></p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">

        <header class="flex justify-between items-center px-6 py-4 bg-white/80 dark:bg-darkbg/80 backdrop-blur-md shadow-lg sticky top-0 z-20 transition-colors duration-300">
            <div class="flex items-center">
                <button id="menuToggle" onclick="toggleSidebar()" class="md:hidden mr-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    <span class="font-bold text-primary dark:text-indigo-400">Account Security Check</span>
                </h1>
            </div>
            <div class="flex items-center space-x-3">
                <button id="themeToggle" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                    <i data-lucide="sun" class="w-6 h-6"></i> 
                </button>
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold uppercase shadow-md cursor-pointer">
                    <?php echo $initials; ?>
                </div>
            </div>
        </header>

        <section class="p-4 sm:p-8 flex-1 flex items-center justify-center">

            <div class="glass p-8 sm:p-12 rounded-3xl shadow-2xl text-center max-w-2xl w-full">
                <i data-lucide="shield-check" class="w-16 h-16 mx-auto text-secondary mb-4"></i>
                <h2 class="text-4xl font-extrabold text-gray-800 dark:text-gray-100 mb-2">Welcome Back, <?php echo $username; ?>!</h2>
                <p class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-8">
                    Your account security is our priority.
                </p>

                <div class="bg-indigo-50 dark:bg-slate-800 p-6 rounded-xl mb-10 border-l-4 border-primary">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 mr-2 text-primary"></i> Last Successful Login Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left text-gray-700 dark:text-gray-300 text-sm">
                        <p><strong><i data-lucide="calendar" class="w-4 h-4 inline mr-2 text-indigo-500"></i> Time:</strong> <?php echo htmlspecialchars($last_login['timestamp']); ?></p>
                        <p><strong><i data-lucide="map-pin" class="w-4 h-4 inline mr-2 text-indigo-500"></i> Location:</strong> <?php echo htmlspecialchars($last_login['location']); ?></p>
                        <p><strong><i data-lucide="globe" class="w-4 h-4 inline mr-2 text-indigo-500"></i> IP Address:</strong> <?php echo htmlspecialchars($last_login['ip_address']); ?></p>
                        <p><strong><i data-lucide="monitor" class="w-4 h-4 inline mr-2 text-indigo-500"></i> Device/OS:</strong> <?php echo htmlspecialchars($last_login['device']); ?></p>
                    </div>
                </div>

                <p class="text-sm text-red-500 dark:text-red-400 mb-6">
                    If this login information seems incorrect or suspicious, please proceed to your **Profile** and immediately change your password.
                </p>

                <a href="welcome.php" class="inline-block w-full">
                    <button class="bg-primary hover:bg-indigo-700 text-white font-bold py-4 px-10 rounded-full text-lg transition shadow-xl shadow-primary/50 transform hover:scale-[1.02] w-full">
                        Proceed to Dashboard <i data-lucide="layout-dashboard" class="w-5 h-5 inline-block ml-3"></i>
                    </button>
                </a>
            </div>

        </section>

        <footer class="p-4 sm:p-6 text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-slate-700 bg-white/50 dark:bg-darkbg/50 backdrop-blur-sm">
            <p class="text-sm">
                &copy; <?php echo date("Y"); ?> RiseGen Premium Dashboard. All rights reserved.
                | Built for You, Secure by Design
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
document.getElementById('mobileOverlay').addEventListener('click', function() {
    toggleSidebar();
});


// --- Theme Toggle Function (Refined) ---
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

// No external logout-protection.js needed for this mockup
</script>

</body>
</html>