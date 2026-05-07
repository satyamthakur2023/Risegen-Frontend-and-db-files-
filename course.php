<?php
session_start();

// Session protection
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Learner';
$user_id = $_SESSION['user_id'] ?? 1;
// Advanced course data with correct picture alignment
$courses = [
    [
        'id' => 1,
        'title' => 'Full Stack Web Development',
        'desc' => 'Master HTML, CSS, JS, React, Node.js and build dynamic web apps.',
        'level' => 'Intermediate',
        'rating' => '4.8',
        'time' => '8h 30m',
        'cat' => 'development',
        'instructor' => 'John Parker',
        'img' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80',
        'price' => 299,
        'enrolled' => 1250,
        'progress' => 65
    ],
    [
        'id' => 2,
        'title' => 'Machine Learning Basics',
        'desc' => 'Understand algorithms, train models, and deploy ML apps with Python.',
        'level' => 'Advanced',
        'rating' => '4.9',
        'time' => '10h 0m',
        'cat' => 'ai',
        'instructor' => 'Dr. Aisha Khan',
        'img' => 'https://images.unsplash.com/photo-1581090700227-1e37b190418e?auto=format&fit=crop&w=800&q=80',
        'price' => 399,
        'enrolled' => 890,
        'progress' => 0
    ],
    [
        'id' => 3,
        'title' => 'UI/UX Design for Beginners',
        'desc' => 'Learn Figma, typography, wireframing, and design psychology.',
        'level' => 'Beginner',
        'rating' => '4.6',
        'time' => '6h 45m',
        'cat' => 'design',
        'instructor' => 'Elena Rose',
        'img' => 'https://images.unsplash.com/photo-1590608897129-79c9d9f7b6f1?auto=format&fit=crop&w=800&q=80',
        'price' => 199,
        'enrolled' => 2100,
        'progress' => 100
    ],
    [
        'id' => 4,
        'title' => 'Entrepreneurship Essentials',
        'desc' => 'Learn how to start and scale your business with proven models.',
        'level' => 'Intermediate',
        'rating' => '4.7',
        'time' => '5h 20m',
        'cat' => 'business',
        'instructor' => 'Michael Stone',
        'img' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=800&q=80',
        'price' => 249,
        'enrolled' => 567,
        'progress' => 30
    ],
    [
        'id' => 5,
        'title' => 'Data Science with Python',
        'desc' => 'Dive into data visualization, cleaning, and statistical analysis.',
        'level' => 'Advanced',
        'rating' => '4.9',
        'time' => '12h 15m',
        'cat' => 'ai',
        'instructor' => 'Dr. Lin Wei',
        'img' => 'https://images.unsplash.com/photo-1581091012184-9d6c6a2333d3?auto=format&fit=crop&w=800&q=80',
        'price' => 449,
        'enrolled' => 1890,
        'progress' => 0
    ],
    [
        'id' => 6,
        'title' => 'Digital Marketing Strategy',
        'desc' => 'Master SEO, SEM, and social media advertising for maximum reach.',
        'level' => 'Beginner',
        'rating' => '4.5',
        'time' => '7h 0m',
        'cat' => 'business',
        'instructor' => 'Sarah Lee',
        'img' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
        'price' => 179,
        'enrolled' => 3200,
        'progress' => 85
    ]
];

