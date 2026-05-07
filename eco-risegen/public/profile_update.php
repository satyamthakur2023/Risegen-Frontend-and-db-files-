<?php
/**
 * User Profile Update Script (FIXED: PHP Version Compatibility)
 * ----------------------------------------------------------------------
 * FIX: The dynamic parameter binding has been rewritten to resolve the 
 * "unexpected token &" parse error in older PHP versions.
 * * UPDATE: Implemented redirection to admin_dashboard.php upon successful update.
 * * ADJUSTMENT: Cleaned up HTML structure and removed conflicting CSS for better layout alignment.
 * ----------------------------------------------------------------------
 */

// =======================================================
// 1. DATABASE CONNECTION CONFIGURATION (MUST BE EDITED!)
// =======================================================
$servername = "localhost";
$db_username = "root";       // <-- CHANGE THIS to your MySQL user
$db_password = "";           // <-- (Updated to user context)
$database = "ai_mcqs";       // <-- (Updated to user context)

// Simulate Admin User Name (for the navbar, required by user's HTML structure)
$admin_name = "Admin User"; 

$user_id_to_update = 1;      // !!! SIMULATED: Change this to a session variable in production!

// Establish connection
$conn = new mysqli($servername, $db_username, $db_password, $database);

// Initial status variables
$message = "";
$db_error = false;

// Check connection
if ($conn->connect_error) {
    $db_error = true;
    $message = "<div class='alert alert-danger mt-3 text-center'>❌ Database Connection Failed: " . htmlspecialchars($conn->connect_error) . ". Please check your credentials.</div>";
}

// =======================================================
// 2. FETCH EXISTING USER DATA (Initial GET Request)
// =======================================================
$user_data = [];

if (!$db_error) {
    // Select specific columns for the profile to be updated
    $stmt = $conn->prepare("SELECT id, username, email, role, status, bio FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_to_update);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger mt-3 text-center border-0'>❌ User profile (ID {$user_id_to_update}) not found. Cannot proceed with update.</div>";
        $db_error = true;
    }
    $stmt->close();
}

// =======================================================
// 3. HANDLE FORM SUBMISSION (POST Request - UPDATE)
// =======================================================

