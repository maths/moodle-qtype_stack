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
     * Summary of render_question
     * @param object Moodle XML of question
     * @throws \stack_exception
     * @return string HTML render of question text
     */
    public static function render_question(object $question): string {
        global $CFG;
        StackSeedHelper::initialize_seed($question, null);

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
            $questiontext = str_replace($original, $CFG->wwwroot . '/question/type/stack/plot.php/' . $new, $questiontext);
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
     * @param string $url URL of the directory required
     * @param string $repotype The type of repo being searched. (Currently only GitHub is supported)
     * @return array [object StdClass structured representation of the file system, array flat array of file objects]
     */
    public static function get_file_list_from_repo($url, $repotype) {
        switch ($repotype) {
            case self::GITHUB:
                return self::list_github_repo($url);
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
    public static function get_external_file($requestedfile, $repotype) {
        switch ($repotype) {
            case self::GITHUB:
                return self::get_external_github_file($requestedfile);
            default:
                return null;
        }
    }

    /**
     * Retrieves a list of all the files in a GitHub repo via API
     * @param string $githuburl
     * @return array [object StdClass structured representation of the file system, array flat array of file objects]
     */
    public static function list_github_repo(string $githuburl) {
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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle-STACK'); // GitHub requires a user agent.
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/vnd.github.v3+json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $files = [];

        // Always use the git/trees API with recursive=1, then filter by subpath.
        $apiurl = "{$apibase}/git/trees/" . rawurlencode($branch) . "?recursive=1";
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $httpcode >= 400) {
            return [];
        }
        $data = json_decode($response, true);
        if (empty($data['tree']) || !is_array($data['tree'])) {
            return [];
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
                ];
            }
        }

        $flatarray = array_column($files, null, 'relpath');

        return [self::format_file_list($files), $flatarray];
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

        $results->label = end(explode('/', $basedir));
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
     * @return string XML file contents
     */
    public static function get_external_github_file($requestedfile) {
        $headers = [
            'User-Agent: PHP',
            'Accept: application/vnd.github.v3+json',
        ];

        $ch = curl_init($requestedfile);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res === false || $httpcode !== 200) {
            throw new \stack_exception('File unavailable.');
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
}
