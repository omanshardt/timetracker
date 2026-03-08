<?php
// public/api/get_balance.php
// Returns per-day deltas and cumulative running balance for all work_days.
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    // 1. Get all work_days entries, ordered by date
    $stmt = $pdo->query("
        SELECT id, work_date, required_time
        FROM work_days
        ORDER BY work_date ASC
    ");
    $work_days = $stmt->fetchAll();

    if (empty($work_days)) {
        echo json_encode([
            'days' => [],
            'total_balance_min' => 0,
            'total_balance_formatted' => '00:00',
            'total_balance_sign' => ''
        ]);
        exit;
    }

    // 2. Get worked minutes per day (only for dates in work_days)
    //    Exclude transfer=0 and PAUSE tasks, same rules as the main summary.
    $worked_stmt = $pdo->query("
        SELECT 
            reporting_date,
            SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS worked_min
        FROM timetracker
        WHERE transfer != 0
          AND task_id NOT LIKE '%PAUSE%'
          AND start_time IS NOT NULL
          AND end_time IS NOT NULL
        GROUP BY reporting_date
        ORDER BY reporting_date ASC
    ");
    $worked_data = $worked_stmt->fetchAll();

    // Index by date for fast lookup
    $worked_by_date = [];
    foreach ($worked_data as $row) {
        $worked_by_date[$row['reporting_date']] = (int) $row['worked_min'];
    }

    // 3. Build per-day result with running balance
    $days = [];
    $cumulative = 0;

    foreach ($work_days as $wd) {
        $date = $wd['work_date'];
        $required_parts = explode(':', $wd['required_time']);
        $required_min = ((int) $required_parts[0]) * 60 + ((int) $required_parts[1]);

        $worked_min = $worked_by_date[$date] ?? 0;
        $delta_min = $worked_min - $required_min;
        $cumulative += $delta_min;

        $days[] = [
            'date' => $date,
            'required_min' => $required_min,
            'required_formatted' => sprintf("%02d:%02d", floor($required_min / 60), $required_min % 60),
            'worked_min' => $worked_min,
            'worked_formatted' => sprintf("%02d:%02d", floor(abs($worked_min) / 60), abs($worked_min) % 60),
            'delta_min' => $delta_min,
            'delta_formatted' => ($delta_min >= 0 ? '+' : '-') . sprintf("%02d:%02d", floor(abs($delta_min) / 60), abs($delta_min) % 60),
            'balance_min' => $cumulative,
            'balance_formatted' => ($cumulative >= 0 ? '+' : '-') . sprintf("%02d:%02d", floor(abs($cumulative) / 60), abs($cumulative) % 60),
        ];
    }

    echo json_encode([
        'days' => $days,
        'total_balance_min' => $cumulative,
        'total_balance_formatted' => ($cumulative >= 0 ? '+' : '-') . sprintf("%02d:%02d", floor(abs($cumulative) / 60), abs($cumulative) % 60),
        'total_balance_sign' => $cumulative >= 0 ? 'positive' : 'negative'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
