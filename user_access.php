<?php
    function redirectIfNotLoggedIn() {
        if (!isset($_COOKIE['userType']) || !isset($_COOKIE['username'])) {
            header("Location: login.php");
            exit;
        }
    }

    function redirectIfNotTeacher() {
        if (isset($_COOKIE['userType']) && $_COOKIE['userType'] != 'teacher') {
            header("Location: index.php");
            exit;
        }
    }