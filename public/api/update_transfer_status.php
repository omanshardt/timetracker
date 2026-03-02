<?php
// public/api/update_transfer_status.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try $_POST if not JSON
    $input = $_POST;
}

$required_fields = ['id', 'field', 'value'];
foreach ($required_fields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

$id = (int) $input['id'];
$field = $input['field'];
$value = (int) $input['value'];

// Validate field name to prevent SQL injection
if (!in_array($field, ['transfer', 'transfered_intern', 'transfered_jira'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid field']);
    exit;
}

// Validate value
if (!in_array($value, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid value']);
    exit;
}

try {
    // Dynamic field name format is safe here because we validated it against an allowlist above
    $stmt = $pdo->prepare("UPDATE timetracker SET {$field} = :val WHERE id = :id");

    $stmt->execute([
        'val' => $value,
        'id' => $id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        // Technically successful execution but no rows modified (maybe ID doesn't exist or value was already the same)
        // Let's assume ID check is fine
        echo json_encode(['success' => true, 'message' => 'No rows updated.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
