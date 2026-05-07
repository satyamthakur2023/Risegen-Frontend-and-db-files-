<?php
header("Content-Type: application/json");

require_once __DIR__ . '/config.php';
if ($conn->connect_error) {
    die(json_encode(["error" => "Link to Central Intelligence Server Lost"]));
}

$limit    = min(intval($_GET['limit'] ?? 12), 50);
$offset   = max(intval($_GET['offset'] ?? 0), 0);
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

// Build WHERE clauses
$conditions = [];
$params     = [];
$types      = '';

if ($search !== '') {
    $like = "%$search%";
    $conditions[] = "(title LIKE ? OR category LIKE ? OR content LIKE ?)";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}

if ($category !== '') {
    $conditions[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$params[] = $limit;
$params[] = $offset;
$types   .= 'ii';

$stmt = $conn->prepare(
    "SELECT id, title, category, content, DATE_FORMAT(created_at, '%b %d, %Y') as date
     FROM blogs $where
     ORDER BY id DESC LIMIT ? OFFSET ?"
);

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$nodes = [];
while ($row = $result->fetch_assoc()) {
    $nodes[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(["nodes" => $nodes]);
?>
