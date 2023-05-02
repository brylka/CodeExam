<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();
    redirectIfNotTeacher();

    require_once 'fpdf/fpdf.php';

    // Funkcja zwracająca tablicę z timestampami wersji
    function getVersionsTimestamps($username)
    {
        $dir = "work/" . $username . "/";
        $files = array_diff(scandir($dir), array(".", "..", "current.php"));

        $timestamps = array_filter($files, function ($file) {
            return preg_match("/^\d+$/", $file);
        });

        return array_values($timestamps);
    }

    // Funkcja zwracająca zawartość wersji
    function getVersionContent($timestamp, $username)
    {
        $file_path = 'work/' . $username . '/' . $timestamp;
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);

            // Usuwanie ostatniej linii związanej z adresem IP
            $content_lines = explode("\n", $content);
            $ip_line = array_pop($content_lines);
            $code = implode("\n", $content_lines);

            // Wyciąganie adresu IP z ostatniej linii
            preg_match('/\/\/ IP: (.*)/', $ip_line, $ip_matches);
            $ip = $ip_matches[1];

            return ['ip' => $ip, 'code' => $code];
        }
        return ['ip' => '', 'code' => ''];
    }

    $studentUsername = isset($_GET['username']) ? $_GET['username'] : null;

    if ($studentUsername) {
        $timestamps = getVersionsTimestamps($studentUsername);
        $pdf = new FPDF();

        // Dodanie pierwszej strony z podsumowaniem pracy ucznia
        $pdf->AddPage();
        $pdf->SetFont('Courier', 'B', 12);
        $pdf->MultiCell(0, 6, "Podsumowanie pracy ucznia: " . $studentUsername);
        $pdf->Ln(5);

        $pdf->SetFont('Courier', '', 8);

        // Dodać treść zadania
        if (file_exists('task_content.txt')) {
            $taskContent = "Treść zadania:\n\n" . file_get_contents('task_content.txt');
        } else {
            $taskContent = "Treść zadania:\n\nNie można wczytać pliku z treścią zadania.";
        }
        $pdf->MultiCell(0, 4, $taskContent);

        // Dodanie statystyk
        $pdf->Ln(10);
        $pdf->SetFont('Courier', 'B', 10);
        $pdf->Cell(0, 4, "Statystyki:");
        $pdf->Ln(5);

        $pdf->SetFont('Courier', '', 8);

        // Dodanie informacji o czasie rozpoczęcia i zakończenia pracy
        $startTime = new DateTime("@{$timestamps[0]}");
        $endTime = new DateTime("@{$timestamps[count($timestamps) - 1]}");

        $pdf->Cell(0, 4, "Data i godzina rozpoczęcia pracy: " . $startTime->format('Y-m-d H:i:s'));
        $pdf->Ln(5);

        $pdf->Cell(0, 4, "Data i godzina zakończenia pracy: " . $endTime->format('Y-m-d H:i:s'));
        $pdf->Ln(5);

        $pdf->Cell(0, 4, "Liczba wersji: " . count($timestamps));
        $pdf->Ln(5);

        // Dodanie kolejnych stron z treścią wersji zadania
        $firstTimestamp = true;
        foreach ($timestamps as $timestamp) {
            $pdf->AddPage();
            $versionDate = new DateTime("@$timestamp");
            $formattedDate = $versionDate->format('Y-m-d H:i:s');
            $content_data = getVersionContent($timestamp, $studentUsername);
            $ip = $content_data['ip'];
            $content = $content_data['code'];
            $content = str_replace("\t", "    ", $content);

            $pdf->MultiCell(0, 4, 'Timestamp: ' . $formattedDate . "\n" . 'IP: ' . $ip . "\n" . 'Content:' . "\n" . $content . "\n\n");

            $firstTimestamp = false;
        }

        $pdf->Output('I', 'student_progress.pdf');
    } else {
        echo "Invalid or missing username.";
    }