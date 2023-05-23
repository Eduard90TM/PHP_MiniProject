<?php
    $task = $_POST['task'];
    $startTime = date('Y-m-d H:i:s');
    $endTime = '';
    $duration = '';

    // Load existing log entries
    $logFile = 'activity.log';
    $log = [];
    if (file_exists($logFile)) {
        $log = file($logFile);
    }

    // Check if a task is already in progress
    if (!empty($log)) {
        $lastActivity = explode(',', $log[count($log) - 1]);
        $lastEndTime = $lastActivity[2];
        if (empty($lastEndTime)) {
            $lastActivity[2] = $startTime;  // Update the end time of the previous task
            $log[count($log) - 1] = implode(',', $lastActivity);
            file_put_contents($logFile, implode('', $log));
        }
    }

    // Append new activity to the log file
    $logEntry = $task . ',' . $startTime . ',' . $endTime . ',' . $duration . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    header('Location: index.php'); // Redirect back to the main page
