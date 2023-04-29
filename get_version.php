<?php
    $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

    if ($timestamp && preg_match("/^\d+$/", $timestamp)) {
        $file_path = "work/" . $timestamp;

        if (file_exists($file_path)) {
            echo file_get_contents($file_path);
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    } else {
        http_response_code(400);
        echo "Invalid timestamp.";
    }
?>