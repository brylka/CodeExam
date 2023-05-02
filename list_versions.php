<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();
    redirectIfNotTeacher();

    // Pobierz identyfikator wybranego ucznia
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;

    // Upewnij się, że $student_id został ustawiony
    if ($student_id) {
        // Zmieniamy katalog na katalog ucznia
        $dir = "work/{$student_id}/";
        $files = array_diff(scandir($dir), array(".", "..", "current.php"));

        $timestamps = array_filter($files, function ($file) {
            return preg_match("/^\d+$/", $file);
        });

        echo json_encode(array_values($timestamps));
    } else {
        http_response_code(400);
        echo "Invalid student ID.";
    }