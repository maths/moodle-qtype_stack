<?php

function install_language($requestedlanguage) {

    $destinationDir = __DIR__ . "/../../lang/{$requestedlanguage}";
    $downloadUrl = "https://packaging.moodle.org/langpack/5.1/{$requestedlanguage}.zip";
    $tempDir = sys_get_temp_dir() . '/moodle_langpack_' . time();
    $zipFile = "{$tempDir}/{$requestedlanguage}.zip";

    try {
        if (str_contains($requestedlanguage, '\\') || str_contains($requestedlanguage, '/') || str_contains($requestedlanguage, '..')) {
            throw new Exception("Dubious requested language {$requestedlanguage}");
        }
    
        // Create temporary directory
        if (!mkdir($tempDir)) {
            throw new Exception("Failed to make directory {$tempDir}");
        }

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $downloadUrl);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlSession,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $zipContent = curl_exec($curlSession);
        $httpCode = curl_getinfo($curlSession, CURLINFO_HTTP_CODE);
        curl_close($curlSession);

        if ($zipContent === false) {
            throw new Exception("Failed to download language pack from {$downloadUrl}");
        }

        // Save the zip file
        if (file_put_contents($zipFile, $zipContent) === false) {
            throw new Exception("Failed to save zip file to {$zipFile}");
        }

        // Extract the zip file
        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            throw new Exception("Failed to open zip file: {$zipFile}");
        }
        if (!$zip->extractTo($tempDir)) {
            throw new Exception("Failed to extract zip file: {$zipFile}");
        }
        $zip->close();

        // Check if source file exists
        $sourceFile = $tempDir . '/' . $requestedlanguage . '/qtype_stack.php';
        if (!file_exists($sourceFile)) {
            throw new Exception("Source file not found: {$sourceFile}");
        }

        // Create destination directory if it doesn't exist
        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true)) {
                throw new Exception("Failed to create destination directory: {$destinationDir}");
            }
        }

        // Copy the file to destination
        $destinationFile = $destinationDir . '/qtype_stack.php';
        if (!copy($sourceFile, $destinationFile)) {
            throw new Exception("Failed to copy file from {$sourceFile} to {$destinationFile}");
        }
    } finally {
        if (is_dir($tempDir)) {
            deleteDirectory($tempDir);
        }
    }
}

function install_language_safe($requestedlanguage) {
    try {
        install_language($requestedlanguage);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Recursively deletes a directory and all its contents.
 *
 * @param string $dir The directory path to delete.
 * @return bool True on success, false on failure.
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
    }
    return rmdir($dir);
}
