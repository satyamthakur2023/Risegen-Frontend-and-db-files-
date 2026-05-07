<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$pdo = connectDatabase();

$top_users = $pdo->query("
    SELECT u.username, t.score, t.status, t.cert_id,
           ROW_NUMBER() OVER (ORDER BY t.score DESC) as rank
    FROM test_results t
    JOIN users u ON u.username = t.username
    WHERE t.status = 'Passed'
    ORDER BY t.score DESC
    LIMIT 20
")->fetchAll();

$my_rank = $pdo->prepare("
    SELECT COUNT(*) + 1 FROM test_results
    WHERE score > (SELECT MAX(score) FROM test_results WHERE username = (SELECT username FROM users WHERE id = ?))
    AND status = 'Passed'
");
$my_rank->execute([$_SESSION['user_id']]);
$my_position = $my_rank->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leaderboard | Risegen</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; }</style>
</head>
<body class="min-h-screen text-white p-6">
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black">🏆 Leaderboard</h1>
            <p class="text-slate-400 mt-1">Top performers on the Risegen platform</p>
        </div>
        <a href="welcome.php" class="px-5 py-2 bg-slate-800 rounded-full text-sm font-bold hover:bg-slate-700 transition">← Dashboard</a>
    </div>

    <?php if ($my_position): ?>
    <div class="bg-indigo-600/20 border border-indigo-500/30 rounded-2xl p-4 mb-6 flex justify-between items-center">
        <span class="font-bold text-indigo-300">Your Current Rank</span>
        <span class="text-2xl font-black text-white">#<?= $my_position ?></span>
    </div>
    <?php endif; ?>

    <div class="space-y-3">
        <?php foreach ($top_users as $i => $user): ?>
        <?php
            $rank = $user['rank'];
            $medal = $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : "#$rank"));
            $highlight = $user['username'] === ($_SESSION['username'] ?? '') ? 'border-indigo-500 bg-indigo-600/10' : 'border-slate-700 bg-slate-800/50';
        ?>
        <div class="flex items-center justify-between p-4 rounded-2xl border <?= $highlight ?> transition">
            <div class="flex items-center gap-4">
                <span class="text-2xl w-10 text-center font-black"><?= $medal ?></span>
                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-bold uppercase">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div>
                    <p class="font-bold"><?= htmlspecialchars($user['username']) ?></p>
                    <?php if ($user['cert_id']): ?>
                    <p class="text-xs text-emerald-400 font-semibold">✅ Certified</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-black text-indigo-400"><?= $user['score'] ?>%</p>
                <p class="text-xs text-slate-400">Score</p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($top_users)): ?>
        <div class="text-center py-20 text-slate-500">No results yet. <a href="gamebox.php" class="text-indigo-400 underline">Take the exam!</a></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
