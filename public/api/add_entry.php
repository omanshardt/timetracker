<?php
// public/api/add_entry.php
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

$required_fields = ['reporting_date', 'start_time', 'end_time', 'task_id'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

$reporting_date = $input['reporting_date'];
$start_time = $input['start_time'];
$end_time = $input['end_time'];
$task_id = $input['task_id'];
$description = $input['description'] ?? '';

// Default tracking time to real time if not specified (Prompt assumes same form for simplicity right now)
$start_time_reported = $input['start_time_reported'] ?? $start_time;
$end_time_reported = $input['end_time_reported'] ?? $end_time;

try {
    $stmt = $pdo->prepare("
        INSERT INTO timetracker (
            reporting_date, 
            start_time, 
            end_time, 
            start_time_reported, 
            end_time_reported, 
            task_id, 
            description
        ) VALUES (
            :reporting_date, 
            :start_time, 
            :end_time, 
            :start_time_reported, 
            :end_time_reported, 
            :task_id, 
            :description
        )
    ");
    
    $stmt->execute([
        'reporting_date' => $reporting_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'start_time_reported' => $start_time_reported,
        'end_time_reported' => $end_time_reported,
        'task_id' => $task_id,
        'description' => $description
    ]);

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
