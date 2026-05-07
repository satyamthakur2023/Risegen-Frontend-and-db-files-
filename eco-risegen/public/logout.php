<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'config.php'; // your DB connection


// Prevent caching and back button
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Clear session cookie completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Extra precaution: prevent caching in browser history
echo '<script type="text/javascript">
    window.history.replaceState(null, "", "index.php");
    window.location.href = "index.php";
</script>';

// Fallback redirect
header("Location: index.php");
exit();
?>
