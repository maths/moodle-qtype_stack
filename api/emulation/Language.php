<?php

/**
 * Install a Moodle language pack in the API.
 * @param mixed $requestedlanguage
 * @throws Exception
 * @return void
 */
function install_language($requestedlanguage) {
    if ($requestedlanguage === 'en') {
        return;
    } 

    $destinationdir = __DIR__ . "/../../lang/{$requestedlanguage}";
    $downloadurl = "https://packaging.moodle.org/langpack/5.1/{$requestedlanguage}.zip";
    $tempdir = sys_get_temp_dir() . '/moodle_langpack_' . time();
    $zipfile = "{$tempdir}/{$requestedlanguage}.zip";
    $sourcefile = null;

    try {
        if (str_contains($requestedlanguage, '\\') || str_contains($requestedlanguage, '/') || str_contains($requestedlanguage, '..')) {
            throw new Exception("Dubious requested language {$requestedlanguage}");
        }
    
        // Create temporary directory
        if (!mkdir($tempdir)) {
            throw new Exception("Failed to make directory {$tempdir}");
        }

        $curlsession = curl_init();
        curl_setopt($curlsession, CURLOPT_URL, $downloadurl);
        curl_setopt($curlsession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlsession,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $zipcontent = curl_exec($curlsession);
        $httpcode = curl_getinfo($curlsession, CURLINFO_HTTP_CODE);
        curl_close($curlsession);

        if ($httpcode === 404) {
            $sourcefile = sys_get_temp_dir() . '/qtype_stack.php';
            file_put_contents($sourcefile, '');
            if (!file_exists($sourcefile)) {
                throw new Exception("Fake source file not created: {$sourcefile}");
            }
        } else {
            if ($zipcontent === false) {
                throw new Exception("Failed to download language pack from {$downloadurl}", $httpcode);
            }

            // Save the zip file
            if (file_put_contents($zipfile, $zipcontent) === false) {
                throw new Exception("Failed to save zip file to {$zipfile}");
            }

            // Extract the zip file
            $zip = new ZipArchive();
            if ($zip->open($zipfile) !== true) {
                throw new Exception("Failed to open zip file: {$zipfile}");
            }
            if (!$zip->extractTo($tempdir)) {
                throw new Exception("Failed to extract zip file: {$zipfile}");
            }
            $zip->close();
            // Check if source file exists
            $sourcefile = $tempdir . '/' . $requestedlanguage . '/qtype_stack.php';
            if (!file_exists($sourcefile)) {
                $sourcefile = sys_get_temp_dir() . '/qtype_stack.php';
                file_put_contents($sourcefile, '');
                if (!file_exists($sourcefile)) {
                    throw new Exception("Fake source file not created: {$sourcefile}");
                }
            }
        }

        // Create destination directory if it doesn't exist
        if (!is_dir($destinationdir)) {
            if (!mkdir($destinationdir)) {
                throw new Exception("Failed to create destination directory: {$destinationdir}");
            }
        }

        // Copy the file to destination
        $destinationFile = $destinationdir . '/qtype_stack.php';
        if (!copy($sourcefile, $destinationFile)) {
            throw new Exception("Failed to copy file from {$sourcefile} to {$destinationFile}");
        }
    } finally {
        if (is_dir($tempdir)) {
            delete_directory($tempdir);
        }
    }
}

/**
 * Install languages but catch errors. Used for installing languages on the fly.
 * 
 * @param mixed $requestedlanguage
 * @return bool
 */
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
function delete_directory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                delete_directory($path);
            } else {
                unlink($path);
            }
        }
    }
    return rmdir($dir);
}

/**
 * Return the nearest parent language by removing final qualifier if it exists.
 * e.g. 'en_us_wp' becomes 'en_us', 'en_us' becomes 'en'.
 * @param mixed $lang
 */
function get_parent_language($lang) {
    $lastPos = strrpos($lang, '_');
    if ($lastPos !== false) {
        return substr($lang, 0, $lastPos);
    }
    return $lang;
}
