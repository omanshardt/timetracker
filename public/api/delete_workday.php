<?php
// public/api/delete_workday.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required field: id']);
    exit;
}

$id = (int) $input['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM work_days WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Entry not found.']);
    } else {
        echo json_encode(['success' => true]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
