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
        file_put_contents("{$studentPath}/{$timestamp}", $code);
    }