if (!$db_error && $_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and trim user input from the submitted form
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"]; 
    $role = $_POST["role"];
    $status = $_POST["status"];
    $bio = trim($_POST["bio"]);

    if (empty($username) || empty($email)) {
        $message = "<div class='alert alert-warning mt-3 text-center border-0' role='alert'>⚠️ Username and Email are required fields.</div>";
    } else {
        $sql_fields = "username = ?, email = ?, role = ?, status = ?, bio = ?, updated_at = NOW()";
        $bind_types = "sssss"; 
        $bind_params = [$username, $email, $role, $status, $bio];

        // ------------------------------------
        // A. Handle password update conditionally
        // ------------------------------------
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql_fields .= ", password_hash = ?";
            array_push($bind_params, $password_hash);
            $bind_types .= "s"; 
        }

        // ------------------------------------
        // B. Construct and execute the final UPDATE statement
        // ------------------------------------
        $sql = "UPDATE users SET {$sql_fields} WHERE id = ?";
        $bind_types .= "i";
        array_push($bind_params, $user_id_to_update);

        $stmt = $conn->prepare($sql);
        
        // FIX for unexpected token "&" on older PHP versions
        $ref_bind_params = [];
        $ref_bind_params[] = $bind_types; 

        foreach ($bind_params as &$param) {
            $ref_bind_params[] = &$param;
        }

        call_user_func_array([$stmt, 'bind_param'], $ref_bind_params);
        
        if ($stmt->execute()) {
            // REDIRECTION: Send admin back to the dashboard upon success
            header("Location: admin_dashboard.php?status=success&user_id=" . $user_id_to_update);
            exit; 
        } else {
            // Error handling for SQL execution (e.g., duplicate email)
            $message = "<div class='alert alert-danger mt-3 text-center border-0' role='alert'>❌ Error updating profile: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// Close the connection
if (!$db_error && $conn) {
    $conn->close();
}

// Helper function (still included but simplified as it's not used in the final view)
function get_status_badge($status) {
    return $status; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Profile Update | Admin Panel</title>
    <!-- Load Tailwind CSS via CDN for overall layout -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4f46e5', // Indigo
                    }
                }
            }
        }
    </script>
    <!-- Load Bootstrap CSS for form components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Load Bootstrap Icons for better UX through visuals -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom styles adapted to work within the dashboard layout -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        :root {
            --primary: #4f46e5; /* Indigo */
        }
        /* Overriding the default background and allowing content to flow naturally */
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1); /* Lighter shadow for dashboard context */
            background: #fff;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .card h3 {
            color: var(--primary);
            font-weight: 800;
        }
        .form-control, .form-select, textarea {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid #ccc;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.15);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #555;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 12px;
            padding: 0.7rem 2.5rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(79,70,229,0.3);
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        .btn-primary:hover {
            background-color: #3730a3;
            border-color: #3730a3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(79,70,229,0.4);
        }
        .input-group-custom {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: var(--primary);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-inter">

<!-- Fixed Navbar (Tailwind/Bootstrap Integration) -->
<header class="fixed top-0 left-0 w-full bg-gray-800 text-white shadow z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <div class="flex-shrink-0 text-2xl font-extrabold text-indigo-400">
        Admin<span class="text-white font-light">Panel</span>
      </div>

      <nav class="hidden md:flex md:space-x-4 items-center">
        <!-- Assuming admin_dashboard.php exists for the redirect -->
        <a href="admin_dashboard.php" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-sm font-medium">Dashboard</a>
        <a href="#" class="flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">Users (Current)</a>
        <a href="#" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-sm font-medium">Products</a>
        <a href="#" class="flex items-center px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-sm font-medium">Reports</a>
      </nav>

      <div class="hidden md:flex items-center space-x-3">
        <span class="text-sm font-medium text-gray-300"><?= htmlspecialchars($admin_name) ?></span>
        <button class="bg-indigo-500 hover:bg-indigo-600 p-2 rounded-lg" title="Logout">Logout</button>
      </div>

      <div class="md:hidden">
        <button id="mobile-menu-toggle" type="button" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
          <svg id="menu-icon-closed" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
          <svg id="menu-icon-open" class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div id="mobile-menu" class="md:hidden hidden bg-gray-800 px-2 pt-2 pb-3 space-y-1">
    <a href="admin_dashboard.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-base font-medium">Dashboard</a>
    <a href="#" class="block px-3 py-2 rounded-lg bg-indigo-600 text-white text-base font-medium">Users (Current)</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-base font-medium">Products</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-indigo-700 hover:text-white text-base font-medium">Reports</a>
    <a href="#" class="block px-3 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-base font-medium mt-2 text-center">Logout</a>
  </div>
</header>

<!-- Main Content Area -->
<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-24 md:pt-20">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">User Profile Editor</h1>
    <p class="text-gray-500 mb-8">Editing user ID: <?= $user_id_to_update ?></p>
    
    <!-- Centering the card within the main content flow -->
    <div class="d-flex justify-content-center">
        <div class="container p-0">
            <div class="card p-4 p-md-5">

                <h3 class="text-center mb-5"><i class="bi bi-person-circle me-2"></i> Update User Profile</h3>

                <!-- Display Messages (Error, Warning) -->
                <?= $message ?>

                <?php if (!empty($user_data) && !$db_error): ?>
                <form method="POST" action="">
                    <div class="row g-4 mb-4">
                        <!-- Username Field with Icon -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control" name="username" required aria-label="Username" placeholder="e.g., JaneDoe123" 
                                       value="<?= htmlspecialchars($user_data['username'] ?? '') ?>">
                                <i class="bi bi-person input-icon"></i>
                            </div>
                        </div>

                        <!-- Email Field with Icon -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <input type="email" class="form-control" name="email" required aria-label="Email" placeholder="e.g., jane@example.com"
                                       value="<?= htmlspecialchars($user_data['email'] ?? '') ?>">
                                <i class="bi bi-envelope input-icon"></i>
                            </div>
                        </div>

                        <!-- Password Field (Optional Update) -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">New Password (Leave blank to keep current)</label>
                            <div class="input-group-custom">
                                <input type="password" class="form-control" name="password" aria-label="Password" placeholder="Minimum 8 characters">
                                <i class="bi bi-lock input-icon"></i>
                            </div>
                            <small class="text-muted">Only fill this if you want to change your password.</small>
                        </div>

                        <!-- Role Select (Now aligned next to Status) -->
                        <div class="col-6 col-md-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" aria-label="User Role">
                                <option value="user" <?= ($user_data['role'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= ($user_data['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="instructor" <?= ($user_data['role'] ?? '') == 'instructor' ? 'selected' : '' ?>>Instructor</option>
                            </select>
                        </div>

                        <!-- Status Select (Now aligned next to Role) -->
                        <div class="col-6 col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" aria-label="User Status">
                                <option value="active" <?= ($user_data['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($user_data['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Trial/Inactive</option>
                                <option value="banned" <?= ($user_data['status'] ?? '') == 'banned' ? 'selected' : '' ?>>Banned</option>
                            </select>
                        </div>

                        <!-- Bio Textarea (Full Width) -->
                        <div class="col-12">
                            <label class="form-label">Bio (Optional)</label>
                            <textarea class="form-control" name="bio" rows="3" placeholder="Tell us a little about your role or interests..." aria-label="User Bio"><?= htmlspecialchars($user_data['bio'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy-fill me-2"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Add Bootstrap JS for collapse functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mobile Menu Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconClosed = document.getElementById('menu-icon-closed');
    const iconOpen = document.getElementById('menu-icon-open');

    toggleButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        iconClosed.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        toggleButton.setAttribute('aria-expanded', !mobileMenu.classList.contains('hidden'));
    });
});
</script>
</body>
</html>
