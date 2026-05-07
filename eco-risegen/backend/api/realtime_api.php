<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'risegen';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['error' => 'Connection failed: ' . $e->getMessage()]));
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'POST':
        if($action === 'upload_pdf') {
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO pdf_uploads (user_id, filename, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$data['user_id'], $data['filename'], $data['file_path']]);
            echo json_encode(['pdf_id' => $pdo->lastInsertId()]);
        }
        
        if($action === 'save_answer') {
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO user_answers (session_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['session_id'], $data['question_id'], $data['user_answer'], $data['is_correct']]);
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'GET':
        if($action === 'get_topics') {
            $pdf_id = $_GET['pdf_id'];
            $stmt = $pdo->prepare("SELECT * FROM topics WHERE pdf_id = ? ORDER BY relevance_score DESC");
            $stmt->execute([$pdf_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        if($action === 'get_questions') {
            $topic_id = $_GET['topic_id'];
            $limit = $_GET['limit'] ?? 10;
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE topic_id = ? LIMIT ?");
            $stmt->execute([$topic_id, $limit]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        if($action === 'live_score') {
            $session_id = $_GET['session_id'];
            $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(is_correct) as correct FROM user_answers WHERE session_id = ?");
            $stmt->execute([$session_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['total' => $result['total'], 'correct' => $result['correct'] ?: 0]);
        }
        break;
}
?>