$totalCourses = count($courses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>RiseGen | Advanced Courses</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<style>
body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1f2937; }
.card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); transition: all 0.3s ease; }
.progress-bar { background: linear-gradient(90deg, #2563eb, #1d4ed8); }
.filter-btn.active { background: #2563eb; color: white; }
.filter-btn:hover { background: #3b82f6; color: white; }
</style>
</head>
<body class="min-h-screen bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-sm fixed w-full z-50 border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a href="welcome.php" class="text-blue-600 font-bold text-xl sm:text-2xl flex items-center">
        <i data-lucide="graduation-cap" class="w-6 h-6 sm:w-8 sm:h-8 mr-2"></i>RiseGen
      </a>
      
      <!-- Mobile menu button -->
      <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>
      
      <!-- Desktop menu -->
      <div class="hidden md:flex items-center space-x-4">
        <div class="relative">
          <input type="text" id="searchInput" placeholder="Search courses..." class="w-64 bg-gray-100 text-gray-700 placeholder-gray-500 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white border border-gray-200">
          <i data-lucide="search" class="absolute right-3 top-2.5 w-4 h-4 text-gray-400"></i>
        </div>
        <div class="flex items-center space-x-2 text-gray-700">
          <i data-lucide="user" class="w-4 h-4"></i>
          <span class="text-sm font-medium"><?= htmlspecialchars($username) ?></span>
        </div>
        <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
          <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>Logout
        </a>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden pb-4">
      <div class="space-y-3">
        <div class="relative">
          <input type="text" id="mobileSearchInput" placeholder="Search courses..." class="w-full bg-gray-100 text-gray-700 placeholder-gray-500 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i data-lucide="search" class="absolute right-3 top-2.5 w-4 h-4 text-gray-400"></i>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2 text-gray-700">
            <i data-lucide="user" class="w-4 h-4"></i>
            <span class="text-sm font-medium"><?= htmlspecialchars($username) ?></span>
          </div>
          <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition">
            <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>Logout
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="pt-20 sm:pt-24 pb-12 sm:pb-16 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">Learn Without <span class="text-blue-600">Limits</span></h1>
    <p class="text-lg sm:text-xl mb-8 text-gray-600 max-w-2xl mx-auto">Start, switch, or advance your career with <?= $totalCourses ?> courses, Professional Certificates, and degrees from world-class universities and companies.</p>
    <div class="grid grid-cols-3 gap-4 sm:gap-8 max-w-md mx-auto">
      <div class="text-center">
        <div class="text-2xl sm:text-3xl font-bold text-blue-600"><?= $totalCourses ?></div>
        <div class="text-xs sm:text-sm text-gray-500">Courses</div>
      </div>
      <div class="text-center">
        <div class="text-2xl sm:text-3xl font-bold text-blue-600">50K+</div>
        <div class="text-xs sm:text-sm text-gray-500">Students</div>
      </div>
      <div class="text-center">
        <div class="text-2xl sm:text-3xl font-bold text-blue-600">100%</div>
        <div class="text-xs sm:text-sm text-gray-500">Online</div>
      </div>
    </div>
  </div>
</section>

<!-- Filters -->
<div class="bg-gray-100 py-4 sm:py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex flex-wrap gap-2 w-full sm:w-auto">
        <button class="filter-btn px-3 sm:px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition active text-sm" data-filter="all">All</button>
        <button class="filter-btn px-3 sm:px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition text-sm" data-filter="development">Development</button>
        <button class="filter-btn px-3 sm:px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition text-sm" data-filter="ai">Data Science</button>
        <button class="filter-btn px-3 sm:px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition text-sm" data-filter="design">Design</button>
        <button class="filter-btn px-3 sm:px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition text-sm" data-filter="business">Business</button>
      </div>
      <select id="sortSelect" class="w-full sm:w-auto bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        <option value="rating">Sort by Rating</option>
        <option value="price">Sort by Price</option>
        <option value="enrolled">Most Popular</option>
      </select>
    </div>
  </div>
</div>

<!-- Courses Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="courseGrid">
    <?php foreach($courses as $c): ?>
    <div class="course-card bg-white rounded-lg overflow-hidden shadow-sm border border-gray-200 card-hover transition-all duration-300" data-cat="<?= $c['cat'] ?>" data-rating="<?= $c['rating'] ?>" data-price="<?= $c['price'] ?>" data-enrolled="<?= $c['enrolled'] ?>">
      <div class="relative h-40 sm:h-48 overflow-hidden">
        <img src="<?= $c['img'] ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover">
        <?php if($c['progress'] > 0): ?>
        <div class="absolute top-2 sm:top-3 left-2 sm:left-3 bg-green-600 text-white px-2 py-1 rounded text-xs font-medium">
          <?= $c['progress'] ?>% Complete
        </div>
        <?php endif; ?>
        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-black/70 text-white px-2 py-1 rounded text-xs flex items-center">
          <i data-lucide="star" class="w-3 h-3 mr-1 fill-yellow-400 text-yellow-400"></i><?= $c['rating'] ?>
        </div>
      </div>
      <div class="p-4 sm:p-6">
        <div class="mb-3">
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2"><?= $c['title'] ?></h3>
          <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= $c['desc'] ?></p>
        </div>
        
        <div class="flex items-center justify-between mb-3 text-xs text-gray-500 flex-wrap gap-1">
          <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded"><?= $c['level'] ?></span>
          <span class="flex items-center">
            <i data-lucide="clock" class="w-3 h-3 mr-1"></i><?= $c['time'] ?>
          </span>
          <span class="flex items-center">
            <i data-lucide="users" class="w-3 h-3 mr-1"></i><?= number_format($c['enrolled']) ?>
          </span>
        </div>
        
        <?php if($c['progress'] > 0): ?>
        <div class="mb-4">
          <div class="flex justify-between text-xs text-gray-600 mb-1">
            <span>Progress</span>
            <span><?= $c['progress'] ?>%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="progress-bar h-2 rounded-full" style="width: <?= $c['progress'] ?>%"></div>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
          <div>
            <div class="text-sm text-gray-600"><?= $c['instructor'] ?></div>
            <div class="text-lg font-bold text-blue-600">₹<?= number_format($c['price']) ?></div>
          </div>
          <button class="open-modal w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition" 
                  data-course='<?= json_encode($c) ?>'>
            <?= $c['progress'] > 0 ? 'Continue' : 'Enroll Now' ?>
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Course Modal -->
<div id="courseModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50 p-4">
  <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="relative">
      <img id="modalImg" class="w-full h-48 sm:h-64 object-cover" src="">
      <button onclick="closeModal()" class="absolute top-4 right-4 bg-white/90 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center hover:bg-white transition">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>
    <div class="p-6 sm:p-8">
      <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-2">
        <h2 id="modalTitle" class="text-xl sm:text-2xl font-bold text-gray-900"></h2>
        <span id="modalPrice" class="text-xl sm:text-2xl font-bold text-blue-600"></span>
      </div>
      <p id="modalDesc" class="text-gray-600 mb-6"></p>
      
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="text-gray-500 text-sm">Instructor</div>
          <div id="modalInstructor" class="font-semibold text-gray-900"></div>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="text-gray-500 text-sm">Duration</div>
          <div id="modalTime" class="font-semibold text-gray-900"></div>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="text-gray-500 text-sm">Level</div>
          <div id="modalLevel" class="font-semibold text-gray-900"></div>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="text-gray-500 text-sm">Students</div>
          <div id="modalEnrolled" class="font-semibold text-gray-900"></div>
        </div>
      </div>
      
     <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
    <a href="enrolled.php" class="flex-1">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition w-full">
            <i data-lucide="play" class="w-4 h-4 inline mr-2"></i>Enroll Now
        </button>
    </a>
</div>
        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition">
          <i data-lucide="bookmark" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8 sm:py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8">
      <div class="col-span-2 md:col-span-1">
        <h3 class="font-bold text-lg mb-4">RiseGen</h3>
        <p class="text-gray-400 text-sm">Empowering learners worldwide with quality education and professional development.</p>
      </div>
      <div>
        <h4 class="font-semibold mb-4">Courses</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="#" class="hover:text-white transition">Web Development</a></li>
          <li><a href="#" class="hover:text-white transition">Data Science</a></li>
          <li><a href="#" class="hover:text-white transition">Design</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-4">Support</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="#" class="hover:text-white transition">Help Center</a></li>
          <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-4">Company</h4>
        <ul class="space-y-2 text-sm text-gray-400">
          <li><a href="#" class="hover:text-white transition">About</a></li>
          <li><a href="#" class="hover:text-white transition">Careers</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center text-gray-400 text-sm">
      <p>&copy; <?= date('Y') ?> RiseGen. All rights reserved.</p>
    </div>
  </div>
</footer>

<script>
// Initialize Lucide icons
lucide.createIcons();

// Mobile menu toggle
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');

mobileMenuBtn.addEventListener('click', () => {
  mobileMenu.classList.toggle('hidden');
});

// Advanced filtering and search
const filterBtns = document.querySelectorAll('.filter-btn');
const courseCards = document.querySelectorAll('.course-card');
const searchInput = document.getElementById('searchInput');
const mobileSearchInput = document.getElementById('mobileSearchInput');
const sortSelect = document.getElementById('sortSelect');

// Sync search inputs
searchInput.addEventListener('input', (e) => {
  mobileSearchInput.value = e.target.value;
  filterCourses();
});

mobileSearchInput.addEventListener('input', (e) => {
  searchInput.value = e.target.value;
  filterCourses();
});

// Filter functionality
filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active', 'bg-blue-600', 'text-white'));
    btn.classList.add('active', 'bg-blue-600', 'text-white');
    filterCourses();
  });
});

