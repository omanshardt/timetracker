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

// These might come from a "Copy" action (hidden fields)
$start_time_reported = $input['start_time_reported'] ?? $start_time;
$end_time_reported = $input['end_time_reported'] ?? $end_time;
$task_name = $input['task_name'] ?? null;
$description_long = $input['description_long'] ?? null;
$transfer = isset($input['transfer']) ? (int) $input['transfer'] : 1;
$transfered_intern = isset($input['transfered_intern']) ? (int) $input['transfered_intern'] : 0;
$transfered_jira = isset($input['transfered_jira']) ? (int) $input['transfered_jira'] : 0;

try {
    $stmt = $pdo->prepare("
        INSERT INTO timetracker (
            reporting_date, 
            start_time, 
            end_time, 
            start_time_reported, 
            end_time_reported, 
            task_id, 
            task_name,
            description,
            description_long,
            transfer,
            transfered_intern,
            transfered_jira
        ) VALUES (
            :reporting_date, 
            :start_time, 
            :end_time, 
            :start_time_reported, 
            :end_time_reported, 
            :task_id, 
            :task_name,
            :description,
            :description_long,
            :transfer,
            :transfered_intern,
            :transfered_jira
        )
    ");

    $stmt->execute([
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

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
