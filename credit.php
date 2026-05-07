<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$pdo = connectDatabase();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

$stmt = $pdo->prepare("SELECT credits FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$credits = $stmt->fetchColumn() ?? 0;

$msg = '';
// Handle credit purchase (demo — in production integrate Razorpay/Stripe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pack'])) {
    $packs = ['basic' => 50, 'standard' => 150, 'premium' => 400];
    $add = $packs[$_POST['pack']] ?? 0;
    if ($add > 0) {
        $pdo->prepare("UPDATE users SET credits = credits + ? WHERE id = ?")->execute([$add, $user_id]);
        $credits += $add;
        $msg = "✅ $add credits added to your account!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Credits | Risegen</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; }</style>
</head>
<body class="min-h-screen text-white p-6">
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black">💳 Credits</h1>
            <p class="text-slate-400 mt-1">Buy credits to unlock premium features</p>
        </div>
        <a href="welcome.php" class="px-5 py-2 bg-slate-800 rounded-full text-sm font-bold hover:bg-slate-700 transition">← Dashboard</a>
    </div>

    <!-- Balance -->
    <div class="bg-indigo-600/20 border border-indigo-500/30 rounded-3xl p-8 mb-8 text-center">
        <p class="text-slate-400 text-sm font-semibold uppercase tracking-widest mb-2">Current Balance</p>
        <p class="text-7xl font-black text-indigo-400"><?= number_format($credits) ?></p>
        <p class="text-slate-400 mt-2">credits available</p>
    </div>

    <?php if ($msg): ?>
    <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 p-4 rounded-2xl mb-6 font-bold text-center"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Packs -->
    <h2 class="text-xl font-bold mb-4 text-slate-300">Choose a Credit Pack</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $packs = [
            ['key'=>'basic',    'name'=>'Starter',  'credits'=>50,  'price'=>'₹99',  'color'=>'slate',   'features'=>['50 Credits','Basic MCQ Access','1 Month Validity']],
            ['key'=>'standard', 'name'=>'Standard', 'credits'=>150, 'price'=>'₹249', 'color'=>'indigo',  'features'=>['150 Credits','Full MCQ Access','Priority Support','3 Month Validity']],
            ['key'=>'premium',  'name'=>'Premium',  'credits'=>400, 'price'=>'₹599', 'color'=>'emerald', 'features'=>['400 Credits','All Features Unlocked','Certificate Priority','6 Month Validity']],
        ];
        foreach ($packs as $pack):
        $highlight = $pack['key'] === 'standard';
        ?>
        <div class="bg-slate-800/50 border <?= $highlight ? 'border-indigo-500' : 'border-slate-700' ?> rounded-3xl p-6 flex flex-col <?= $highlight ? 'ring-2 ring-indigo-500/30' : '' ?>">
            <?php if ($highlight): ?>
            <div class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-3">Most Popular</div>
            <?php endif; ?>
            <h3 class="text-xl font-black mb-1"><?= $pack['name'] ?></h3>
            <p class="text-3xl font-black text-<?= $pack['color'] ?>-400 mb-4"><?= $pack['price'] ?></p>
            <ul class="space-y-2 mb-6 flex-1">
                <?php foreach ($pack['features'] as $f): ?>
                <li class="text-sm text-slate-400 flex items-center gap-2">
                    <span class="text-emerald-400">✓</span> <?= $f ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <form method="POST">
                <input type="hidden" name="pack" value="<?= $pack['key'] ?>">
                <button type="submit" class="w-full py-3 rounded-2xl font-bold text-sm transition
                    <?= $highlight ? 'bg-indigo-600 hover:bg-indigo-500 text-white' : 'bg-slate-700 hover:bg-slate-600 text-white' ?>">
                    Buy <?= $pack['credits'] ?> Credits
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <p class="text-center text-slate-600 text-xs mt-8">Payments are processed securely. Credits are non-refundable.</p>
</div>
</body>
</html>
