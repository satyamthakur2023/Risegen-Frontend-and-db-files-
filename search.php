<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$pdo = connectDatabase();
$q = trim($_GET['q'] ?? '');
$results = ['blogs' => [], 'courses' => [], 'users' => []];

if (strlen($q) >= 2) {
    $like = "%$q%";

    $stmt = $pdo->prepare("SELECT id, title, category FROM blogs WHERE title LIKE ? OR content LIKE ? LIMIT 5");
    $stmt->execute([$like, $like]);
    $results['blogs'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, title, `desc` FROM courses WHERE title LIKE ? OR `desc` LIKE ? LIMIT 5");
    $stmt->execute([$like, $like]);
    $results['courses'] = $stmt->fetchAll();
}
$total = count($results['blogs']) + count($results['courses']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search | Risegen</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }</style>
</head>
<body class="min-h-screen p-6">
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="welcome.php" class="text-indigo-600 font-bold hover:underline">← Back</a>
        <h1 class="text-3xl font-black text-slate-900">Search</h1>
    </div>

    <form method="GET" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search courses, blogs..."
                class="flex-1 p-4 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 text-lg">
            <button type="submit" class="px-6 py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition">Search</button>
        </div>
    </form>

    <?php if ($q): ?>
    <p class="text-slate-500 mb-6 font-medium"><?= $total ?> result(s) for "<strong><?= htmlspecialchars($q) ?></strong>"</p>

    <?php if (!empty($results['blogs'])): ?>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">📰 Blogs</h2>
        <div class="space-y-3">
            <?php foreach ($results['blogs'] as $b): ?>
            <a href="blogs.php" class="block p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                <p class="font-bold text-slate-900"><?= htmlspecialchars($b['title']) ?></p>
                <p class="text-sm text-indigo-500 font-semibold mt-1"><?= htmlspecialchars($b['category']) ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($results['courses'])): ?>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">🎓 Courses</h2>
        <div class="space-y-3">
            <?php foreach ($results['courses'] as $c): ?>
            <a href="course.php" class="block p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                <p class="font-bold text-slate-900"><?= htmlspecialchars($c['title']) ?></p>
                <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars(substr($c['desc'], 0, 100)) ?>...</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($total === 0): ?>
    <div class="text-center py-20 text-slate-400">
        <p class="text-5xl mb-4">🔍</p>
        <p class="text-xl font-bold">No results found for "<?= htmlspecialchars($q) ?>"</p>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
