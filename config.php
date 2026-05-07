


<?php
// ========================================================
// RiseGen Database Configuration (config.php)
// This file provides connection logic for both MySQLi ($conn)
// and PDO (via connectDatabase() function) to maintain compatibility.
// ========================================================

// --- Database Credentials (Variables for MySQLi Connection) ---
$db_host = 'sql107.byethost7.com';    // Correct MySQL Host
$db_user = 'b7_40130868';             // Your DB User
$db_pass = '1cbjvqfy';                        // ⚠️ Replace with your actual DB password
$db_name = 'b7_40130868_risegen';    // Your DB Name

// --- Database Credentials (Constants for PDO/Other Scripts) ---
// Defines constants (DB_HOST, etc.) to resolve the "Undefined constant" error in admin_dashboard.php
if (!defined('DB_HOST')) define('DB_HOST', $db_host);
if (!defined('DB_USER')) define('DB_USER', $db_user);
if (!defined('DB_PASS')) define('DB_PASS', $db_pass);
if (!defined('DB_NAME')) define('DB_NAME', $db_name);


// --- MySQLi Connection Variables ---
$conn = null;
$db_error = false;
$error_message = '';

// --- 1. Establish MySQLi Connection (Used by admin_dashboard.php, user_management.php) ---
try {
    // Create connection object
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Check for connection errors
    if ($conn->connect_error) {
        $db_error = true;
        // Construct user-friendly error message, hiding sensitive connection details
        $error_message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4' role='alert'>
                            ❌ Database Connection Failed (MySQLi). Error: (" . $conn->connect_errno . ") " . htmlspecialchars($conn->connect_error) . "
                          </div>";
    } else {
        // Connection successful
        $db_error = false;
        // Set charset for security and compatibility
        $conn->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    // Catch any exceptions during the connection attempt (e.g., if the mysqli class is missing)
    $db_error = true;
    $error_message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4' role='alert'>
                        ❌ Critical MySQLi Connection Error: " . htmlspecialchars($e->getMessage()) . "
                      </div>";
}


// --- 2. PDO Connection Function (Used by User Registration.php, etc.) ---
/**
 * Establishes a PDO connection to the database.
 * @return PDO The PDO database connection object.
 */
function connectDatabase() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
        ];
        // Note: Using the defined constants (DB_USER, DB_PASS) here
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Log the detailed error but show a generic message to the user
        error_log("Database PDO connection failed: " . $e->getMessage());
        // For fatal errors in PDO scripts, we use die() or throw an exception.
        die("<div style='background-color:#fee2e2; color:#ef4444; padding:15px; border-radius:8px;'>❌ Database connection failed. Please try again later. (Code: P-CONN)</div>");
    }
}

// Note: If the MySQLi connection fails, $conn will be defined but $db_error will be true.
?>

