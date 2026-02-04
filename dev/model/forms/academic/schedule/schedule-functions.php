<?php

function generateScheduleTable($jsonData) {
    // Decode the JSON
    $data = json_decode($jsonData, true);

    // Define days and time range
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $startHour = 6;  // 6 AM
    $endHour = 21;   // 8 PM (to cover evening classes)

    // Initialize table
    $table = '<table class="table table-bordered text-center align-middle" id="schedule-table">';
    $table .= '<thead class="table-primary"><tr><th>Time</th>';
    foreach ($days as $day) $table .= "<th>{$day}</th>";
    $table .= '</tr></thead><tbody>';

    // Convert schedule entries into a lookup
    $scheduleMap = [];
    foreach ($data as $subject) {
        preg_match('/(\w+)\s+(\d{1,2}:\d{2}\s[APM]{2})-(\d{1,2}:\d{2}\s[APM]{2})/', $subject['SCHEDULE'], $matches);
        if (count($matches) === 4) {
            $day = ucfirst(strtolower($matches[1]));
            $start = date('G', strtotime($matches[2]));
            $end = date('G', strtotime($matches[3]));
            $scheduleMap[$day][] = [
                'start' => $start,
                'end' => $end,
                'code' => $subject['CODE'],
                'desc' => $subject['DESC'],
                'instructor' => $subject['INSTRUCTOR'],
                'time' => "{$matches[2]} - {$matches[3]}"
            ];
        }
    }

    // Track used cells (for rowspan skipping)
    $used = [];
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $nextHour = $hour + 1;
        $timeLabel = date('g:i', strtotime("$hour:00")) . ' - ' . date('g:i', strtotime("$nextHour:00"));
        $table .= "<tr><td class='time-col'>{$timeLabel}</td>";

        foreach ($days as $day) {
            // Skip cells already used by rowspan
            if (isset($used[$day][$hour])) {
                continue;
            }

            $entryFound = false;

            // Check if there's a subject that starts at this hour
            if (isset($scheduleMap[$day])) {
                foreach ($scheduleMap[$day] as $subj) {
                    if ($subj['start'] == $hour) {
                        $rowspan = $subj['end'] - $subj['start'];
                        // Mark the covered hours as used
                        for ($i = 0; $i < $rowspan; $i++) {
                            $used[$day][$hour + $i] = true;
                        }

                        $table .= "<td rowspan='{$rowspan}' class='bg-body-secondary'>
                            <div class='subject'>
                                <p class='mb-0'><strong>{$subj['code']}</strong></p>
                                <p class='mb-0'>{$subj['desc']}</p>
                                <!-- <p class='mb-0'>{$subj['time']}</p> -->
                                <p class='mb-0'>{$subj['instructor']}</p>
                            </div>
                        </td>";
                        $entryFound = true;
                        break;
                    }
                }
            }

            // Empty cell if no subject
            if (!$entryFound) {
                $table .= "<td></td>";
            }
        }

        $table .= "</tr>";
    }

    $table .= '</tbody></table>';
    return $table;
}