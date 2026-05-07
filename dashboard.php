<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
$pdo = connectDatabase();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Real DB counts
$enrolled_count = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE user_id = ?");
$enrolled_count->execute([$user_id]);
$courses = $enrolled_count->fetchColumn();

$tests_count = $pdo->prepare("SELECT COUNT(*) FROM test_results WHERE username = (SELECT username FROM users WHERE id = ?)");
$tests_count->execute([$user_id]);
$tests = $tests_count->fetchColumn();

$jobs_count = $pdo->prepare("SELECT COUNT(*) FROM saved_jobs WHERE user_id = ?");
$jobs_count->execute([$user_id]);
$jobs = $jobs_count->fetchColumn();

$blogs_count = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RiseGen User Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <style>
    :root { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- 🔝 Navbar -->
  <header class="bg-indigo-600 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold tracking-tight">RiseGen Dashboard</h1>
      <div class="flex items-center space-x-4">
        <span class="text-sm">👋 Hi, <strong><?php echo htmlspecialchars($username); ?></strong></span>
        <a href="logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md transition">
          Logout
        </a>
      </div>
    </div>
  </header>

  <!-- 🧭 Main Layout -->
  <div class="flex flex-1 overflow-hidden">

    <!-- Sidebar -->
    <aside class="bg-white w-64 border-r border-gray-200 p-4 hidden md:block">
      <nav class="space-y-3">
        <a href="dashboard.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-medium">
          <i class="ph-fill ph-house-line"></i><span>Dashboard</span>
        </a>
        <a href="course.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-book-open"></i><span>Courses</span>
        </a>
        <a href="gamebox.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-list-checks"></i><span>Take Exam</span>
        </a>
        <a href="https://risegen.onrender.com/" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-briefcase"></i><span>Jobs</span>
        </a>
        <a href="blogs.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-newspaper"></i><span>Blog</span>
        </a>
        <a href="leaderboard.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-trophy"></i><span>Leaderboard</span>
        </a>
        <a href="search.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-magnifying-glass"></i><span>Search</span>
        </a>
        <a href="contact.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-headset"></i><span>Support</span>
        </a>
        <a href="profile.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
          <i class="ph-fill ph-user"></i><span>My Profile</span>
        </a>
      </nav>
    </aside>

    <!-- 📊 Main Content -->
    <main class="flex-1 overflow-y-auto p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Overview</h2>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Courses -->
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-indigo-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-700">Courses</h3>
            <i class="ph-fill ph-book-open text-indigo-500 text-2xl"></i>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?php echo $courses; ?></p>
          <p class="text-sm text-gray-500 mt-1">Active courses</p>
        </div>

        <!-- Tests -->
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-green-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-700">Tests</h3>
            <i class="ph-fill ph-list-checks text-green-500 text-2xl"></i>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?php echo $tests; ?></p>
          <p class="text-sm text-gray-500 mt-1">Completed tests</p>
        </div>

        <!-- Jobs -->
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-yellow-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-700">Jobs</h3>
            <i class="ph-fill ph-briefcase text-yellow-500 text-2xl"></i>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?php echo $jobs; ?></p>
          <p class="text-sm text-gray-500 mt-1">Active applications</p>
        </div>

        <!-- Blogs -->
        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-red-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-700">Blogs</h3>
            <i class="ph-fill ph-newspaper text-red-500 text-2xl"></i>
          </div>
          <p class="text-3xl font-bold text-gray-900"><?php echo $blogs_count; ?></p>
          <p class="text-sm text-gray-500 mt-1">Published blogs</p>
        </div>

      </div>

      <!-- Quick Access -->
      <div class="mt-10">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Access</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

          <a href="course.php" class="bg-indigo-50 hover:bg-indigo-100 p-5 rounded-xl shadow flex items-center justify-between transition">
            <span class="font-semibold text-indigo-700">Explore Courses</span>
            <i class="ph-fill ph-arrow-right text-indigo-500"></i>
          </a>

          <a href="gamebox.php" class="bg-green-50 hover:bg-green-100 p-5 rounded-xl shadow flex items-center justify-between transition">
            <span class="font-semibold text-green-700">Take Exam</span>
            <i class="ph-fill ph-arrow-right text-green-500"></i>
          </a>

          <a href="leaderboard.php" class="bg-yellow-50 hover:bg-yellow-100 p-5 rounded-xl shadow flex items-center justify-between transition">
            <span class="font-semibold text-yellow-700">Leaderboard</span>
            <i class="ph-fill ph-arrow-right text-yellow-500"></i>
          </a>

        </div>
      </div>
    </main>
  </div>

  <!-- Footer -->
  <footer class="bg-white text-gray-500 text-center py-3 text-sm border-t">
    © 2025 RiseGen Platform — Version 1.0.0
  </footer>

<script src="js/logout-protection.js"></script>
</body>
</html>
