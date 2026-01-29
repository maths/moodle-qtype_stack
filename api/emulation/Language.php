<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language pack functions for STACK API.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class ApiLanguage {
    /**
     * Install a Moodle language pack in the API.
     * @param mixed $requestedlanguage
     * @throws Exception
     * @return void
     */
    public static function install_language($requestedlanguage) {
        if ($requestedlanguage === 'en') {
            return;
        }

        $destinationdir = __DIR__ . "/../../lang/{$requestedlanguage}";
        $downloadurl = "https://packaging.moodle.org/langpack/5.1/{$requestedlanguage}.zip";
        $tempdir = sys_get_temp_dir() . '/moodle_langpack_' . time();
        $zipfile = "{$tempdir}/{$requestedlanguage}.zip";
        $sourcefile = null;

        try {
            if (
                str_contains($requestedlanguage, '\\') ||
                str_contains($requestedlanguage, '/') ||
                str_contains($requestedlanguage, '..')
            ) {
                throw new Exception("Dubious requested language {$requestedlanguage}");
            }

            // Create temporary directory.
            if (!mkdir($tempdir)) {
                throw new Exception("Failed to make directory {$tempdir}");
            }

            $curlsession = curl_init();
            curl_setopt($curlsession, CURLOPT_URL, $downloadurl);
            curl_setopt($curlsession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $curlsession,
                CURLOPT_USERAGENT,
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
            );

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

                // Save the zip file.
                if (file_put_contents($zipfile, $zipcontent) === false) {
                    throw new Exception("Failed to save zip file to {$zipfile}");
                }

                // Extract the zip file.
                $zip = new ZipArchive();
                if ($zip->open($zipfile) !== true) {
                    throw new Exception("Failed to open zip file: {$zipfile}");
                }
                if (!$zip->extractTo($tempdir)) {
                    throw new Exception("Failed to extract zip file: {$zipfile}");
                }
                $zip->close();
                // Check if source file exists.
                $sourcefile = $tempdir . '/' . $requestedlanguage . '/qtype_stack.php';
                if (!file_exists($sourcefile)) {
                    $sourcefile = sys_get_temp_dir() . '/qtype_stack.php';
                    file_put_contents($sourcefile, '');
                    if (!file_exists($sourcefile)) {
                        throw new Exception("Fake source file not created: {$sourcefile}");
                    }
                }
            }

            // Create destination directory if it doesn't exist.
            if (!is_dir($destinationdir)) {
                if (!mkdir($destinationdir)) {
                    throw new Exception("Failed to create destination directory: {$destinationdir}");
                }
            }

            // Copy the file to destination.
            $destinationfile = $destinationdir . '/qtype_stack.php';
            if (!copy($sourcefile, $destinationfile)) {
                throw new Exception("Failed to copy file from {$sourcefile} to {$destinationfile}");
            }
        } finally {
            if (is_dir($tempdir)) {
                self::delete_directory($tempdir);
            }
        }
    }

    /**
     * Install languages but catch errors. Used for installing languages on the fly.
     *
     * @param string $requestedlanguage
     * @return bool
     */
    public static function install_language_safe($requestedlanguage) {
        try {
            self::install_language($requestedlanguage);
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
    public static function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    self::delete_directory($path);
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
    public static function get_next_parent_language($lang) {
        $lastpos = strrpos($lang, '_');
        if ($lastpos !== false) {
            return substr($lang, 0, $lastpos);
        }
        return $lang;
    }

    /**
     * Separated out for testing.
     * @return string|null
     */
    public static function api_current_language($requestheader) {
        $locale = locale_parse($requestheader);
        $languages = [];
        $requestedlanguage = strtolower($locale['language']) ?? 'en';
        $languages[] = $requestedlanguage;
        $requestedregion = null;
        if (!empty($locale['region'])) {
            $requestedregion = $requestedlanguage . '_' . strtolower($locale['region']);
            $languages[] = $requestedregion;
        }
        if (!empty($locale['variant0']) && $requestedregion) {
            $languages[] = $requestedregion . '_' . strtolower($locale['variant0']);
        }
        $supportedlanguages = get_config('qtype_stack', 'supportedlanguages');
        $supportedlanguages = (!empty($supportedlanguages)) ? $supportedlanguages : 'en,de';
        $supportedlanguages = explode(',', $supportedlanguages);

        if (in_array('*', $supportedlanguages)) {
            $currentlang = 'en';
            foreach ($languages as $lang) {
                if (!in_array($lang, $supportedlanguages) && !is_file(__DIR__ . "/../../lang/{$lang}/qtype_stack.php")) {
                    $success = static::install_language_safe($lang);
                    $currentlang = ($success) ? $lang : $currentlang;
                } else {
                    $currentlang = $lang;
                }
            }
            return $currentlang;
        }

        $languages = array_reverse($languages);
        foreach ($languages as $lang) {
            if (in_array($lang, $supportedlanguages)) {
                return $lang;
            }
        }

        return locale_lookup($supportedlanguages, $requestedlanguage, true, 'en');
    }
}
