<?php
// 1. Fix Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 2. Database Credentials
$db_host = 'sql107.byethost7.com';    
$db_user = 'b7_40130868';             
$db_pass = '1cbjvqfy';    
$db_name = 'b7_40130868_risegen';    

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed", "details" => $conn->connect_error]);
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
    $exclude_query = "SELECT question_id FROM user_exams WHERE username = '$username'";
    $exclude_res = $conn->query($exclude_query);
    
    $seen_ids = [];
    if ($exclude_res) {
        while($row = $exclude_res->fetch_assoc()) {
            $seen_ids[] = $row['question_id'];
        }
    }

    // STEP B: Build Exclusion Clause
    $exclude_clause = "";
    if (!empty($seen_ids)) {
        $exclude_clause = "WHERE id NOT IN (" . implode(',', $seen_ids) . ")";
    }

    // STEP C: Fetch New Random Questions
    $query = "SELECT * FROM questions $exclude_clause ORDER BY RAND() LIMIT $limit";
    $res = $conn->query($query);

    // STEP D: Pool Exhaustion Reset
    // If no new questions are left, delete history for this user and restart
    if ($res->num_rows == 0 && !empty($seen_ids)) {
        $conn->query("DELETE FROM user_exams WHERE username = '$username'");
        $query = "SELECT * FROM questions ORDER BY RAND() LIMIT $limit";
        $res = $conn->query($query);
    }

    $questions = $res->fetch_all(MYSQLI_ASSOC);

    // STEP E: Log current question IDs so they don't repeat next time
    foreach ($questions as $q) {
        $q_id = $q['id'];
        $conn->query("INSERT INTO user_exams (username, question_id) VALUES ('$username', $q_id)");
    }

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

    $name = $conn->real_escape_string($data['username']);
    $score = intval($data['score']);
    $status = ($score >= 70) ? 'Passed' : 'Failed';
    
    $cert_id = ($status === 'Passed') ? 'CERT-' . strtoupper(uniqid()) : null;
    $cert_val = ($cert_id) ? "'$cert_id'" : "NULL";

    $sql = "INSERT INTO test_results (username, score, status, cert_id) 
            VALUES ('$name', $score, '$status', $cert_val)";
    
    if ($conn->query($sql)) {
        echo json_encode([
            "status" => $status, 
            "cert_id" => $cert_id,
            "message" => "Result recorded successfully"
        ]);
    } else {
        echo json_encode(["error" => "Database save failed: " . $conn->error]);
    }
    exit();
}
?>