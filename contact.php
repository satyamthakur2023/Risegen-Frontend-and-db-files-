<?php
session_start();
require_once 'config.php';
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$pdo = connectDatabase();
$message = '';
$type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $message = "Invalid request.";
    } else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['message'] ?? '');

        if (!$name || !$email || !$subject || !$body) {
            $message = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address.";
        } else {
            $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?,?,?,?,NOW())")
                ->execute([$name, $email, $subject, $body]);
            $message = "Message sent! We'll get back to you within 24 hours.";
            $type = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | Risegen</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="w-full max-w-lg bg-white p-8 rounded-[2.5rem] shadow-2xl">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Contact Us</h1>
        <p class="text-slate-500 mt-1">We typically respond within 24 hours.</p>
    </div>

    <?php if ($message): ?>
    <div class="p-4 rounded-2xl mb-6 text-sm font-bold <?= $type === 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="text" name="name" placeholder="Your Name" required
            class="w-full p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition"
            value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
        <input type="email" name="email" placeholder="Email Address" required
            class="w-full p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition">
        <input type="text" name="subject" placeholder="Subject" required
            class="w-full p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition">
        <textarea name="message" rows="5" placeholder="Your message..." required
            class="w-full p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"></textarea>
        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl">
            Send Message
        </button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-500">
        <a href="<?= isset($_SESSION['user_id']) ? 'welcome.php' : 'index.php' ?>" class="text-indigo-600 font-bold hover:underline">← Go Back</a>
    </p>
</div>
</body>
</html>
