<?php
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

    if ($student_id && $timestamp && preg_match("/^\d+$/", $timestamp)) {
        $file_path = "work/{$student_id}/" . $timestamp;

        if (file_exists($file_path)) {
            echo file_get_contents($file_path);
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    } else {
        http_response_code(400);
        echo "Invalid student ID or timestamp.";
    }