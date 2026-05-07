<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>