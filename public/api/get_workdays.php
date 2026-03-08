<?php
// public/api/get_workdays.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $month = $_GET['month'] ?? null; // YYYY-MM

    if ($month) {
        // Validate format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid month format. Use YYYY-MM.']);
            exit;
        }
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start)); // Last day of month

        $stmt = $pdo->prepare("
            SELECT * FROM work_days
            WHERE work_date >= :start AND work_date <= :end
            ORDER BY work_date ASC
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
    } else {
        $stmt = $pdo->query("SELECT * FROM work_days ORDER BY work_date ASC");
    }

    $data = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
