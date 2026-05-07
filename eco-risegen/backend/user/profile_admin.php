<?php
// ========================================================
// RiseGen User Profile Editor (v1.1) - Fixed Logic & UX
// ========================================================

// 1. Configuration & Initialization
require_once 'config.php'; 

session_start();
// **SECURITY CHECK REMINDER**: 
// For production, ensure user is authenticated and has admin privileges here.
// Example: if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) { header('Location: login.php'); exit; }

// --- Dynamic Variables ---
// NOTE: For a real app, this ID MUST come from a secure GET parameter (e.g., ?id=)
$user_id_to_update = 1; 
$admin_name = "System Administrator"; // Should be pulled from session
$message = "";
$db_error = false;

// Initialize form data array with empty strings for fallback
$form_data = [
    'username' => '', 
    'email' => '', 
    'role' => 'user', 
    'status' => 'inactive', 
    'bio' => ''
];

// --- Database Connection ---
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    $db_error = true;
    $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg' role='alert'>❌ Database connection failed: " . htmlspecialchars($conn->connect_error) . ". Check config.php.</div>";
}

// --------------------------------------------------------
// 2. FETCH EXISTING USER DATA (Initial Load)
// --------------------------------------------------------
if (!$db_error) {
    $stmt = $conn->prepare("SELECT id, username, email, role, status, bio FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_to_update);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        // Use database data for the form if no POST is yet processed
        $form_data = array_merge($form_data, $user_data);
    } else {
        $db_error = true;
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg' role='alert'>❌ User profile (ID {$user_id_to_update}) not found.</div>";
    }
    $stmt->close();
}

