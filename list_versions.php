<?php
    $dir = "work/";
    $files = array_diff(scandir($dir), array(".", "..", "current.php"));

    $timestamps = array_filter($files, function ($file) {
        return preg_match("/^\d+$/", $file);
    });

    echo json_encode(array_values($timestamps));