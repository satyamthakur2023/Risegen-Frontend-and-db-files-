<?php
session_start();
require_once 'config.php'; // Make sure this contains connectDatabase()

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$pdo = connectDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id  = $_SESSION['user_id'];
    $job_id   = intval($_POST['job_id'] ?? 0);

    if (!$job_id) {
        echo json_encode(["status" => "error", "message" => "Invalid job ID."]);
        exit();
    }

    $check = $pdo->prepare("SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $check->execute([$user_id, $job_id]);

    if ($check->fetch()) {
        echo json_encode(["status" => "info", "message" => "Job already saved."]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO saved_jobs (user_id, job_id, saved_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $job_id]);
        echo json_encode(["status" => "success", "message" => "Job saved successfully!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>