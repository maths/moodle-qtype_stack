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
 * Allows users to view and import questions from a library of samples.
 *
 * @package   qtype_stack
 * @copyright 2024 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
 defined('MOODLE_INTERNAL') || die();

 require_once(__DIR__ . '../../api/util/StackSeedHelper.php');
 require_once(__DIR__ . '../../api/util/StackPlotReplacer.php');

 use core_question\local\bank\question_edit_contexts;
 use api\util\StackSeedHelper;
 use api\util\StackPlotReplacer;

/**
 * Functions required to display the STACK question library
 * @package   qtype_stack
 */
class stack_question_library {
    /** @var int increments unique folder ids */
    public static $dircount = 1;
    /**
     * GITHUB library identifier
     * @var string
     */
    public const GITHUB = 'githublibrary';
    /**
     * Site library identifier
     * @var string
     */
    public const SITELIB = 'sitelibrary';
    /**
     * STACK library identifier
     * @var string
     */
    public const STACKLIB = 'stacklibrary';
    /**
     * ATLAS API identifier
     * @var string
     */
    public const NRWSEARCH = 'nrwsearch';
    /**
     * ATLAS API base URL
     * @var string
     */
    public const NRWAPIBASE = 'https://vmits1614.vm.ruhr-uni-bochum.de/dev/api';

    /**
     * Stable setting key for an entry in the external library catalogue.
     *
     * Moodle setting names may only contain letters, digits and underscores,
     * while catalogue keys can be any text. A short hash of the key gives a valid
     * name that stays the same when the library's display name changes.
     * @param string $libraryid Catalogue key (independent of the display name)
     * @return string Encrypted configuration setting name
     */
    public static function github_token_setting(string $libraryid): string {
        return 'githubtoken_' . substr(hash('sha256', $libraryid), 0, 16);
    }

    /**
     * Wrapper for curl_exec to allow mocking in unit tests.
     *
     * @param resource $ch cURL handle
     * @return string|bool
     */
    protected static function execute_curl_request($ch) {
        return curl_exec($ch);
    }

    /**
     * Wrapper for curl_getinfo to allow mocking in unit tests.
     *
     * @param resource $ch cURL handle
     * @param int $opt cURL info option
     * @return mixed
     */
    protected static function get_curl_info($ch, int $opt) {
        return curl_getinfo($ch, $opt);
    }

    /**
     * Create a Moodle HTTP client; overridable for transport tests.
     * @return \curl HTTP client without response caching
     */
    protected static function github_client(): \curl {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        return new \curl(['cache' => false]);
    }

