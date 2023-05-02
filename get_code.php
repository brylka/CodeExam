<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();

    if (isset($_GET['username'])) {
        $username = $_GET['username'];
        $studentPath = "work/{$username}";

        if (file_exists("{$studentPath}/current.php")) {
            $code = file_get_contents("{$studentPath}/current.php");
            echo $code;
        } else {
            echo '';
        }
    }