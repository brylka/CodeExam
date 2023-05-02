<?php
    setcookie("userType", "", time() - 3600);
    setcookie("username", "", time() - 3600);

    header("Location: login.php");