    /**
     * Build GitHub headers using an encrypted Moodle setting.
     *
     * @param string $setting Encrypted configuration setting name
     * @return array HTTP headers
     */
    protected static function github_headers(string $setting = 'githubtoken'): array {
        $headers = ['User-Agent: Moodle-STACK', 'Accept: application/vnd.github.v3+json'];
        $encrypted = get_config('qtype_stack', $setting);
        $token = $encrypted ? \core\encryption::decrypt($encrypted) : '';
        if ($token !== false && $token !== '' && !preg_match('/[\r\n]/', $token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        return $headers;
    }

    /**
     * Fetch GitHub data and retain only numeric diagnostic headers.
     *
     * @param string $url Request URL
     * @param int $timeout Request timeout in seconds; zero uses the cURL default
     * @param string $setting Encrypted configuration setting name
     * @return array Response body, HTTP status, safe diagnostic text and numeric diagnostic headers
     */
    protected static function request_github(string $url, int $timeout = 0, string $setting = 'githubtoken'): array {
        $parts = parse_url($url);
        if (
            ($parts['scheme'] ?? '') !== 'https' || ($parts['host'] ?? '') !== 'api.github.com' ||
                ($parts['port'] ?? 443) !== 443 || isset($parts['user']) || isset($parts['pass'])
        ) {
            throw new \stack_exception('GitHub requests require an HTTPS api.github.com URL on port 443 without credentials.');
        }
        $client = static::github_client();
        $client->setHeader(static::github_headers($setting));
        $response = $client->get($url, [], [
            'CURLOPT_USERAGENT' => 'Moodle-STACK',
            'CURLOPT_TIMEOUT' => $timeout,
            'CURLOPT_CONNECTTIMEOUT' => 300,
            'CURLOPT_FOLLOWLOCATION' => false,
            'CURLOPT_SSL_VERIFYPEER' => true,
            'CURLOPT_SSL_VERIFYHOST' => 2,
        ]);
        $status = (int) ($client->get_info()['http_code'] ?? 0);
        $errno = $client->get_errno();
        // Moodle collects the headers. Select rate-limit numbers for errors and fallback decisions.
        $headers = array_change_key_case($client->getResponse(), CASE_LOWER);
        $diagnostics = [];
        foreach (['x-ratelimit-remaining', 'x-ratelimit-reset', 'retry-after'] as $name) {
            $value = $headers[$name] ?? null;
            if (is_string($value) && ctype_digit(trim($value))) {
                $diagnostics[$name] = trim($value);
            }
        }
        // Moodle can return error text when a URL is blocked; do not treat it as a response body.
        if ($errno || !$status) {
            $response = false;
        }
        $details = 'HTTP ' . $status . ', cURL error ' . $errno;
        foreach ($diagnostics as $name => $value) {
            $details .= ', ' . $name . '=' . $value;
        }
        return [$response, $status, $details, $diagnostics];
    }

    /**
     * Summary of render_question
     * @param object Moodle XML of question
     * @throws \stack_exception
     * @return string HTML render of question text
     */
    public static function render_question(object $question): string {
        global $CFG;
        try {
            StackSeedHelper::initialize_seed($question, null);
        } catch (\stack_exception $e) {
            // XML has no deployed seeds but we don't care in the library.
            $question->seed = 0;
        }

        // Handle Pluginfiles.
        $storeprefix = uniqid();
        StackPlotReplacer::persist_plugin_files($question, $storeprefix);
        switch ($question->questiontextformat) {
            case 'html':
                $format = FORMAT_HTML;
                break;
            case 'plain':
                $format = FORMAT_PLAIN;
                break;
            case 'markdown':
                $format = FORMAT_MARKDOWN;
                break;
            case 'moodle':
                $format = FORMAT_MOODLE;
                break;
            default:
                $format = $question->questiontextformat;
                break;
        }
        $question->questiontextformat = $format;
        $question->initialise_question_from_seed();

        $question->castextprocessor = new \castext2_qa_processor(new \stack_outofcontext_process());

        if (!empty($question->runtimeerrors)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            throw new \stack_exception(implode("\n", array_keys($question->runtimeerrors)));
        }

        $translate = new \stack_multilang();
        // This is a hack, that restores the filter regex to the exact one used in moodle.
        // The modifications done by the stack team prevent the filter funcitonality from working correctly.
        $translate->search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang")' .
                                '{2}\s*>.*?<\/span>)(\s*<span(\s+lang="[a-zA-Z0-9_-]+"' .
                                '|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';
        $language = current_language();

        $plots = [];
        $questiontext = $translate->filter(
            $question->questiontextinstantiated->apply_placeholder_holder(
                \stack_maths::process_display_castext(
                    $question->questiontextinstantiated->get_rendered(
                        $question->castextprocessor
                    )
                )
            ),
            $language
        );

        StackPlotReplacer::replace_plots($plots, $questiontext, 'render', $storeprefix);

        foreach ($question->inputs as $name => $input) {
            $tavalue = $question->get_ta_for_input($name);
            $fieldname = 'stack_temp_' . $name;
            $state = $question->get_input_state($name, []);
            // ISS1436 - As far as I can tell, only equiv input is using $tavalue
            // and that's expecting a string not an array.
            $render = $input->render($state, $fieldname, false, $tavalue);
            StackPlotReplacer::replace_plots($plots, $render, "answer-" . $name, $storeprefix);
            $questiontext = str_replace(
                "[[input:{$name}]]",
                $render,
                $questiontext
            );
            $questiontext = str_replace(
                "[[validation:{$name}]]",
                '',
                $questiontext
            );
        }

        foreach ($plots as $original => $new) {
            $questiontext = str_replace($original, stack_plot_url_base() . $new, $questiontext);
        }

        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $formatoptions->allowid = true;
        $formatoptions->filter = true;
        $questiontext = format_text($questiontext, FORMAT_HTML, $formatoptions);

        return '<div class="formulation">' . $questiontext . '</div>';
    }

    /**
     * Gets the structure of folders and files within a given directory on the server.
     * See questionfolder.mustache for output and usage.
     * @param string $dir sanitised full real path of library e.g. '/srv/stack/samplequestions/stacklibrary'
     * @return object StdClass Representation of the file system
     */
    public static function get_file_list(string $dir): object {
        global $CFG;
        $directoryiterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($directoryiterator, RecursiveIteratorIterator::SELF_FIRST);
        $result = [];
        foreach ($files as $item) {
            $pathfromsq = str_replace(dirname(__DIR__) . '/samplequestions/', '', $item->getPathname());
            $pathfromsq = str_replace("{$CFG->dataroot}/stack/", '', $pathfromsq);
            $result[] = (object)[
                'label' => $item->getFilename(),
                'relpath' => $pathfromsq,
                'isdirectory' => $item->isDir(),
                'url' => '',
            ];
        }

        return self::format_file_list($result);
    }

    /**
     * Gets the structure of folders and files within a given remote repo.
     * Two versions are returned. The first is a structured object for feeding into the mustache template
     * for displaying the folder structure. The second is a flat array keyed by file path for easy
     * retrieval of file info, paerticularly the file URL.
     * This is a wrapper function to make it easier to support different repo types.
     * See questionfolder.mustache for output and usage.
     * @param string|array $detail URL of the directory required or search term and apikey
     * @param string $repotype The type of repo being searched.
     * @return array [object StdClass structured representation of the file system, array flat array of file objects]
     */
    public static function get_file_list_from_repo($detail, $repotype) {
        switch ($repotype) {
            case self::GITHUB:
                return is_array($detail) ? self::list_github_repo($detail['url'], $detail['id']) : self::list_github_repo($detail);
            case self::NRWSEARCH:
                return self::list_nrw_search($detail);
            default:
                return [new StdClass(), []];
        }
    }

    /**
     * Gets a file from an external repo.
     * This is a wrapper function to make it easier to support different repo types.
     *
     * @param string $requestedfile URL
     * @param string $repotype
     * @return void
     */
    public static function get_external_file($requestedfile, $repotype, $apikey) {
        switch ($repotype) {
            case self::GITHUB:
                return self::get_external_github_file($requestedfile, is_array($apikey) ? $apikey : []);
            case self::NRWSEARCH:
                return self::get_external_nrw_file($requestedfile, $apikey);
            default:
                return null;
        }
    }

    /**
     * Retrieves a list of all the files in a GitHub repo via API
     * @param string $githuburl
     * @param string $libraryid Configured catalogue key, or empty to use only the default credential
     * @return array [object StdClass structured representation of the file system, array flat array of file objects]
     */
    public static function list_github_repo(string $githuburl, string $libraryid = '') {
        // Parse github URL like:
        // https://github.com/{owner}/{repo}/tree/{branch}/{path...}.
        $parts = parse_url($githuburl);
        if (empty($parts['host']) || strpos($parts['host'], 'github.com') === false) {
            return [];
        }
        $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
        $segments = explode('/', $path);
        if (count($segments) < 2) {
            return [];
        }
        $owner = $segments[0];
        $repo = $segments[1];

        // Default values.
        $branch = 'main';
        $subpath = '';

        // If URL uses the tree layout, extract branch and subpath.
        // Expected segments: owner, repo, tree, branch, ...subpath.
        if (isset($segments[2]) && $segments[2] === 'tree' && isset($segments[3])) {
            $branch = $segments[3];
            if (count($segments) > 4) {
                $subpath = implode('/', array_slice($segments, 4));
            } else {
                $subpath = '';
            }
        }

        $apibase = "https://api.github.com/repos/{$owner}/{$repo}";

        $files = [];
        $errorresponse = new StdClass();
        $errorresponse->error = stack_string('stack_library_connection_error');

        // Always use the git/trees API with recursive=1, then filter by subpath.
        $apiurl = "{$apibase}/git/trees/" . rawurlencode($branch) . "?recursive=1";
        $setting = 'githubtoken';
        if ($libraryid !== '') {
            $library = (json_decode(get_config('qtype_stack', 'libraries') ?: '{}', true) ?: [])[$libraryid] ?? null;
            if (!$library || $library['url'] !== $githuburl) {
                throw new \stack_exception('The GitHub library settings have changed or the library is unavailable. ' .
                    'Reload the library page and try again.');
            }
            $specific = self::github_token_setting($libraryid);
            if (get_config('qtype_stack', $specific)) {
                $setting = $specific;
            }
        }
        [$response, $httpcode, $details, $diagnostics] = static::request_github($apiurl, 10, $setting);
        // Retry access failures only. Rate limits must retain their original diagnostics.
        if ($setting !== 'githubtoken' && in_array($httpcode, [401, 403, 404], true)
                && $response !== false && !isset($diagnostics['retry-after'])
                && ($diagnostics['x-ratelimit-remaining'] ?? null) !== '0'
                && !str_contains(strtolower((string)$response), 'rate limit')
                && get_config('qtype_stack', 'githubtoken')
                && static::github_headers($setting) !== static::github_headers('githubtoken')) {
            $setting = 'githubtoken';
            [$response, $httpcode, $details] = static::request_github($apiurl, 10, $setting);
        }
        if ($response === false || $httpcode !== 200) {
            $errorresponse->error .= ': ' . $details;
            return [$errorresponse, []];
        }
        $data = json_decode($response, true);
        if (empty($data['tree']) || !is_array($data['tree'])) {
            return [$errorresponse, []];
        }
        $prefix = $subpath === '' ? '' : rtrim($subpath, '/') . '/';
        foreach ($data['tree'] as $item) {
            if ($prefix === '' || strpos($item['path'], $prefix) === 0) {
                $relpath = ltrim(substr($item['path'], strlen($prefix)), '/');
                $files[] = (object)[
                    'label' => basename($item['path']),
                    'relpath' => $relpath,
                    'isdirectory' => ($item['type'] === 'tree') ? 1 : 0,
                    'url' => ($item['type'] === 'tree') ? '' : $item['url'],
                    // Cache only the setting name, never the credential. Keep downloads on the listing's credential.
                    'githubcredential' => ['setting' => $setting, 'repository' => $apibase],
                ];
            }
        }

        $flatarray = array_column($files, null, 'relpath');

        return [self::format_file_list($files), $flatarray];
    }

    /**
     * Retrieves search results via API
     * @param array $details - search term and apikey
     * @return array [object StdClass structured representation of the file system, array flat array of file objects]
     */
    public static function list_nrw_search(array $details) {
        $apibase = self::NRWAPIBASE . "/questions/search?fields=id,data";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle-STACK'); // GitHub requires a user agent.
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/vnd.github.v3+json']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$details['apikey']}"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $files = new StdClass();
        $files->divid = 'stack-library-folder-1';
        $files->children = [];
        $files->isdirectory = 1;
        $files->label = '.';

        // Always use the git/trees API with recursive=1, then filter by subpath.
        $apiurl = "{$apibase}&q={$details['search']}";
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        $response = static::execute_curl_request($ch);
        $httpcode = static::get_curl_info($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $httpcode >= 400) {
            $files->error = stack_string('stack_library_connection_error');
            if ($response) {
                $files->error .= ': ' . $response;
            }

            return [$files, []];
        }
        $data = json_decode($response, true);
        if (isset($data['results'])) {
            foreach ($data['results'] as $item) {
                $files->children[] = (object)[
                    'label' => $item['question']['data']['title'],
                    // Once we have format data use format_text($text, FORMAT_MARKDOWN).
                    'description' => $item['question']['data']['description'][0][1] ?? null,
                    'license' => $item['question']['data']['license'],
                    'source' => $item['question']['data']['source'],
                    'subject' => (is_array($item['question']['data']['subject'])) ?
                        implode(', ', $item['question']['data']['subject']) : $item['question']['data']['subject'],
                    'path' => $item['question']['id'],
                    'isdirectory' => 0,
                    'url' => '',
                ];
            }
            if (!count($data['results'])) {
                $files->error = stack_string('stack_library_nothing');
            }
        } else {
            $files->error = stack_string('stack_library_connection_error');
        }

        return [$files, []];
    }

    /**
     * Take an array of file objects and format into an object with a directory-style structure.
     * The file objects should be in the form:
     *          {'label' => file/directory name,
     *          'relpath' => file path relative to top directory,
     *          'isdirectory' => boolean,
     *          'url' => url for obtaining the file if remote}
     * We sanitise the structure a bit to remove gitsync files and folders.
     * @param mixed $filelist
     * @return object stdClass
     */
    public static function format_file_list($filelist) {
        usort($filelist, function ($a, $b) {
            return strnatcmp($a->relpath, $b->relpath);
        });
        $results = new stdClass();
        $results->divid = 'stack-library-folder-' . self::$dircount;
        self::$dircount++;
        $results->children = [];
        $results->isdirectory = 1;
        // First file in sorted list will be in the current base directory. If we're on the first pass, we're in overall top
        // directory and dirname will return '.' which we convert to '' for our string compare.
        $firstfile = $filelist[array_key_first($filelist)]->relpath;
        $basedir = dirname($firstfile !== '.') ? dirname($firstfile) : '';

        $dirparts = explode('/', $basedir);
        $results->label = end($dirparts);
        foreach ($filelist as $file) {
            // We only want children of the current base directory. We ignore more distant descendants.
            if ($basedir === '') {
                if (str_contains($file->relpath, '/')) {
                    continue;
                }
            } else {
                if (str_contains(str_replace($basedir . '/', '', $file->relpath), '/')) {
                    continue;
                }
            }

            if (!$file->isdirectory) {
                if (
                    (pathinfo($file->relpath, PATHINFO_EXTENSION) === 'xml'
                    && strrpos($file->relpath, 'gitsync_category') === false)
                    || (pathinfo($file->relpath, PATHINFO_EXTENSION) === 'json' && strrpos($file->relpath, '_quiz.json') !== false)
                ) {
                    $childless = new StdClass();
                    $childless->path = $file->relpath;
                    $childless->url = $file->url;
                    $childless->label = $file->label;
                    $childless->isdirectory = 0;
                    $results->children[] = $childless;
                }
            } else {
                if (strrpos($file->relpath, 'manifest_backups') === false) {
                    $descendants = array_filter($filelist, fn($x) => str_starts_with($x->relpath, $file->relpath . '/'));
                    if ($descendants) {
                        $children = self::format_file_list($descendants);
                    } else {
                        continue;
                    }
                    if ($children->label === 'top') {
                        $childrenoftop = $children->children;
                        $quizzesintop = [];
                        $foldersintop = [];
                        foreach ($childrenoftop as $childoftop) {
                            if (
                                isset($childoftop->path) && pathinfo($childoftop->path, PATHINFO_EXTENSION) === 'json'
                                    && strrpos($childoftop->path, '_quiz.json') !== false
                            ) {
                                $quizzesintop[] = $childoftop;
                            } else if ($childoftop->isdirectory) {
                                $foldersintop[] = $childoftop;
                            }
                        }
                        if (count($foldersintop) === 1 && count($quizzesintop) === 0) {
                            // If we have a 'top' folder containing only a single folder (e.g. 'Default for...)
                            // strip out both from file display.
                            $results->children = array_merge($results->children, $foldersintop[0]->children);
                        } else if (count($foldersintop) === 1 && count($quizzesintop) > 0) {
                            // Quizzes and a single folder. Display quizzes and contents of folder.
                            $results->children = array_merge($results->children, $quizzesintop, $foldersintop[0]->children);
                        } else {
                            // Just strip out 'top'.
                            $results->children = array_merge($results->children, $childrenoftop);
                        }
                    } else {
                        $results->children[] = $children;
                    }
                }
            }
        }
        usort($results->children, function ($a, $b) {
            return strnatcmp($a->label, $b->label);
        });
        return $results;
    }

    /**
     * Fetch a file from GitHub using the api blob URL.
     *
     * @param string $requestedfile API URL
     * @param array $credential Server-cached repository and setting selected during listing
     * @return string XML file contents
     */
    public static function get_external_github_file($requestedfile, array $credential = []) {
        if ($credential && !str_starts_with($requestedfile, $credential['repository'] . '/git/blobs/')) {
            throw new \stack_exception('The requested file is outside this GitHub library. ' .
                'Reload the library page and select the file again.');
        }
        [$res, $httpcode, $details] = static::request_github($requestedfile, 0, $credential['setting'] ?? 'githubtoken');
        if ($res === false || $httpcode !== 200) {
            throw new \stack_exception('File unavailable (' . $details . ').');
        }

        $json = json_decode($res, true);
        if (!is_array($json) || empty($json['content']) || empty($json['encoding'])) {
            throw new \stack_exception('Invalid JSON.');
        }

        if ($json['encoding'] !== 'base64') {
            throw new \stack_exception('Wrongly encoded.');
        }

        $filecontents = base64_decode($json['content'], true);
        if ($filecontents === false) {
            throw new \stack_exception('Could not decode.');
        }

        return $filecontents;
    }

    /**
     * Fetch a file from GitHub using the api blob URL.
     *
     * @param string $requestedfile API URL
     * @return string XML file contents
     */
    public static function get_external_nrw_file($requestedid, $apikey) {
        $apibase = self::NRWAPIBASE . "/questions/get/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle-STACK'); // GitHub requires a user agent.
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$apikey}"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $apiurl = "{$apibase}{$requestedid}?fields=export_xml";
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        $response = static::execute_curl_request($ch);
        $httpcode = static::get_curl_info($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $httpcode >= 400) {
            throw new \stack_exception('File unavailable.');
        }

        $json = json_decode($response, true);
        if (!is_array($json) || !isset($json['export_xml']) || !is_string($json['export_xml'])) {
            throw new \stack_exception('Invalid JSON.');
        }

        return $json['export_xml'];
    }

    /**
     * Upload a Moodle question object to the ATLAS API.
     *
     * @param object $questiondata Question data from question_bank::load_question_data().
     * @param string $apikey ATLAS API key.
     * @return array Upload result for template output.
     */
    public static function upload_nrw_question(object $questiondata, string $apikey): array {
        global $CFG, $COURSE;
        require_once($CFG->libdir . '/questionlib.php');
        require_once($CFG->dirroot . '/question/format/xml/format.php');

        if (empty($apikey)) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadapikeymissing'),
            ];
        }

