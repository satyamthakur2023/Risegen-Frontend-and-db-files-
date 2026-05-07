<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

$pdo = connectDatabase();
$msg = '';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([intval($_GET['delete'])]);
    $msg = "Course deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['desc'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $level = trim($_POST['level'] ?? 'Beginner');

    if ($id) {
        $pdo->prepare("UPDATE courses SET title=?, `desc`=?, price=?, level=? WHERE id=?")->execute([$title, $desc, $price, $level, $id]);
        $msg = "Course updated.";
    } else {
        $pdo->prepare("INSERT INTO courses (title, `desc`, price, level, created_at) VALUES (?,?,?,?,NOW())")->execute([$title, $desc, $price, $level]);
        $msg = "Course created.";
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $edit = $stmt->fetch();
}

$courses = $pdo->query("SELECT id, title, price, level, created_at FROM courses ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Manager | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">🎓 Course Manager</h1>
        <a href="admin_dashboard.php" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">← Dashboard</a>
    </div>

    <?php if ($msg): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 font-semibold"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4"><?= $edit ? 'Edit Course' : 'Add New Course' ?></h2>
        <form method="POST" class="space-y-4">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
            <input type="text" name="title" placeholder="Course Title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"
                class="w-full p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500">
            <textarea name="desc" rows="3" placeholder="Course Description" required
                class="w-full p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500 resize-none"><?= htmlspecialchars($edit['desc'] ?? '') ?></textarea>
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="price" placeholder="Price (₹)" value="<?= $edit['price'] ?? 0 ?>"
                    class="p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500">
                <select name="level" class="p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['Beginner','Intermediate','Advanced'] as $l): ?>
                    <option value="<?= $l ?>" <?= ($edit['level'] ?? '') === $l ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    <?= $edit ? 'Update Course' : 'Add Course' ?>
                </button>
                <?php if ($edit): ?>
                <a href="admin_course_manager.php" class="px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">Title</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Level</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Price</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($courses as $c): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium text-gray-900"><?= htmlspecialchars($c['title']) ?></td>
                    <td class="p-4"><span class="px-2 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold"><?= htmlspecialchars($c['level']) ?></span></td>
                    <td class="p-4 font-bold text-indigo-600">₹<?= number_format($c['price']) ?></td>
                    <td class="p-4 flex gap-2">
                        <a href="?edit=<?= $c['id'] ?>" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold hover:bg-yellow-200">Edit</a>
                        <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Delete this course?')" class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-200">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
