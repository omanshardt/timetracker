<?php
// public/api/update_workday.php
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
$work_date = $input['work_date'] ?? null;
$required_time = $input['required_time'] ?? null;

// Normalize time format
if ($required_time && preg_match('/^\d{2}:\d{2}$/', $required_time)) {
    $required_time .= ':00';
}

try {
    // Build dynamic update
    $fields = [];
    $params = ['id' => $id];

    if ($work_date !== null) {
        $fields[] = 'work_date = :work_date';
        $params['work_date'] = $work_date;
    }
    if ($required_time !== null) {
        $fields[] = 'required_time = :required_time';
        $params['required_time'] = $required_time;
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update.']);
        exit;
    }

    $sql = "UPDATE work_days SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Entry not found.']);
    } else {
        echo json_encode(['success' => true]);
    }

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['error' => 'An entry for this date already exists.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
