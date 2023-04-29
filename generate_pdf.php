<?php
require_once 'fpdf/fpdf.php';

function getVersionsTimestamps()
{
    $dir = "work/";
    $files = array_diff(scandir($dir), array(".", "..", "current.php"));

    $timestamps = array_filter($files, function ($file) {
        return preg_match("/^\d+$/", $file);
    });

    return array_values($timestamps);
}

function getVersionContent($timestamp)
{
    $file_path = 'work/' . $timestamp;
    if (file_exists($file_path)) {
        return file_get_contents($file_path);
    }
    return '';
}

$timestamps = getVersionsTimestamps();
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Courier', '', 8);

$firstTimestamp = true;
foreach ($timestamps as $timestamp) {
    if (!$firstTimestamp) {
        $pdf->AddPage();
    }

    $versionDate = new DateTime("@$timestamp");
    $formattedDate = $versionDate->format('Y-m-d H:i:s');
    $content = getVersionContent($timestamp);
    $content = str_replace("\t", "    ", $content);

    // Set smaller line spacing
    $pdf->MultiCell(0, 4, 'Timestamp: ' . $formattedDate . "\n" . 'Content:' . "\n" . $content . "\n\n");

    $firstTimestamp = false;
}

$pdf->Output('I', 'student_progress.pdf');