// --------------------------------------------------------
// 3. HANDLE FORM SUBMISSION (POST Request)
// --------------------------------------------------------
if (!$db_error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3.1 Gather and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'user';
    $status   = $_POST['status'] ?? 'inactive';
    $bio      = trim($_POST['bio'] ?? '');
    
    // **FIXED ISSUE**: Update $form_data with POST values immediately 
    // so user input is preserved if execution fails (e.g., validation error)
    $form_data = compact('username', 'email', 'role', 'status', 'bio');


    if (empty($username) || empty($email)) {
        $message = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg' role='alert'>⚠️ Username and Email are required.</div>";
    } else {
        // 3.2 Build SQL dynamically
        $sql_fields = "username = ?, email = ?, role = ?, status = ?, bio = ?, updated_at = NOW()";
        $bind_types = "sssss";
        $bind_params = [$username, $email, $role, $status, $bio];

        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql_fields .= ", password_hash = ?";
            $bind_types .= "s";
            $bind_params[] = $password_hash;
        }

        $sql = "UPDATE users SET $sql_fields WHERE id = ?";
        $bind_types .= "i";
        $bind_params[] = $user_id_to_update;

        // 3.3 Execute update
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bind_types, ...$bind_params);

        if ($stmt->execute()) {
            // SUCCESS: Redirect
            header("Location: security_logs.php?action=user_updated&user_id=" . $user_id_to_update);
            exit;
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg' role='alert'>❌ Error updating profile: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// --------------------------------------------------------
// 4. CONNECTION CLOSE
// --------------------------------------------------------
if (isset($conn) && !$db_error) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Editor (ID <?= $user_id_to_update ?>) | RiseGen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* --- Tailwind Config for Custom Colors --- */
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5', /* Indigo */
                        secondary: '#10b981', /* Emerald */
                    }
                }
            }
        };
        /* --- Custom Styles --- */
        body { font-family: 'Inter', sans-serif; min-height: 100vh; background: #f3f4f6; }
        .card-shadow { border-radius: 1rem; box-shadow: 0 15px 40px rgba(0,0,0,0.08); transition: all 0.3s ease; background: #fff; }
        .card-shadow:hover { transform: translateY(-3px); box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #4f46e5; pointer-events: none; }
        input[type="text"], input[type="email"], input[type="password"], textarea, select {
            border-radius: 0.75rem; /* 12px */
            padding: 0.8rem 1rem;
            border: 1px solid #ccc;
            transition: all 0.3s;
            width: 100%;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>
<body class="text-gray-800">

<header class="fixed w-full bg-gray-800 text-white shadow z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center h-16 px-4 sm:px-6 lg:px-8">
        <div class="text-2xl font-extrabold text-primary">RiseGen<span class="text-white font-light">Admin</span></div>
        
        <nav class="hidden md:flex space-x-4 items-center">
            <a href="admin_dashboard.php" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-sm font-medium">Dashboard</a>
            <a href="#" class="flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">User Management</a>
            <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 p-2 rounded-lg" title="Logout"><i data-lucide="log-out" class="w-5 h-5"></i></a>
        </nav>
        
        <div class="md:hidden">
            <button id="mobile-menu-toggle" type="button" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <i data-lucide="menu" id="menu-icon-closed" class="h-6 w-6"></i>
                <i data-lucide="x" id="menu-icon-open" class="hidden h-6 w-6"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="md:hidden hidden bg-gray-800 px-2 pt-2 pb-3 space-y-1">
        <a href="admin_dashboard.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-base font-medium">Dashboard</a>
        <a href="#" class="block px-3 py-2 rounded-lg bg-indigo-600 text-white text-base font-medium">User Management</a>
        <a href="logout.php" class="block px-3 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-base font-medium mt-2 text-center">Logout</a>
    </div>
</header>
<main class="max-w-4xl mx-auto px-4 py-8 pt-24 md:pt-20">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">User Management</h1>
    <p class="text-gray-500 mb-8">Editing Profile for User ID: <span class="font-semibold text-primary"><?= htmlspecialchars($user_id_to_update) ?></span></p>

    <div class="card-shadow p-6 sm:p-8 lg:p-10 mx-auto">

        <h3 class="text-2xl font-bold text-center mb-5 flex items-center justify-center text-primary">
            <i data-lucide="user-cog" class="w-6 h-6 mr-3"></i> Edit User Details
        </h3>

        <div class="mb-4">
            <?= $message ?>
        </div>

        <?php if (!$db_error): // Only show form if DB connection is okay ?>
        <form method="POST" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                    <div class="input-group-custom">
                        <input type="text" name="username" required value="<?= htmlspecialchars($form_data['username']) ?>">
                        <i data-lucide="user" class="w-5 h-5 input-icon"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <div class="input-group-custom">
                        <input type="email" name="email" required value="<?= htmlspecialchars($form_data['email']) ?>">
                        <i data-lucide="mail" class="w-5 h-5 input-icon"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role">
                        <option value="user" <?= ($form_data['role']=='user')?'selected':'' ?>>User</option>
                        <option value="admin" <?= ($form_data['role']=='admin')?'selected':'' ?>>Admin</option>
                        <option value="instructor" <?= ($form_data['role']=='instructor')?'selected':'' ?>>Instructor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status">
                        <option value="active" <?= ($form_data['status']=='active')?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= ($form_data['status']=='inactive')?'selected':'' ?>>Trial/Inactive</option>
                        <option value="banned" <?= ($form_data['status']=='banned')?'selected':'' ?>>Banned</option>
                    </select>
                </div>
            </div>

            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
                <div class="input-group-custom">
                    <input type="password" name="password" placeholder="Minimum 8 characters">
                    <i data-lucide="lock" class="w-5 h-5 input-icon"></i>
                </div>
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current password.</p>
            </div>

            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio/Notes</label>
                <textarea name="bio" rows="4" placeholder="Enter notes or user bio here..."><?= htmlspecialchars($form_data['bio']) ?></textarea>
            </div>


            <div class="text-center pt-4">
                <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition shadow-md shadow-primary/30">
                    <i data-lucide="save" class="w-5 h-5 inline mr-2"></i> Save Profile Changes
                </button>
            </div>
        </form>
        <?php endif; ?>

    </div>
</main>

<footer class="mt-12 p-4 text-center text-gray-500 border-t border-gray-200">
    <p class="text-sm">
        &copy; <?= date("Y"); ?> RiseGen Admin Panel. All rights reserved.
    </p>
</footer>

<script>
// Initialize Lucide Icons and Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // 2. Mobile Menu Toggle Logic **FIXED ISSUE**
    const toggleButton = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconClosed = document.getElementById('menu-icon-closed');
    const iconOpen = document.getElementById('menu-icon-open');

    if (toggleButton && mobileMenu && iconClosed && iconOpen) {
        toggleButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            iconClosed.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            toggleButton.setAttribute('aria-expanded', !mobileMenu.classList.contains('hidden'));
        });
    }
});
</script>
</body>
</html>