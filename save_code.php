<?php
    if (isset($_POST['code']) && isset($_POST['username'])) {
        $code = $_POST['code'];
        $username = $_POST['username'];
        $studentPath = "work/{$username}";

        if (!file_exists($studentPath)) {
            mkdir($studentPath);
        }

        file_put_contents("{$studentPath}/current.php", $code);

        $timestamp = time();

        // Zapisywanie adresu IP
        $ip = $_SERVER['REMOTE_ADDR'];
        $code_with_ip = $code . "\n\n// IP: {$ip}";

        file_put_contents("{$studentPath}/{$timestamp}", $code_with_ip);
    }