<?php
$task_file = 'task_content.txt';

if (isset($_POST['content'])) {
    $content = $_POST['content'];

    if (file_put_contents($task_file, $content) !== false) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error']);
}