<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$app_version = "1.1.1";

// Connect DB
$pdo = connectDatabase();

// 1. Fetch summary data for past 30 days
$sql = "
  SELECT summary_date, total_study_minutes, quizzes_taken
  FROM user_daily_summary
  WHERE user_id = :uid
    AND summary_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 29 DAY) AND CURDATE()
  ORDER BY summary_date ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$study = [];
$quizzes = [];
foreach ($rows as $r) {
    $dates[] = $r['summary_date'];
    $study[] = (int)$r['total_study_minutes'];
    $quizzes[] = (int)$r['quizzes_taken'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RiseGen Dashboard</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#4f46e5',
            secondary: '#10b981',
            darkbg: '#0f172a',
            darkcard: 'rgba(30,41,59,0.7)'
          }
        }
      }
    };
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; transition: background-color 0.3s, color 0.3s; }
    body:not(.dark) { background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%); }
    .dark body { background: #0f172a; }
    .glass {
      background: rgba(255,255,255,0.6);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255,255,255,0.4);
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .dark .glass {
      background: rgba(30,41,59,0.7);
      border: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .dark .card:hover {
      box-shadow: 0 10px 20px rgba(0,0,0,0.4);
    }
    .active-link {
      background: linear-gradient(90deg, #4f46e5, #6366f1);
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(79,70,229,0.3);
    }
  </style>
</head>
<body class="text-gray-800 dark:text-gray-100 min-h-screen">
  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>
  <div class="flex h-screen overflow-hidden">
    <aside id="sidebar" class="glass fixed md:static inset-y-0 left-0 w-64 p-6 flex flex-col justify-between z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
      <div>
        <div class="text-center mb-10">
          <i data-lucide="graduation-cap" class="w-8 h-8 mx-auto text-primary"></i>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">RiseGen</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Future-Proof Learning</p>
        </div>
        <nav class="space-y-2">
          <a href="#" class="flex items-center px-4 py-3 rounded-xl active-link">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i><span class="ml-4">Dashboard</span>
          </a>
          <a href="course.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="graduation-cap" class="w-5 h-5"></i><span class="ml-4">Course</span>
          </a>
          <a href="blogs.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="newspaper" class="w-5 h-5"></i><span class="ml-4">Blogs</span>
          </a>
          <a href="advanced-mcq-generator.html" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="brain" class="w-5 h-5"></i><span class="ml-4">Smart MCQ Test</span>
          </a>
            
             <a href="advanced-mcq-generator-v2.html " class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="brain" class="w-5 h-5"></i><span class="ml-4">Smart learning</span>
          </a>
          <a href="https://risegen.onrender.com/" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i><span class="ml-4">Jobs</span>
          </a>
          <a href="instructor.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="mic" class="w-5 h-5"></i><span class="ml-4">Instructor</span>
          </a>
          <a href="credit.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="credit-card" class="w-5 h-5"></i><span class="ml-4">Buy Credits</span>
          </a>
          <a href="profile.php" class="flex items-center px-4 py-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-700/50">
            <i data-lucide="user" class="w-5 h-5"></i><span class="ml-4">Profile</span>
          </a>
        </nav>
      </div>
      <div class="text-center border-t border-gray-200/50 dark:border-slate-700 pt-4 text-sm text-gray-500 dark:text-gray-400">
        <p>RiseGen v<?= htmlspecialchars($app_version) ?></p>
        <a href="logout.php" class="mt-2 inline-block text-red-500 hover:text-red-400 font-medium">Logout ↗</a>
        <p class="text-xs mt-1">User ID: <?= htmlspecialchars($user_id) ?></p>
      </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">
      <header class="flex justify-between items-center px-6 py-4 bg-white/80 dark:bg-darkbg/80 backdrop-blur-md shadow-lg sticky top-0 z-20 transition-colors duration-300">
        <div class="flex items-center">
          <button id="menuToggle" onclick="toggleSidebar()" class="md:hidden mr-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition">
            <i data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100">
            Welcome back, <span class="font-bold text-primary dark:text-indigo-400"><?= htmlspecialchars($username) ?></span>!
          </h1>
          <span class="hidden sm:inline text-xs ml-3 bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-3 py-1 rounded-full font-medium">Premium User</span>
        </div>
        <div class="flex items-center space-x-3">
          <button id="themeToggle" class="p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
            <i data-lucide="sun" class="w-6 h-6"></i>
          </button>
          <a href="notification.php" title="Notifications">
            <div class="relative p-2 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
              <i data-lucide="bell" class="w-6 h-6"></i>
              <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full animate-ping"></span>
              <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
            </div>
          </a>
          <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold uppercase shadow-md cursor-pointer">
            <?= htmlspecialchars(substr($username, 0, 1)) ?>
          </div>
        </div>
      </header>

      <section class="p-4 sm:p-8 space-y-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="card glass p-6 rounded-3xl text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Courses Enrolled</p>
            <p class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2"><?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE user_id = ?");
            $stmt->execute([$user_id]);
            echo $stmt->fetchColumn();
          ?></p>
            <p class="text-xs text-green-500 font-semibold mt-1">▲ 12% growth</p>
          </div>
          <div class="card glass p-6 rounded-3xl text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Blogs Read</p>
            <p class="text-4xl font-extrabold text-green-500 dark:text-green-400 mt-2"><?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
            echo $stmt->fetchColumn();
          ?></p>
            <p class="text-xs text-green-500 font-semibold mt-1">▲ 8% growth</p>
          </div>
          <div class="card glass p-6 rounded-3xl text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Skills Mastered</p>
            <p class="text-4xl font-extrabold text-yellow-500 dark:text-yellow-400 mt-2"><?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM test_results WHERE username = (SELECT username FROM users WHERE id = ?)");
            $stmt->execute([$user_id]);
            echo $stmt->fetchColumn();
          ?></p>
            <p class="text-xs text-red-500 font-semibold mt-1">▼ 2% dip</p>
          </div>
          <div class="card glass p-6 rounded-3xl text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Learning Streak</p>
            <p class="text-4xl font-extrabold text-pink-500 dark:text-pink-400 mt-2">27 🔥</p>
            <p class="text-xs text-green-500 font-semibold mt-1">High Score!</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-8">
            <div class="glass p-6 rounded-2xl h-[400px] shadow-xl flex flex-col">
              <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-slate-700 pb-3">Monthly Learning Overview</h2>
              <div class="flex-grow">
                <canvas id="learningChart"></canvas>
              </div>
            </div>
            <div class="glass p-6 rounded-2xl shadow-xl">
              <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-slate-700 pb-3 mb-4">Your Active Courses</h2>
              <div class="grid sm:grid-cols-2 gap-4">
                <div class="card p-4 rounded-xl bg-white dark:bg-slate-700/50 shadow-md border border-gray-100 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-slate-700 transition cursor-pointer">
                  <h3 class="font-bold text-indigo-700 dark:text-indigo-300 text-lg">AI Fundamentals</h3>
                  <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Progress: <span class="font-semibold">80%</span></div>
                  <div class="mt-2 w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 80%"></div>
                  </div>
                </div>
                <div class="card p-4 rounded-xl bg-white dark:bg-slate-700/50 shadow-md border border-gray-100 dark:border-slate-700 hover:bg-green-50 dark:hover:bg-slate-700 transition cursor-pointer">
                  <h3 class="font-bold text-green-700 dark:text-green-300 text-lg">Web Development</h3>
                  <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Progress: <span class="font-semibold">45%</span></div>
                  <div class="mt-2 w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 45%"></div>
                  </div>
                </div>
              </div>
              <button class="mt-4 w-full text-center text-indigo-600 dark:text-indigo-400 font-medium py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 transition">
                View All Courses ❯
              </button>
            </div>
          </div>
          <div class="space-y-8">
            <div class="glass p-6 rounded-2xl shadow-xl">
              <h2 class="text-xl font-bold mb-3 border-b border-gray-200 dark:border-slate-700 pb-3 text-gray-800 dark:text-gray-100">Upcoming Events</h2>
              <ul class="space-y-4 text-sm">
                <li class="hover:text-primary transition cursor-pointer">🎤 <b>Webinar:</b> Future of AI — <span class="text-gray-500 dark:text-gray-400 font-semibold">Oct 12</span></li>
                <li class="hover:text-primary transition cursor-pointer">🧩 <b>Challenge:</b> ReactJS Skills — <span class="text-gray-500 dark:text-gray-400 font-semibold">Oct 14</span></li>
                <li class="hover:text-primary transition cursor-pointer">🚀 <b>Hackathon 2025</b> — <span class="text-gray-500 dark:text-gray-400 font-semibold">Oct 20</span></li>
              </ul>
            </div>
            <div class="glass p-6 rounded-2xl shadow-xl">
              <h2 class="text-xl font-bold mb-3 border-b border-gray-200 dark:border-slate-700 pb-3 text-gray-800 dark:text-gray-100">Recent Insights</h2>
              <ul class="space-y-3 text-sm">
                <li class="hover:text-primary transition cursor-pointer">📘 Understanding Neural Networks</li>
                <li class="hover:text-primary transition cursor-pointer">🧠 Productivity for Developers</li>
                <li class="hover:text-primary transition cursor-pointer">🌍 Global Tech Trends 2025</li>
              </ul>
              <button class="mt-4 w-full text-center text-indigo-600 dark:text-indigo-400 font-medium py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800 transition">
                Browse More Blogs ❯
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    let chartInstance = null;

    function renderChart() {
      const ctx = document.getElementById('learningChart');
      if (!ctx) return;
      if (chartInstance) {
        chartInstance.destroy();
      }

      const labels = <?= json_encode($dates) ?>;
      const studyData = <?= json_encode($study) ?>;
      const quizData = <?= json_encode($quizzes) ?>;

      const isDark = document.documentElement.classList.contains('dark');
      const textColor = isDark ? '#f3f4f6' : '#1f2937';
      const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

      chartInstance = new Chart(ctx, {
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Study Minutes',
              data: studyData,
              backgroundColor: 'rgba(79,70,229,0.8)',
              borderColor: '#4f46e5',
              borderWidth: 1,
              yAxisID: 'y',
              borderRadius: 8
            },
            {
              label: 'Quizzes Taken',
              data: quizData,
              type: 'line',
              tension: 0.4,
              borderColor: '#10b981',
              backgroundColor: 'rgba(16,185,129,0.8)',
              borderWidth: 1,
              yAxisID: 'y1',
              pointRadius: 5
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: textColor }
            }
          },
          scales: {
            x: { grid: { display: false }, ticks: { color: textColor } },
            y: {
              type: 'linear',
              position: 'left',
              title: { display: true, text: 'Minutes', color: '#4f46e5' },
              grid: { color: gridColor },
              ticks: { color: textColor }
            },
            y1: {
              type: 'linear',
              position: 'right',
              title: { display: true, text: 'Quizzes', color: '#10b981' },
              grid: { display: false },
              ticks: { color: textColor }
            }
          }
        }
      });
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobileOverlay');
      const closed = sidebar.classList.contains('-translate-x-full');
      if (closed) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      }
    }

    function toggleTheme() {
      const html = document.documentElement;
      html.classList.toggle('dark');
      const isDark = html.classList.contains('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      const icon = document.querySelector('#themeToggle i');
      if (icon) {
        icon.setAttribute('data-lucide', isDark ? 'moon' : 'sun');
        lucide.createIcons();
      }
      renderChart();
    }

    window.onload = () => {
      const saved = localStorage.getItem('theme');
      const useDark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
      if (useDark) document.documentElement.classList.add('dark');
      lucide.createIcons();
      const icon = document.querySelector('#themeToggle i');
      if (icon) {
        icon.setAttribute('data-lucide', document.documentElement.classList.contains('dark') ? 'moon' : 'sun');
        lucide.createIcons();
      }
      renderChart();
      document.getElementById('themeToggle').addEventListener('click', toggleTheme);
      document.getElementById('menuToggle').addEventListener('click', toggleSidebar);
    };

    window.addEventListener('resize', () => {
      renderChart();
    });
  </script>

  <script src="js/logout-protection.js"></script>
</body>
</html>
