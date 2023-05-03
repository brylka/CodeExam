<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();

    if (isset($_POST['code']) && isset($_POST['username']) && isset($_POST['task_file'])) {
        $code = $_POST['code'];
        $username = $_POST['username'];
        $task_file = $_POST['task_file'];
        $studentPath = "work/{$username}/{$task_file}";

        if (!file_exists($studentPath)) {
            mkdir($studentPath, 0777, true);
        }

        file_put_contents("{$studentPath}/current.php", $code);

        $timestamp = time();

        // Zapisywanie adresu IP
        $ip = $_SERVER['REMOTE_ADDR'];
        $code_with_ip = $code . "\n\n// IP: {$ip}";

        file_put_contents("{$studentPath}/{$timestamp}", $code_with_ip);
    }