        // Preflight the same capability exportprocess(true) will enforce, so we can
        // fail cleanly instead of falling into a zero-question export edge case.
        if (!question_has_capability_on($questiondata, 'view')) {
            return [
                'iserror' => true,
                'message' => get_string('nopermissions', 'error', get_string('stack:exporttoexternallibraries', 'qtype_stack')),
            ];
        }

        $qformat = new \qformat_xml();
        $qformat->setQuestions([$questiondata]);
        $thiscontext = context::instance_by_id($questiondata->contextid);
        $contexts = new question_edit_contexts($thiscontext);
        // Checks user has export permission for the supplied context.
        $qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
        $qformat->setCattofile(false);
        $qformat->setContexttofile(false);
        $qformat->setCourse($COURSE);

        if (!$qformat->exportpreprocess()) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadxmlerror'),
            ];
        }
        $xmlstring = $qformat->exportprocess(true);
        if (!$xmlstring) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadxmlerror'),
            ];
        }

        // POST /uploads is the only write route open to an LMS key (role `lms`).
        // atlas-api imports the file in the background and answers with a claim
        // link: whoever opens it takes over the upload in the ATLAS hub
        // (metadata, duplicates, predecessor) and publishes it from there.
        $apiurl = self::NRWAPIBASE . '/uploads';
        $postfields = [
            'file' => new \CURLStringFile($xmlstring, 'question.xml', 'application/xml'),
        ];
        if (!empty($questiondata->name)) {
            $postfields['collection_title'] = \core_text::substr($questiondata->name, 0, 255);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle-STACK');
        curl_setopt($ch, CURLOPT_POST, true);
        // An array makes curl send multipart/form-data.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apikey,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = static::execute_curl_request($ch);
        $httpcode = static::get_curl_info($ch, CURLINFO_HTTP_CODE);
        if ($response === false) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadfailed') . ': ' . curl_error($ch),
            ];
        }

        $json = json_decode($response, true);
        if (!is_array($json)) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadfailed') . ' HTTP ' . $httpcode . ': ' . $response,
            ];
        }

        if ($httpcode === 202) {
            if (empty($json['claim_url'])) {
                return [
                    'iserror' => true,
                    'message' => stack_string('atlasuploadunexpected') . ' HTTP ' . $httpcode . ': ' . $response,
                ];
            }
            $expires = !empty($json['claim_expires_at']) ? strtotime($json['claim_expires_at']) : false;
            return [
                'issuccess' => true,
                'message' => stack_string('atlasuploadqueued'),
                'claimurl' => $json['claim_url'],
                'claimexpires' => $expires ? userdate($expires) : '',
            ];
        }

        if ($httpcode === 400) {
            return [
                'iserror' => true,
                'message' => stack_string('atlasuploadrejected') . ' ' . static::nrw_error_detail($json),
            ];
        }

        return [
            'iserror' => true,
            'message' => stack_string('atlasuploadfailed') . ' HTTP ' . $httpcode . ': ' . $response,
        ];
    }

    /**
     * The human-readable part of an atlas-api error body.
     *
     * `detail` is either a string or an object with `message` (and `error`).
     *
     * @param array $json Decoded error body.
     * @return string
     */
    protected static function nrw_error_detail(array $json): string {
        $detail = $json['detail'] ?? '';
        if (is_array($detail)) {
            $detail = $detail['message'] ?? $detail['error'] ?? json_encode($detail);
        }
        return (string) $detail;
    }
}
