<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();

    if (isset($_GET['username']) && isset($_GET['task_file'])) {
        $username = $_GET['username'];
        $task_file = $_GET['task_file'];
        $studentPath = "work/{$username}/{$task_file}";

        if (file_exists("{$studentPath}/current.php")) {
            $code = file_get_contents("{$studentPath}/current.php");
            echo $code;
        } else {
            echo '';
        }
    }