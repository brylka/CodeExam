<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();

    $task_file = isset($_GET['task_file']) ? $_GET['task_file'] : '';
    $task_file = "tasks/{$task_file}";

    if (file_exists($task_file)) {
        $file_contents = file_get_contents($task_file);
        $lines = explode("\n", $file_contents);
        $title = $lines[0];
        $content = preg_replace('/^.*\n/', '', $file_contents); // Usuwa pierwszą linię z pliku
        echo json_encode(['title' => $title, 'content' => $content]);
    } else {
        echo json_encode(['title' => '', 'content' => '']);
    }