sortSelect.addEventListener('change', sortCourses);

function filterCourses() {
  const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
  const searchTerm = searchInput.value.toLowerCase();
  
  courseCards.forEach(card => {
    const category = card.dataset.cat;
    const title = card.querySelector('h3').textContent.toLowerCase();
    const instructor = card.querySelector('.text-gray-600').textContent.toLowerCase();
    
    const matchesFilter = activeFilter === 'all' || category === activeFilter;
    const matchesSearch = title.includes(searchTerm) || instructor.includes(searchTerm);
    
    card.style.display = matchesFilter && matchesSearch ? 'block' : 'none';
  });
}

function sortCourses() {
  const sortBy = sortSelect.value;
  const grid = document.getElementById('courseGrid');
  const cards = Array.from(courseCards);
  
  cards.sort((a, b) => {
    const aVal = parseFloat(a.dataset[sortBy]);
    const bVal = parseFloat(b.dataset[sortBy]);
    return sortBy === 'price' ? aVal - bVal : bVal - aVal;
  });
  
  cards.forEach(card => grid.appendChild(card));
}

// Enhanced Modal
const modal = document.getElementById('courseModal');
document.querySelectorAll('.open-modal').forEach(btn => {
  btn.addEventListener('click', () => {
    const course = JSON.parse(btn.dataset.course);
    
    document.getElementById('modalTitle').textContent = course.title;
    document.getElementById('modalDesc').textContent = course.desc;
    document.getElementById('modalInstructor').textContent = course.instructor;
    document.getElementById('modalTime').textContent = course.time;
    document.getElementById('modalLevel').textContent = course.level;
    document.getElementById('modalPrice').textContent = '₹' + course.price.toLocaleString();
    document.getElementById('modalEnrolled').textContent = course.enrolled.toLocaleString();
    document.getElementById('modalImg').src = course.img;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
  });
});

function closeModal() {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.style.overflow = 'auto';
}

// Close modal on outside click
modal.addEventListener('click', (e) => {
  if (e.target === modal) closeModal();
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
  if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
    mobileMenu.classList.add('hidden');
  }
});
</script>

<script src="js/logout-protection.js"></script>
</body>
</html>
