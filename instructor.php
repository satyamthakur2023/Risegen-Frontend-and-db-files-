<?php
// ========================================================
// RiseGen Premium Dashboard (v6.0) - Instructor Page
// ========================================================

// --------------------------------------------------------
// 1. Configuration & Security
// --------------------------------------------------------
session_start();

// Security Headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// --------------------------------------------------------
// 2. Data Simulation/API Layer
// --------------------------------------------------------

/**
 * Simulates fetching logged-in user and instructor status.
 * In a real application, this would involve database queries.
 *
 * @param string $session_username
 * @param string $session_user_id
 * @return array{username: string, user_id: string, is_current_instructor: bool}
 */
function get_user_status(string $session_username, string $session_user_id): array {
    // SECURITY NOTE: This flag determines the page content.
    // In production, this MUST come from a secure DB lookup based on $session_user_id.
    $is_instructor_in_db = true; // Set to true for Dr. Elara Vance profile
    
    return [
        'username' => htmlspecialchars($session_username),
        'user_id' => htmlspecialchars($session_user_id),
        'is_current_instructor' => $is_instructor_in_db,
    ];
}

/**
 * Fetches mock instructor profile data.
 * @return array<string, mixed>
 */
function get_instructor_data(): array {
    return [
        'name' => "Dr. Elara Vance",
        'title' => "Lead AI/ML Scientist",
        'bio' => "Dr. Vance is a recognized expert in neural networks and natural language processing. With over 15 years in both academia and industry, she is passionate about making complex AI concepts accessible to all learners.",
        'rating' => 4.9,
        'total_students' => 15400,
        'total_courses' => 5,
        'courses' => [
            ['name' => 'Advanced Deep Learning', 'students' => '3,200', 'rating' => 4.8, 'progress' => 85, 'color' => 'indigo'],
            ['name' => 'Python for Data Science', 'students' => '6,100', 'rating' => 4.9, 'progress' => 60, 'color' => 'green'],
            ['name' => 'Natural Language Processing', 'students' => '1,800', 'rating' => 5.0, 'progress' => 100, 'color' => 'yellow'],
        ],
        'testimonials' => [
            ['name' => 'Alex M.', 'feedback' => 'Her explanations of complex algorithms are unmatched. Highly recommend!'],
            ['name' => 'Sarah K.', 'feedback' => 'I landed my dream job directly because of her deep learning course.'],
            ['name' => 'Jian L.', 'feedback' => 'The projects were incredibly practical and relevant to the industry.'],
        ],
    ];
}


// --------------------------------------------------------
// 3. Core Logic Execution
// --------------------------------------------------------

$app_version = "6.0.0";

// Fetch user session data
$session_username = $_SESSION['username'] ?? "RiseGen User";
$session_user_id = $_SESSION['user_id'] ?? "U948572";

// Get user status and apply sanitization
$user_status = get_user_status($session_username, $session_user_id);
$username = $user_status['username'];
$user_id = $user_status['user_id'];
$is_current_instructor = $user_status['is_current_instructor'];

// Fetch instructor data if applicable
$instructor_data = $is_current_instructor ? get_instructor_data() : [];

// Determine Header Title
$page_title = $is_current_instructor 
    ? "Instructor Profile: " . htmlspecialchars($instructor_data['name'])
    : "Become an Instructor";

