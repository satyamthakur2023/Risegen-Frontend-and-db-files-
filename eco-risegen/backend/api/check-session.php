<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isAuthenticated()) {
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'] ?? 1,
            'name' => $_SESSION['username'] ?? 'User',
            'role' => $_SESSION['role'] ?? 'student'
        ]
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}
?>