<?php
session_start();

// 🚫 Prevent back access after logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Show errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Include database connection
require_once 'config.php';

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$user_data = [];
$status_message = '';
$status_type = 'error';

// ✅ Fetch user data
try {
    if (!function_exists('connectDatabase')) {
        throw new Exception("Database connection function not found in config.php");
    }

    $pdo = connectDatabase();

    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        throw new Exception("User not found in database.");
    }

} catch (Exception $e) {
    // Log and display error
    error_log("Profile fetch error: " . $e->getMessage());
    $status_message = "Unable to load profile. Please try again later.";
}

// ✅ Handle session messages (like updates)
if (isset($_SESSION['update_status'])) {
    $status_message = $_SESSION['update_status']['message'] ?? $status_message;
    $status_type = $_SESSION['update_status']['type'] ?? 'success';
    unset($_SESSION['update_status']);
}

$app_version = "1.0.1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - Risegen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl space-y-6">
    
    <h2 class="text-3xl font-extrabold text-gray-900 text-center">Update Your Profile</h2>
    <p class="text-center text-sm text-gray-600">
      Logged in as: <span class="font-semibold text-indigo-600"><?php echo htmlspecialchars($username); ?></span>
    </p>

    <!-- Status -->
    <?php if (!empty($status_message)): ?>
      <?php $alert_class = ($status_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>
      <div class="p-4 rounded-lg <?php echo $alert_class; ?>" role="alert">
        <p class="font-medium"><?php echo htmlspecialchars($status_message); ?></p>
      </div>
    <?php endif; ?>

    <!-- Profile Update Form -->
    <form action="user_details_update.php" method="POST" class="space-y-4">
      <!-- Username -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md 
                      focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
               required>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md 
                      focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
               required>
      </div>

      <!-- Created At -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Account Created On</label>
        <input type="text" readonly
               value="<?php echo htmlspecialchars($user_data['created_at'] ?? ''); ?>"
               class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600 cursor-not-allowed">
      </div>

      <!-- Change Password -->
      <div class="pt-4 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Change Password</h3>

        <label class="block text-sm font-medium text-gray-700">New Password</label>
        <input type="password" name="new_password"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md 
                      focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Enter new password">

        <label class="block text-sm font-medium text-gray-700 mt-3">Confirm New Password</label>
        <input type="password" name="confirm_password"
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md 
                      focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Re-enter new password">
      </div>

      <div class="pt-4">
        <button type="submit" 
                class="w-full flex justify-center py-2 px-4 rounded-md shadow-sm 
                       text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
          Save Changes
        </button>
      </div>
    </form>

    <div class="text-center pt-4 border-t border-gray-200 flex justify-between items-center">
      <a href="welcome.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; Back</a>
      <a href="logout.php" class="text-sm font-medium text-red-600 hover:text-red-500">Logout</a>
    </div>
  </div>
</body>
</html>
