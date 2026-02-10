<?php
// public/api/get_day_data.php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? date('Y-m-d');

try {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM timetracker 
        WHERE reporting_date = :date 
        ORDER BY start_time ASC
    ");
    $stmt->execute(['date' => $date]);
    $raw_data = $stmt->fetchAll();

    // --- Helper Functions ---

    function calculate_duration($start, $end)
    {
        if (!$start || !$end)
            return 0;
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        return max(0, ($end_ts - $start_ts) / 60); // minutes
    }

    function format_duration($minutes)
    {
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return sprintf("%02d:%02d", $h, $m);
    }

    // --- 1. Process Raw Data (Gaps/Overlaps) ---
    $processed_raw = [];
    $prev_end_real = null;
    $prev_end_tracking = null;

    // Filter for interaction analysis (exclude transfer=0)
    // We need to know the "previous valid row" for gap calculation
    $last_valid_real_idx = -1;
    $last_valid_tracking_idx = -1;

    foreach ($raw_data as $i => $row) {
        $row['duration_real_min'] = calculate_duration($row['start_time'], $row['end_time']);
        $row['duration_tracking_min'] = calculate_duration($row['start_time_reported'], $row['end_time_reported']);

        $row['duration_real_formatted'] = format_duration($row['duration_real_min']);
        $row['duration_tracking_formatted'] = format_duration($row['duration_tracking_min']);

        // Defaults
        $row['gap_real'] = null; // 'gap', 'overlap', or null
        $row['gap_tracking'] = null;

        // Gap/Overlap Logic (Real Time)
        // Must include PAUSE, Must NOT include transfer=0
        if ($row['transfer'] != 0) {
            if ($last_valid_real_idx !== -1) {
                // Compare with previous valid row's end time
                $prev_row = $processed_raw[$last_valid_real_idx];
                $prev_end = $prev_row['end_time'];
                $curr_start = $row['start_time'];

                if ($prev_end && $curr_start) {
                    if ($curr_start > $prev_end) {
                        $processed_raw[$last_valid_real_idx]['gap_real_next'] = 'gap';
                        $row['gap_real_prev'] = 'gap';
                    } elseif ($curr_start < $prev_end) {
                        $processed_raw[$last_valid_real_idx]['gap_real_next'] = 'overlap';
                        $row['gap_real_prev'] = 'overlap';
                    }
                }
            }
            $last_valid_real_idx = $i;
        }

        // Gap/Overlap Logic (Tracking Time)
        if ($row['transfer'] != 0) {
            if ($last_valid_tracking_idx !== -1) {
                $prev_row = $processed_raw[$last_valid_tracking_idx];
                $prev_end = $prev_row['end_time_reported'];
                $curr_start = $row['start_time_reported'];

                if ($prev_end && $curr_start) {
                    if ($curr_start > $prev_end) {
                        $processed_raw[$last_valid_tracking_idx]['gap_tracking_next'] = 'gap';
                        $row['gap_tracking_prev'] = 'gap';
                    } elseif ($curr_start < $prev_end) {
                        $processed_raw[$last_valid_tracking_idx]['gap_tracking_next'] = 'overlap';
                        $row['gap_tracking_prev'] = 'overlap';
                    }
                }
            }
            $last_valid_tracking_idx = $i;
        }

        $processed_raw[] = $row;
    }

    // --- Aggregation Helper ---
    function aggregate_rows($rows)
    {
        if (empty($rows))
            return null;

        $first = $rows[0];
        $is_pause = (strpos($first['task_id'], 'PAUSE') !== false);

        // Times
        $start_real = $rows[0]['start_time'];
        $end_real = $rows[count($rows) - 1]['end_time']; // Assuming sorted by time

        // For reported, we usually want min/max
        $starts_reported = array_column($rows, 'start_time_reported');
        $ends_reported = array_column($rows, 'end_time_reported');
        // Filter out nulls if any
        $starts_reported = array_filter($starts_reported);
        $ends_reported = array_filter($ends_reported);

        $start_tracking = !empty($starts_reported) ? min($starts_reported) : null;
        $end_tracking = !empty($ends_reported) ? max($ends_reported) : null;

        // Descriptions
        $descriptions = [];
        foreach ($rows as $r) {
            if (!empty($r['description'])) {
                $descriptions[] = $r['description'];
            }
        }
        $desc_str = implode("<br>", $descriptions);

        // Transfer Status
        $intern_counts = array_count_values(array_column($rows, 'transfered_intern'));
        $jira_counts = array_count_values(array_column($rows, 'transfered_jira'));

        $count = count($rows);

        $get_status = function ($counts, $total) {
            $ones = $counts[1] ?? 0;
            if ($ones == $total)
                return 'green';
            if ($ones == 0)
                return 'red';
            return 'yellow';
        };

        $status_intern = $get_status($intern_counts, $count);
        $status_jira = $get_status($jira_counts, $count);

        $dur_real = calculate_duration($start_real, $end_real);
        $dur_tracking = calculate_duration($start_tracking, $end_tracking);

        return [
            'task_id' => $first['task_id'],
            'description' => $desc_str,
            'start_real' => $start_real,
            'end_real' => $end_real,
            'start_tracking' => $start_tracking,
            'end_tracking' => $end_tracking,
            'duration_real_formatted' => format_duration($dur_real),
            'duration_tracking_formatted' => format_duration($dur_tracking),
            'status_intern' => $status_intern,
            'status_jira' => $status_jira,
            'is_pause' => $is_pause
        ];
    }

    // --- 2. Consecutive Aggregation ---
    // Rule: Exclude transfer=0
    $consecutive_data = [];
    $current_chunk = [];
    $last_task_id = null;

    foreach ($processed_raw as $row) {
        if ($row['transfer'] == 0)
            continue;

        if ($last_task_id !== null && $row['task_id'] != $last_task_id) {
            $consecutive_data[] = aggregate_rows($current_chunk);
            $current_chunk = [];
        }
        $current_chunk[] = $row;
        $last_task_id = $row['task_id'];
    }
    if (!empty($current_chunk)) {
        $consecutive_data[] = aggregate_rows($current_chunk);
    }

    // --- 3. Grouped Aggregation ---
    // Rule: Exclude transfer=0. Group ALL by ID.
    $grouped_map = [];
    foreach ($processed_raw as $row) {
        if ($row['transfer'] == 0)
            continue;
        $tid = $row['task_id'];
        if (!isset($grouped_map[$tid])) {
            $grouped_map[$tid] = [];
        }
        $grouped_map[$tid][] = $row;
    }

    $grouped_data = [];
    foreach ($grouped_map as $tid => $rows) {
        // For grouped, start is min(all starts), end is max(all ends) -> WAIT.
        // If I have 9-10 and 14-15.
        // "smallest start_time ... and largest end_time"
        // 9:00 ... 15:00. 
        // Duration? "calculated: end_time - start_time".
        // If the prompt strictly means diff between min-start and max-end, that includes the gap.
        // "Duration (calculated: end_time - start_time in hh:mm)" -> This usually implies simple arithmetic on the displayed bounds.
        // Let's stick to the prompt's instruction.

        // However, for Multi-Block groups, the description aggregation logic is simpler.
        // Let's reuse aggregate_rows but we need to sort them by time first to ensure start/end logic holds if needed,
        // although min/max works regardless of order.
        $grouped_data[] = aggregate_rows($rows);
    }

    // --- 4. Summaries (Footer) ---
    // Sum up time periods. 
    // Exclude transfer=0 AND PAUSE.
    $sum_real = 0;
    $sum_tracking = 0;
    foreach ($processed_raw as $row) {
        if ($row['transfer'] == 0)
            continue;
        if (strpos($row['task_id'], 'PAUSE') !== false)
            continue;

        $sum_real += calculate_duration($row['start_time'], $row['end_time']);
        $sum_tracking += calculate_duration($row['start_time_reported'], $row['end_time_reported']);
    }

    echo json_encode([
        'raw' => $processed_raw,
        'consecutive' => $consecutive_data,
        'grouped' => $grouped_data,
        'summary' => [
            'real' => format_duration($sum_real),
            'tracking' => format_duration($sum_tracking)
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
