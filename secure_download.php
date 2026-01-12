<?php
if (!isset($_GET['file'])) {
    die("No file specified.");
}

/*
 Expected format from JS:
 file=ebooks/satan_get_lost.pdf
*/

// Prevent directory traversal
$file = str_replace(['../', '..\\'], '', $_GET['file']);

$filepath = __DIR__ . '/' . $file;

if (!file_exists($filepath)) {
    die("File not found.");
}

// Force download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . basename($filepath) . "\"");
header("Content-Length: " . filesize($filepath));
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: public");

readfile($filepath);
exit;
