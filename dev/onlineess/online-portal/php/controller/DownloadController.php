<?php
session_start();
include '../configuration/connection-config.php';

if(isset($_GET['id_loc'])) {   
    // Clear all output buffers first
    while (ob_get_level()) {
        ob_end_clean();
    }

    $fileName = basename($_GET['id_loc']); 
    $filePath = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $reg_req_path . $_GET['id_loc']);

    // Security check - verify the path is within allowed directory
    $allowedPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $reg_req_path);
    if (strpos($filePath, $allowedPath) !== 0) {
        header("HTTP/1.0 403 Forbidden");
        die("Access denied");
    }

    if(file_exists($filePath) && is_file($filePath)) {
        // Get proper MIME type
        $mimeType = mime_content_type($filePath);
        
        // Set headers
        header("Content-Description: File Transfer"); 
        header("Content-Type: " . $mimeType);
        header("Content-Disposition: attachment; filename=\"" . rawurlencode($fileName) . "\""); 
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filePath));
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        
        // Disable output compression
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // Read the file in chunks (better for large files)
        $chunkSize = 1024 * 1024; // 1MB chunks
        $handle = fopen($filePath, 'rb');
        
        if ($handle === false) {
            header("HTTP/1.0 500 Internal Server Error");
            die("Cannot open file");
        }

        while (!feof($handle)) {
            echo fread($handle, $chunkSize);
            flush();
        }
        
        fclose($handle);
        exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        die("File not found");
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    die("Invalid request");
}