<?php
    $task_file = 'task_content.txt';

    if (file_exists($task_file)) {
        echo file_get_contents($task_file);
    } else {
        echo '';
    }