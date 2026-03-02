<?php
// public/api/update_entry.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Required fields
$id = $input['id'] ?? null;
$reporting_date = $input['reporting_date'] ?? null;
$start_time = $input['start_time'] ?? null;
$end_time = $input['end_time'] ?? null;

if (!$id || !$reporting_date || !$start_time || !$end_time) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields (id, date, start, end)']);
    exit;
}

// Optional Fields
$task_id = $input['task_id'] ?? null;
$description = $input['description'] ?? null;
$start_time_reported = $input['start_time_reported'] ?? null;
$end_time_reported = $input['end_time_reported'] ?? null;
$task_name = $input['task_name'] ?? null;
$description_long = $input['description_long'] ?? null;
$transfer = $input['transfer'] ?? 1;
$transfered_intern = $input['transfered_intern'] ?? 0;
$transfered_jira = $input['transfered_jira'] ?? 0;

try {
    $stmt = $pdo->prepare("
        UPDATE timetracker 
        SET 
            reporting_date = :reporting_date,
            start_time = :start_time,
            end_time = :end_time,
            start_time_reported = :start_time_reported,
            end_time_reported = :end_time_reported,
            task_id = :task_id,
            task_name = :task_name,
            description = :description,
            description_long = :description_long,
            transfer = :transfer,
            transfered_intern = :transfered_intern,
            transfered_jira = :transfered_jira
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $id,
        'reporting_date' => $reporting_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'start_time_reported' => $start_time_reported,
        'end_time_reported' => $end_time_reported,
        'task_id' => $task_id,
        'task_name' => $task_name,
        'description' => $description,
        'description_long' => $description_long,
        'transfer' => $transfer,
        'transfered_intern' => $transfered_intern,
        'transfered_jira' => $transfered_jira
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        // rowCount can be 0 if the row was found but no data actually changed (same exact data)
        // or if ID did not exist. For UX, we can just say success=true to simulate "it's done".
        echo json_encode(['success' => true, 'message' => 'No rows modified or row unchanged']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
