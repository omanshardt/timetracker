<?php
/**
 * Populate work_days table with Mon-Thu entries (8:00 hours each).
 *
 * Usage:
 *   php scripts/populate_workdays.php                     # Current year
 *   php scripts/populate_workdays.php 2026-01-01 2026-12-31  # Custom range
 */

require_once __DIR__ . '/../config/db.php';

// Parse arguments
$start_date = $argv[1] ?? date('Y') . '-01-01';
$end_date   = $argv[2] ?? date('Y') . '-12-31';

$required_time = '08:00:00';

// Validate dates
$start = new DateTime($start_date);
$end   = new DateTime($end_date);

if ($start > $end) {
    echo "Error: start_date must be before end_date.\n";
    exit(1);
}

echo "Populating work_days from {$start_date} to {$end_date} (Mon-Thu, {$required_time})...\n";

$stmt = $pdo->prepare("
    INSERT IGNORE INTO work_days (work_date, required_time)
    VALUES (:work_date, :required_time)
");

$inserted = 0;
$skipped  = 0;
$current  = clone $start;

while ($current <= $end) {
    $dayOfWeek = (int) $current->format('N'); // 1=Mon, 7=Sun

    // Monday (1) through Thursday (4)
    if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
        $dateStr = $current->format('Y-m-d');
        $result = $stmt->execute([
            'work_date'     => $dateStr,
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

echo "Done! Inserted: {$inserted}, Skipped (already existed): {$skipped}\n";
