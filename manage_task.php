<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();
    redirectIfNotTeacher();

    function get_next_task_file($dir) {
        $files = glob($dir . '/task_*.txt');
        $highest_number = 0;
        foreach ($files as $file) {
            preg_match('/task_(\d+)\.txt/', $file, $matches);
            if (isset($matches[1])) {
                $file_number = intval($matches[1]);
                $highest_number = max($highest_number, $file_number);
            }
        }
        return 'task_' . ($highest_number + 1) . '.txt';
    }

    if (isset($_POST['task_title']) && isset($_POST['task_content'])) {
        $task_title = $_POST['task_title'];
        $task_content = $_POST['task_content'];
        $task_dir = 'tasks';

        $task_file = $_POST['task_file'] ?? null;

        if ($task_file == 'NEW') {
            $task_file = get_next_task_file($task_dir);
        }

        $task_path = $task_dir . '/' . $task_file;
        $content_with_title = $task_title . "\n" . $task_content;

        if (file_put_contents($task_path, $content_with_title) !== false) {
            echo json_encode(['status' => 'success', 'task_file' => $task_file]);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {
        echo json_encode(['status' => 'error']);
    }