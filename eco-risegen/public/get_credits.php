<?php
session_start();
require_once "config.php";
$pdo = connectDatabase();
$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT credits FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$credits = $stmt->fetchColumn();

echo json_encode(['credits' => $credits]);
?>
