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
     * Gets the structure of folders and files within a given directory
     * See questionfolder.mustache for output and usage.
     * We sanitise the structure a bit to remove gitsync files and folders.
     * @param string sanitised search string e.g. '/srv/stack/samplequestions/stacklibrary/*'
     * with the full real path of the folder and search criteria.
     * @return object StdClass Representation of the file system
     */
    public static function get_file_list(string $dir): object {
        global $CFG;
        $files = glob($dir);
        $results = new stdClass();
        $labels = explode('/', $dir);
        $results->label = $labels[count($labels) - 2];
        $results->divid = 'stack-library-folder-' . self::$dircount;
        self::$dircount++;
        $results->children = [];
        $results->isdirectory = 1;
        foreach ($files as $path) {
            if (!is_dir($path)) {
                if (
                    (pathinfo($path, PATHINFO_EXTENSION) === 'xml' && strrpos($path, 'gitsync_category') === false)
                    || (pathinfo($path, PATHINFO_EXTENSION) === 'json' && strrpos($path, '_quiz.json') !== false)
                ) {
                    $childless = new StdClass();
                    // Get the path relative to the samplequestions or stack dataroot folder.
                    $pathfromsq = str_replace(dirname(__DIR__) . '/samplequestions/', '', $path);
                    $pathfromsq = str_replace("{$CFG->dataroot}/stack/", '', $pathfromsq);
                    $childless->path = $pathfromsq;
                    $labels = explode('/', $path);
                    $childless->label = end($labels);
                    $childless->isdirectory = 0;
                    $results->children[] = $childless;
                }
            } else {
                if (strrpos($path, 'manifest_backups') === false) {
                    $children = self::get_file_list($path . '/*');
                    if ($children->label === 'top') {
                        $topchildren = $children->children;
                        $topquizzes = [];
                        $topfolders = [];
                        foreach ($topchildren as $topchild) {
                            if (
                                isset($topchild->path) && pathinfo($topchild->path, PATHINFO_EXTENSION) === 'json'
                                    && strrpos($topchild->path, '_quiz.json') !== false
                            ) {
                                $topquizzes[] = $topchild;
                            } else if ($topchild->isdirectory) {
                                $topfolders[] = $topchild;
                            }
                        }
                        if (count($topfolders) === 1 && count($topquizzes) === 0) {
                            // If we have a 'top' folder containing only a single folder (e.g. 'Default for...)
                            // strip out both from file display.
                            $results->children = array_merge($results->children, $topchildren[0]->children);
                        } else if (count($topfolders) === 1 && count($topquizzes) > 0) {
                            // Quizzes and a single folder. Display quizzes and contents of folder.
                            $results->children = array_merge($topquizzes, $topfolders[0]->children);
                        } else {
                            // Just strip out 'top'.
                            $results->children = array_merge($results->children, $topchildren);
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

    public static function stack_list_github_repo(string $githuburl) {
        // Parse github URL like:
        // https://github.com/{owner}/{repo}/tree/{branch}/{path...}
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
        $branch = 'master';
        $subpath = '';

        // If URL uses the tree layout, extract branch and subpath.
        // Expected segments: owner, repo, tree, branch, ...subpath
        if (isset($segments[2]) && $segments[2] === 'tree' && isset($segments[3])) {
            $branch = $segments[3];
            if (count($segments) > 4) {
                $subpath = implode('/', array_slice($segments, 4));
            } else {
                $subpath = '';
            }
        }

        $apiBase = "https://api.github.com/repos/{$owner}/{$repo}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle-STACK'); // GitHub requires a user agent.
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/vnd.github.v3+json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $files = [];

        // Always use the git/trees API with recursive=1, then filter by subpath.
        $apiurl = "{$apiBase}/git/trees/" . rawurlencode($branch) . "?recursive=1";
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

        usort($files, function ($a, $b) {
            return strnatcmp($a->relpath, $b->relpath);
        });

        return self::format_file_list($files);

    }

    public static function format_file_list($filelist) {
        $results = new stdClass();
        $results->divid = 'stack-library-folder-' . self::$dircount;
        self::$dircount++;
        $results->children = [];
        $results->isdirectory = 1;
        $results->label = dirname($filelist[array_key_first($filelist)]->relpath) !== '.' ? dirname($filelist[array_key_first($filelist)]->relpath) : '';
        foreach ($filelist as $file) {
            if ($results->label === '') {
                if (str_contains($file->relpath, '/')) {
                    continue;
                }
            } else {
                if (str_contains(ltrim($file->relpath, $results->label . '/'), '/')) {
                    continue;
                }
            }

            if (!$file->isdirectory) {
                if (
                    (pathinfo($file->relpath, PATHINFO_EXTENSION) === 'xml' && strrpos($file->relpath, 'gitsync_category') === false)
                    || (pathinfo($file->relpath, PATHINFO_EXTENSION) === 'json' && strrpos($file->relpath, '_quiz.json') !== false)
                ) {
                    $childless = new StdClass();
                    $childless->path = $file->url;
                    $childless->label = $file->label;
                    $childless->isdirectory = 0;
                    $results->children[] = $childless;
                }
            } else {
                if (strrpos($file->relpath, 'manifest_backups') === false) {
                    $children = array_filter($filelist, fn($x) => str_starts_with($x->relpath, $file->relpath . '/'));
                    $children = self::format_file_list($children);
                    if ($children->label === 'top') {
                        $topchildren = $children->children;
                        $topquizzes = [];
                        $topfolders = [];
                        foreach ($topchildren as $topchild) {
                            if (
                                isset($topchild->relpath) && pathinfo($topchild->relpath, PATHINFO_EXTENSION) === 'json'
                                    && strrpos($topchild->path, '_quiz.json') !== false
                            ) {
                                $topquizzes[] = $topchild;
                            } else if ($topchild->isdirectory) {
                                $topfolders[] = $topchild;
                            }
                        }
                        if (count($topfolders) === 1 && count($topquizzes) === 0) {
                            // If we have a 'top' folder containing only a single folder (e.g. 'Default for...)
                            // strip out both from file display.
                            $results->children = array_merge($results->children, $topchildren[0]->children);
                        } else if (count($topfolders) === 1 && count($topquizzes) > 0) {
                            // Quizzes and a single folder. Display quizzes and contents of folder.
                            $results->children = array_merge($topquizzes, $topfolders[0]->children);
                        } else {
                            // Just strip out 'top'.
                            $results->children = array_merge($results->children, $topchildren);
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

    public static function get_external_file($requestedfile) {
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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res === false || $httpCode !== 200) {
            throw new stack_exception('');
        }

        $json = json_decode($res, true);
        if (!is_array($json) || empty($json['content']) || empty($json['encoding'])) {
            throw new stack_exception('');
        }

        if ($json['encoding'] !== 'base64') {
            throw new stack_exception('');
        }

        $filecontents = base64_decode($json['content'], true);
        if ($filecontents === false) {
            throw new stack_exception('');
        }

        return $filecontents;
    }
}
