<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

$pdo = connectDatabase();
$msg = '';

// Handle delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM blogs WHERE id = ?")->execute([intval($_GET['delete'])]);
    $msg = "Blog deleted.";
}

// Handle create/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = intval($_POST['id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $content  = trim($_POST['content'] ?? '');

    if ($id) {
        $pdo->prepare("UPDATE blogs SET title=?, category=?, content=? WHERE id=?")->execute([$title, $category, $content, $id]);
        $msg = "Blog updated.";
    } else {
        $pdo->prepare("INSERT INTO blogs (title, category, content, created_at) VALUES (?,?,?,NOW())")->execute([$title, $category, $content]);
        $msg = "Blog created.";
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $edit = $stmt->fetch();
}

$blogs = $pdo->query("SELECT id, title, category, created_at FROM blogs ORDER BY id DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Manager | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">📝 Blog Manager</h1>
        <a href="admin_dashboard.php" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">← Dashboard</a>
    </div>

    <?php if ($msg): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 font-semibold"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Create / Edit Form -->
    <div class="bg-white rounded-2xl shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4"><?= $edit ? 'Edit Blog' : 'Create New Blog' ?></h2>
        <form method="POST" class="space-y-4">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
            <input type="text" name="title" placeholder="Blog Title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"
                class="w-full p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="category" class="w-full p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500">
                <?php foreach (['Infrastructure','Cyber-Security','Neural-Logic','Data-Science','SaaS'] as $cat): ?>
                <option value="<?= $cat ?>" <?= ($edit['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="content" rows="6" placeholder="Blog content (HTML allowed)..." required
                class="w-full p-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-indigo-500 resize-none"><?= htmlspecialchars($edit['content'] ?? '') ?></textarea>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    <?= $edit ? 'Update Blog' : 'Publish Blog' ?>
                </button>
                <?php if ($edit): ?>
                <a href="admin_blog_manager.php" class="px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Blog List -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">Title</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Category</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Date</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($blogs as $b): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium text-gray-900"><?= htmlspecialchars(substr($b['title'], 0, 60)) ?>...</td>
                    <td class="p-4"><span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold"><?= htmlspecialchars($b['category']) ?></span></td>
                    <td class="p-4 text-gray-500"><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
                    <td class="p-4 flex gap-2">
                        <a href="?edit=<?= $b['id'] ?>" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold hover:bg-yellow-200">Edit</a>
                        <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete this blog?')" class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-200">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
