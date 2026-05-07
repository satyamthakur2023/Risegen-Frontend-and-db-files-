<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? 'login';

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    $name     = trim($input['name'] ?? '');
    $email    = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $input['password'] ?? '';
    $role     = in_array($input['role'] ?? '', ['student', 'instructor']) ? $input['role'] : 'student';

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name, email and password are required']);
        exit();
    }

    try {
        // Check if email already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hash, $role]);
        $userId = $pdo->lastInsertId();

        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $name;
        $_SESSION['role'] = $role;

        echo json_encode([
            'success' => true,
            'user' => ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => $role]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
    exit();
}

// ── LOGIN ─────────────────────────────────────────────────
$email    = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $input['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit();
}

// Demo accounts — always work without DB
$demoUsers = [
    'student@example.com'    => ['id' => 0, 'name' => 'Demo Student',    'role' => 'student',     'password' => 'password'],
    'instructor@example.com' => ['id' => 0, 'name' => 'Demo Instructor', 'role' => 'instructor',  'password' => 'password'],
];

if (isset($demoUsers[$email]) && $demoUsers[$email]['password'] === $password) {
    $u = $demoUsers[$email];
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['username'] = $u['name'];
    $_SESSION['role'] = $u['role'];
    echo json_encode(['success' => true, 'user' => ['id' => $u['id'], 'name' => $u['name'], 'email' => $email, 'role' => $u['role']]]);
    exit();
}

// Real DB login
try {
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
?>
