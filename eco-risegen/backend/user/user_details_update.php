<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status_message = '';
$status_type = 'error';

try {
    // Connect to database
    if (!function_exists('connectDatabase')) {
        throw new Exception("Database connection function not found.");
    }
    $pdo = connectDatabase();

    // Collect form data safely
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    // Validate password match if provided
    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }
        if (strlen($new_password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    }

    // Prepare update fields
    $fields = "username = :username, email = :email, updated_at = NOW(), last_login_ip = :ip, last_login_time = NOW()";
    $params = [
        ':username' => $username,
        ':email' => $email,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ':id' => $user_id
    ];

    // Include password hash if changed
    if (!empty($new_password)) {
        $fields .= ", password_hash = :password_hash";
        $params[':password_hash'] = $password_hash;
    }

    // Execute update
    $stmt = $pdo->prepare("UPDATE users SET $fields WHERE id = :id");
    $stmt->execute($params);

    // Update session username
    $_SESSION['username'] = $username;

    $_SESSION['update_status'] = [
        'message' => 'Profile updated successfully!',
        'type' => 'success'
    ];

} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    $_SESSION['update_status'] = [
        'message' => 'Error updating profile: ' . $e->getMessage(),
        'type' => 'error'
    ];
}

// Redirect back to profile page
header("Location: profile.php");
exit();
