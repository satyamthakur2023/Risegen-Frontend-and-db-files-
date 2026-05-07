<?php
// 1. Fix Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 2. Load config
require_once __DIR__ . '/config.php';
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

// --- 3. Logic for FETCHING Questions (GET) with Anti-Repeat ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $username = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : 'anonymous';

    // Validate limit
    $allowed_limits = [10, 20, 40];
    if (!in_array($limit, $allowed_limits)) { $limit = 20; }

    // STEP A: Get IDs of questions already seen by this user
    // STEP A: Get seen question IDs using prepared statement
    $stmt = $conn->prepare("SELECT question_id FROM user_exams WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $seen_ids = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'question_id');
    $stmt->close();

    // STEP B: Fetch unseen questions
    if (!empty($seen_ids)) {
        $placeholders = implode(',', array_fill(0, count($seen_ids), '?'));
        $types = str_repeat('i', count($seen_ids));
        $stmt = $conn->prepare("SELECT * FROM questions WHERE id NOT IN ($placeholders) ORDER BY RAND() LIMIT ?");
        $params = array_merge($seen_ids, [$limit]);
        $types .= 'i';
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt = $conn->prepare("SELECT * FROM questions ORDER BY RAND() LIMIT ?");
        $stmt->bind_param('i', $limit);
    }
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // STEP C: Pool reset if exhausted
    if (empty($questions) && !empty($seen_ids)) {
        $stmt = $conn->prepare("DELETE FROM user_exams WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM questions ORDER BY RAND() LIMIT ?");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // STEP D: Log seen questions
    $log = $conn->prepare("INSERT INTO user_exams (username, question_id) VALUES (?, ?)");
    foreach ($questions as $q) {
        $log->bind_param('si', $username, $q['id']);
        $log->execute();
    }
    $log->close();

    echo json_encode($questions);
    exit(); 
}

// --- 4. Logic for SAVING Results (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['username']) || !isset($data['score'])) {
        echo json_encode(["error" => "Invalid input data"]);
        exit();
    }

    $name  = $data['username'];
    $score = intval($data['score']);
    $status  = ($score >= 70) ? 'Passed' : 'Failed';
    $cert_id = ($status === 'Passed') ? 'CERT-' . strtoupper(uniqid()) : null;

    $stmt = $conn->prepare("INSERT INTO test_results (username, score, status, cert_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $name, $score, $status, $cert_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => $status, "cert_id" => $cert_id, "message" => "Result recorded successfully"]);
    } else {
        echo json_encode(["error" => "Database save failed"]);
    }
    $stmt->close();
    exit();
}
?>