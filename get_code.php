<?php
if (file_exists('work/current.php')) {
    $code = file_get_contents('work/current.php');
    echo $code;
} else {
    echo '';
}