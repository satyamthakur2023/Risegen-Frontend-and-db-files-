<?php
// Start the session to access user data
session_start();

// Check if the user is NOT logged in. If not, redirect them to the login page.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// User is logged in, retrieve their data
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Dashboard</title>
    <!-- Load Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style> :root { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl p-8 space-y-8 text-center">
        
        <h1 class="text-4xl font-extrabold text-indigo-700">
            Welcome to Risegen!
        </h1>
        
        <p class="text-3xl font-semibold text-gray-800">
            Hello, <?php echo htmlspecialchars($username); ?>.
        </p>

        <div class="p-6 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-inner">
            <p class="text-lg text-gray-600 mb-2">
                Your secure session is active.
            </p>
            <small class="text-sm text-gray-500 block">
                User ID: <?php echo htmlspecialchars($user_id); ?>
            </small>
        </div>

        <!-- Logout Button -->
        <a href="logout.php"
            class="inline-flex items-center justify-center 
                   bg-red-600 hover:bg-red-700 active:bg-red-800 
                   text-white font-bold py-3 px-8 
                   rounded-lg transition duration-200 
                   shadow-lg shadow-red-500/50 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Secure Logout
        </a>

    </div>
</body>
</html>
