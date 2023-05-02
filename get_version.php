<?php
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

    if ($student_id && $timestamp && preg_match("/^\d+$/", $timestamp)) {
        $file_path = "work/{$student_id}/" . $timestamp;

        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);

            // Usuwanie ostatniej linii związanej z adresem IP
            $content_lines = explode("\n", $content);
            $ip_line = array_pop($content_lines);
            $content = implode("\n", $content_lines);

            // Wyciąganie adresu IP z ostatniej linii
            preg_match('/\/\/ IP: (.*)/', $ip_line, $ip_matches);
            $ip = $ip_matches[1];

            // Zwracanie danych jako JSON
            $version_data = [
                'content' => $content,
                'ip' => $ip,
            ];
            echo json_encode($version_data);
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    } else {
        http_response_code(400);
        echo "Invalid student ID or timestamp.";
    }