<?php
    if (isset($_POST['code'])) {
        $code = $_POST['code'];
        $isStudent = isset($_POST['isStudent']) && $_POST['isStudent'] === 'true';

        if ($isStudent) {
            file_put_contents("work/current.php", $code);

            $timestamp = time();
            file_put_contents("work/{$timestamp}", $code);
        }
    }