// Helper function to extract initials from a full name (for avatar)
function get_initials(string $name): string {
    $parts = explode(' ', trim($name));
    $initials = '';
    // Use the first letter of the first part, and the first letter of the last part.
    if (count($parts) > 0) {
        $initials .= strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
        }
    }
    return $initials;
}

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
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .dark .card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
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
            <a href="welcome.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="ml-4">Dashboard</span>
            </a>
            <a href="course.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                <span class="ml-4">Course</span>
            </a>
            <a href="blogs.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="newspaper" class="w-5 h-5"></i>
                <span class="ml-4">Blogs</span>
            </a>
            <a href="https://risegen.onrender.com/" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                <span class="ml-4">Jobs Search</span>
            </a>
            <a href="advanced-mcq-generator.html" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span class="ml-4">Smart MCQ Test</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl active-link">
                <i data-lucide="mic" class="w-5 h-5"></i>
                <span class="ml-4">Instructor</span>
            </a>
            <a href="profile.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="ml-4">Profile</span>
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
                    <?php if ($is_current_instructor): ?>
                        Instructor: <span class="font-bold text-primary dark:text-indigo-400"><?php echo htmlspecialchars($instructor_data['name']); ?></span>
                    <?php else: ?>
                        <span class="font-bold text-secondary dark:text-green-400">Instructor Hub</span>
                    <?php endif; ?>
                </h1>
            </div>
            <div class="flex items-center space-x-3">
                <button id="themeToggle" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                    <i data-lucide="sun" class="w-6 h-6"></i> 
                </button>
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold uppercase shadow-md cursor-pointer">
                    <?php echo get_initials($username); ?>
                </div>
            </div>
        </header>

        <section class="p-4 sm:p-8 space-y-8">

            <?php if ($is_current_instructor): ?>
                
                <div class="glass p-8 rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:space-x-8">
                    <div class="flex-shrink-0 mb-4 md:mb-0">
                        <div class="w-32 h-32 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-4xl font-extrabold text-primary border-4 border-primary/50">
                            <?php echo get_initials($instructor_data['name']); ?>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?php echo htmlspecialchars($instructor_data['name']); ?></h2>
                        <p class="text-lg font-medium text-secondary dark:text-green-400 mb-4"><?php echo htmlspecialchars($instructor_data['title']); ?></p>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6"><?php echo htmlspecialchars($instructor_data['bio']); ?></p>
                        
                        <div class="flex flex-wrap gap-6">
                            <div class="text-center">
                                <i data-lucide="star" class="w-5 h-5 text-yellow-500 inline-block mr-1"></i>
                                <p class="text-xl font-bold text-primary dark:text-indigo-400"><?php echo number_format($instructor_data['rating'], 1); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Average Rating</p>
                            </div>
                            <div class="text-center">
                                <i data-lucide="users" class="w-5 h-5 text-green-500 inline-block mr-1"></i>
                                <p class="text-xl font-bold text-primary dark:text-indigo-400"><?php echo number_format($instructor_data['total_students']); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Students</p>
                            </div>
                            <div class="text-center">
                                <i data-lucide="book-open" class="w-5 h-5 text-pink-500 inline-block mr-1"></i>
                                <p class="text-xl font-bold text-primary dark:text-indigo-400"><?php echo $instructor_data['total_courses']; ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Active Courses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Courses by Dr. Vance</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($instructor_data['courses'] as $course): ?>
                            <div class="card glass p-5 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 cursor-pointer hover:border-<?php echo htmlspecialchars($course['color']); ?>-400">
                                <h3 class="font-bold text-xl text-<?php echo htmlspecialchars($course['color']); ?>-700 dark:text-<?php echo htmlspecialchars($course['color']); ?>-300 mb-2"><?php echo htmlspecialchars($course['name']); ?></h3>
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    <span><i data-lucide="star" class="w-4 h-4 inline-block text-yellow-500"></i> <?php echo $course['rating']; ?></span>
                                    <span><i data-lucide="users" class="w-4 h-4 inline-block text-primary"></i> <?php echo $course['students']; ?> Students</span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2.5">
                                    <div class="bg-<?php echo htmlspecialchars($course['color']); ?>-600 h-2.5 rounded-full" style="width: <?php echo $course['progress']; ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Completion: <?php echo $course['progress']; ?>%</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Student Testimonials</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($instructor_data['testimonials'] as $review): ?>
                            <div class="card glass p-5 rounded-xl shadow-lg border-l-4 border-secondary dark:border-green-500">
                                <p class="italic text-gray-700 dark:text-gray-200 mb-3">"<?php echo htmlspecialchars($review['feedback']); ?>"</p>
                                <p class="text-sm font-semibold text-secondary dark:text-green-400">- <?php echo htmlspecialchars($review['name']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="glass p-6 rounded-2xl shadow-xl text-center">
                    <h3 class="text-xl font-bold mb-3">Ready to Ask a Question?</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">You can connect with the instructor on the course Q&A board.</p>
                    <button class="bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg shadow-primary/50">
                        View Q&A Forum <i data-lucide="message-square" class="w-5 h-5 inline-block ml-2"></i>
                    </button>
                </div>

            <?php else: ?>
                <div class="glass p-8 rounded-3xl shadow-2xl text-center border-2 border-dashed border-primary/50">
                    <i data-lucide="mic-2" class="w-12 h-12 mx-auto text-primary mb-4"></i>
                    <h2 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mb-2">Become a RiseGen Instructor</h2>
                    <p class="text-xl font-semibold text-secondary dark:text-green-400 mb-6">Guide the Next Generation of Learners.</p>
                    
                    <p class="max-w-3xl mx-auto text-gray-600 dark:text-gray-300 mb-8">
                        Ready to share your expertise? Join our elite team and create future-proof content. To ensure content quality, all applicants must pass our Specialized Instructor Assignment Test.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="p-4 bg-indigo-50 dark:bg-slate-800 rounded-xl">
                            <i data-lucide="award" class="w-6 h-6 text-primary mb-2"></i>
                            <p class="font-bold">1. Pass the Test</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Prove your subject mastery.</p>
                        </div>
                        <div class="p-4 bg-indigo-50 dark:bg-slate-800 rounded-xl">
                            <i data-lucide="monitor-check" class="w-6 h-6 text-primary mb-2"></i>
                            <p class="font-bold">2. Submit a Demo</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Showcase your teaching style.</p>
                        </div>
                        <div class="p-4 bg-indigo-50 dark:bg-slate-800 rounded-xl">
                            <i data-lucide="rocket" class="w-6 h-6 text-primary mb-2"></i>
                            <p class="font-bold">3. Launch Your Course</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Start earning and building your brand!</p>
                        </div>
                    </div>

                    <a href="instructor_test.php" class="inline-block">
                        <button class="bg-secondary hover:bg-green-600 text-white font-bold py-4 px-10 rounded-full text-lg transition shadow-xl shadow-secondary/50 transform hover:scale-[1.02]">
                            Start Instructor Assignment Test Now <i data-lucide="arrow-right" class="w-5 h-5 inline-block ml-3"></i>
                        </button>
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Estimated Time: 60 minutes. Requires 90% accuracy.</p>
                </div>
                
                <div class="glass p-6 rounded-2xl shadow-xl">
                    <h3 class="text-xl font-bold mb-3 border-b border-gray-200 dark:border-slate-700 pb-2">Why Teach on RiseGen?</h3>
                    <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                        <li class="flex items-center"><i data-lucide="trending-up" class="w-5 h-5 text-indigo-500 mr-3"></i> Competitive Royalties: Earn top industry rates for your content.</li>
                        <li class="flex items-center"><i data-lucide="globe" class="w-5 h-5 text-indigo-500 mr-3"></i>Global Audience: Reach thousands of eager students worldwide.</li>
                        <li class="flex items-center"><i data-lucide="book-mark" class="w-5 h-5 text-indigo-500 mr-3"></i> Dedicated Support: Get help with marketing and technical setup.</li>
                    </ul>
                </div>
            <?php endif; ?>

        </section>

        <footer class="mt-auto p-4 sm:p-6 text-center text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-slate-700 bg-white/50 dark:bg-darkbg/50 backdrop-blur-sm">
            <p class="text-sm">
                &copy; <?php echo date("Y"); ?> RiseGen Premium Dashboard. All rights reserved.
                | Made with Future, Built for You 
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

// Assuming js/logout-protection.js is a valid, existing script
// <script src="js/logout-protection.js"></script>
</script>

</body>
</html>