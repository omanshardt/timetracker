<?php
// public/api/add_workday.php
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

if (empty($input['work_date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required field: work_date']);
    exit;
}

$work_date = $input['work_date'];
$required_time = $input['required_time'] ?? '08:00:00';

// Normalize time format (accept HH:MM or HH:MM:SS)
if (preg_match('/^\d{2}:\d{2}$/', $required_time)) {
    $required_time .= ':00';
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO work_days (work_date, required_time)
        VALUES (:work_date, :required_time)
    ");
    $stmt->execute([
        'work_date' => $work_date,
        'required_time' => $required_time,
    ]);

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['error' => 'An entry for this date already exists.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
