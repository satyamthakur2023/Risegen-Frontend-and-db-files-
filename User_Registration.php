<?php
// 1. Force error reporting to catch issues on ByetHost
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Start session silently to avoid ByetHost directory permission notices
if (!isset($_SESSION)) {
    @session_start();
}

require_once "config.php";

// Redirect if already logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['loggedin'])) {
    header("Location: welcome.php");
    exit();
}

// 3. Connect to Database using your config function
try {
    $pdo = connectDatabase();
} catch (Exception $e) {
    die("Connection failed: Check your database credentials in config.php");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        try {
            // Check if email already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $check->execute([$email]);
            
            if ($check->fetch()) {
                $error = "Email already registered.";
            } else {
                // Hash password and insert matching your exact DB schema
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Using named parameters for cleaner ByetHost execution
                $sql = "INSERT INTO users (username, email, password_hash, role, status) 
                        VALUES (:username, :email, :password, 'student', 'active')";
                
                $stmt = $pdo->prepare($sql);
                $success = $stmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':password' => $hash
                ]);
                
                if ($success) {
                    $_SESSION['success_message'] = "Account created! Please log in.";
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Failed to create account. Please try again.";
                }
            }
        } catch (PDOException $e) {
            // This will show exactly what's wrong with the SQL if it fails
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Risegen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.5); }
        .bg-blob { position: absolute; filter: blur(80px); z-index: -1; opacity: 0.4; border-radius: 50%; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <div class="bg-blob bg-indigo-400 w-96 h-96 top-[-10%] left-[-10%]"></div>
    <div class="bg-blob bg-pink-400 w-96 h-96 bottom-[-10%] right-[-10%]"></div>

    <div class="glass w-full max-w-md p-8 rounded-[2.5rem] shadow-2xl">
        <div class="text-center mb-8">
            <a href="index.php" class="inline-block p-4 bg-indigo-600 rounded-2xl shadow-lg mb-4 hover:scale-105 transition-transform">
                <i class="fas fa-graduation-cap text-white text-2xl"></i>
            </a>
            <h2 class="text-3xl font-extrabold text-slate-900">Create Account</h2>
            <p class="text-slate-500">Start your Learn, Earn, Repeat journey.</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm border border-red-100 font-bold">
                <i class="fas fa-circle-exclamation mr-2"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div class="relative">
                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required 
                    class="w-full pl-12 p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white/50">
            </div>
            
            <div class="relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email ?? ''); ?>" required 
                    class="w-full pl-12 p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white/50">
            </div>

            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="password" placeholder="Password" required 
                    class="w-full pl-12 p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white/50">
            </div>

            <div class="relative">
                <i class="fas fa-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" name="password_confirm" placeholder="Confirm Password" required 
                    class="w-full pl-12 p-3.5 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white/50">
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-indigo-600 transition shadow-xl mt-2">
                Register Now
            </button>
        </form>
        
        <p class="mt-8 text-center text-sm text-slate-600 font-medium">
            Already a member? <a href="login.php" class="text-indigo-600 font-extrabold hover:underline">Log in</a>
        </p>
    </div>
</body>
</html>