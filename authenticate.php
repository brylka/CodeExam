<?php
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $userType = '';

        if ($username == $teacherCredentials['username'] && $password == $teacherCredentials['password']) {
            $userType = 'teacher';
        } elseif ($password == $studentPassword) {
            $userType = 'student';
        } else {
            header("Location: login.php?error=1");
            exit;
        }

        setcookie("userType", $userType, time() + 3600*8); // Ważność: 8 godzin
        setcookie("username", $username, time() + 3600*8); // Ważność: 8 godzin

        header("Location: index.php");
    } else {
        header("Location: login.php");
    }