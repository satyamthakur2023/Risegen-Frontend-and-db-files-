<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) { header("Location: welcome.php"); exit(); }

if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$pdo = connectDatabase();
$message = '';
$type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $message = "Invalid request.";
    } else {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Enter a valid email address.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $pdo->prepare("UPDATE users SET reset_token=?, reset_token_expires=? WHERE email=?")
                    ->execute([$token, $expires, $email]);
                // In production: send email with reset link
                // mail($email, "Reset Password", "Click: " . BASE_URL . "/reset_password.php?token=$token");
                $message = "If that email exists, a reset link has been sent.";
                $type = 'success';
            } else {
                $message = "If that email exists, a reset link has been sent.";
                $type = 'success';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | Risegen</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md bg-white p-8 rounded-[2.5rem] shadow-2xl">
    <div class="text-center mb-8">
        <a href="index.php" class="inline-block p-4 bg-indigo-600 rounded-2xl shadow-lg mb-4">
            <i class="fas fa-graduation-cap text-white text-2xl"></i>
        </a>
        <h2 class="text-3xl font-extrabold text-slate-900">Forgot Password</h2>
        <p class="text-slate-500 mt-1">Enter your email to receive a reset link.</p>
    </div>

    <?php if ($message): ?>
        <div class="p-4 rounded-2xl mb-6 text-sm font-bold <?= $type === 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="relative">
            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="email" name="email" placeholder="Email Address" required
                class="w-full pl-12 p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition">
        </div>
        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl">
            Send Reset Link
        </button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">
        Remember it? <a href="login.php" class="text-indigo-600 font-extrabold hover:underline">Log in</a>
    </p>
</div>
</body>
</html>
