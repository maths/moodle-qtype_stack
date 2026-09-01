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
 * Build the private demo question manifest.
 *
 * The browser receives opaque question ids from this manifest. Only the
 * frontend container maps those ids back to question-library XML files.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/lib.php');

$root = STACK_PRIVATE_DEMO_LIBRARY_ROOT;
$output = __DIR__ . '/assets/question-manifest.json';
$libraryroot = 'samplequestions/' . getenv('STACK_PRIVATE_DEMO_LIBRARY');

if ($root === false || !is_dir($root)) {
    fwrite(STDERR, "Could not find $libraryroot.\n");
    exit(1);
}

$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'xml') {
        $files[] = $file->getPathname();
    }
}
sort($files, SORT_STRING);

$questions = [];
$errors = [];
$skipped = [
    'nostack' => 0,
    'multiple' => 0,
];

foreach ($files as $file) {
    $relativepath = ltrim(str_replace('\\', '/', substr($file, strlen($root))), '/');
    $xml = file_get_contents($file);

    libxml_use_internal_errors(true);
    $quiz = simplexml_load_string($xml);
    $xmlerrors = libxml_get_errors();
    libxml_clear_errors();

    if ($quiz === false) {
        $errors[] = $relativepath . ': invalid XML';
        continue;
    }

    $stackquestions = [];
    foreach ($quiz->question as $question) {
        $type = (string) $question['type'];
        if ($type === 'category') {
            continue;
        }
        if ($type === 'stack') {
            $stackquestions[] = $question;
        }
    }

    if (count($stackquestions) === 0) {
        // The library contains category metadata and Moodle question types that
        // are not STACK API questions. They are not part of this demo catalogue.
        $skipped['nostack']++;
        continue;
    }

    if (count($stackquestions) !== 1) {
        // This demo maps one public id to one library file. Multi-question
        // bundles can stay in the library, but are skipped here.
        $skipped['multiple']++;
        continue;
    }

    $name = trim((string) $stackquestions[0]->name->text);
    if ($name === '') {
        $name = basename($relativepath, '.xml');
    }

    $id = 'q_' . substr(hash('sha256', $relativepath), 0, 16);
    if (array_key_exists($id, $questions)) {
        $errors[] = $relativepath . ': duplicate generated question id ' . $id;
        continue;
    }

    $category = dirname($relativepath);
    $questions[$id] = [
        'id' => $id,
        'name' => $name,
        'filename' => basename($relativepath),
        'path' => $relativepath,
        'category' => $category === '.' ? '' : $category,
    ];
}

if ($errors) {
    foreach ($errors as $error) {
        fwrite(STDERR, $error . "\n");
    }
}

$manifest = [
    'root' => $libraryroot,
    'questions' => $questions,
];

$json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "Could not encode manifest JSON.\n");
    exit(1);
}

file_put_contents($output, $json . "\n");
fwrite(STDOUT, 'Wrote ' . count($questions) . " questions to $output\n");
fwrite(
    STDOUT,
    'Skipped ' . $skipped['nostack'] . ' XML files without STACK questions and ' .
        $skipped['multiple'] . " multi-question XML files.\n"
);
