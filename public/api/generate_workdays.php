<?php
// public/api/generate_workdays.php
// Bulk-generate work_days entries for a given month (Mon-Thu, 8h each).
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

$month = $input['month'] ?? null; // YYYY-MM

if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid field: month (YYYY-MM)']);
    exit;
}

$required_time = $input['required_time'] ?? '08:00:00';
if (preg_match('/^\d{2}:\d{2}$/', $required_time)) {
    $required_time .= ':00';
}

$start = new DateTime($month . '-01');
$end = new DateTime($start->format('Y-m-t'));

try {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO work_days (work_date, required_time)
        VALUES (:work_date, :required_time)
    ");

    $inserted = 0;
    $skipped = 0;
    $current = clone $start;

    while ($current <= $end) {
        $dayOfWeek = (int) $current->format('N'); // 1=Mon, 7=Sun

        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            $stmt->execute([
                'work_date' => $current->format('Y-m-d'),
                'required_time' => $required_time,
            ]);

            if ($stmt->rowCount() > 0) {
                $inserted++;
            } else {
                $skipped++;
            }
        }

        $current->modify('+1 day');
    }

    echo json_encode([
        'success' => true,
        'inserted' => $inserted,
        'skipped' => $skipped,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
