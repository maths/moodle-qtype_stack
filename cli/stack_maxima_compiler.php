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

define('CLI_SCRIPT', true);

// This script collects and compiles the sorces of STACK-Maxima functions
// stored under `stack/maximasrc/` and produces `stack/maxima/compiled.mac`.
// Also produces `stack/maxima/compiled_tests.mac` a script that can be
// executed inside a STACK-Maxima to see if those functions pass their own 
// tests.
//
// Remeber to run this when working under `stack/maximasrc/`.
//
// @copyright  2023 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');

// Collect script names. From a hard-coded depth.
$scripts = [];
foreach (glob("../stack/maximasrc/*.mac") as $filename) {
    if ($filename !== false) {
        $scripts[] = $filename;
    }
}
foreach (glob("../stack/maximasrc/*/*.mac") as $filename) {
    if ($filename !== false) {
        $scripts[] = $filename;
    }
}
foreach (glob("../stack/maximasrc/*/*/*.mac") as $filename) {
    if ($filename !== false) {
        $scripts[] = $filename;
    }
}
foreach (glob("../stack/maximasrc/*/*/*/*.mac") as $filename) {
    if ($filename !== false) {
        $scripts[] = $filename;
    }
}

// Return them into some sort of order, not the depth last order.
sort($scripts);
$n = count($scripts);

// Set the parser options, include LISP-support and stop all inserts.
$po = new stack_parser_options();
$po->tryinsert = false;
$po->lispids = true;
$po->dropcomments = false;
$po->statementendtokens = [';', '$'];

// To String settings.
$ts = ['dealias' => false];

// Some irrelevant objects.
$err = [];
$sec = new stack_cas_security();

echo("Processing $n source files.\n");

$code = '/* Result of compilation do not edit. */' . "\n";
$test = <<<ESCAPE
/* Result of compilation do not edit. */
s_test_fails: 0$ 
s_test_success: 0$
s_exec_test(src_name, src_pos) := block([simp],
    simp:true,
    if not s_test_case(true) then (
        print(sconcat("Test[simp=true ] failure '",src_name,"' test case ", src_pos)),
        s_test_fails: s_test_fails + 1
    ) else 
        s_test_success: s_test_success + 1,
    simp:false,
    if not s_test_case(false) then (
        print(sconcat("Test[simp=false] failure '",src_name,"' test case ", src_pos)),
        s_test_fails: s_test_fails + 1
    ) else 
        s_test_success: s_test_success + 1
)$

ESCAPE;

$pipeline = stack_parsing_rule_factory::get_filter_pipeline([
    '601_castext',
    '602_castext_simplifier', 
    '680_gcl_sconcat'], [], false);


// Collect docs.
class doc_tree_node {
    public $parent = null;
    public $children = null;
    public $name = null;
    public $content = null;
    public $description = null;

    public function __construct($parent) {
        if ($parent !== null) {
            $this->parent = $parent;
            $parent->children[] = $this;
        }
        $this->content = [];
        $this->children = [];
        $this->description = '';
    }
}


$docs = []; // Name -> text-blob.
$categories = []; // Directoryname -> object.
$categories[''] = new doc_tree_node(null);
$categories['']->name = 'ROOT';
$nametopath = []; // Name -> Directoryname.

foreach ($scripts as $filename) {
    if (mb_strrpos($filename, '/description.md') !== false) {
        // Some files are not script files.
        continue;
    }
    $content = trim(file_get_contents($filename));
    $sname = substr($filename, 19);
    if ($content === '') {
        echo ("'$sname' seems to be empty.");
    } else {
        $ast = maxima_parser_utils::parse_po($content, $po);
        $payload = [];
        $tests = [];
        $comments = []; // Endpos -> value        

        // Note that all the content is at the top level.
        foreach ($ast->items as $item) {
            if ($item instanceof MP_Statement) {
                if ($item->statement instanceof MP_Operation
                    && $item->statement->op === ':='
                    && $item->statement->lhs instanceof MP_FunctionCall
                    && $item->statement->lhs->name->toString() === 's_test_case'
                ) {
                    $tests[] = $item;
                } else {
                    $payload[] = $item;
                }
            } else {
                // Only picking comments that follow the format where the comment starts
                // with '*' and lines follow that.
                // /**
                //  * Fooo
                //  * bar
                //  */
                //
                if (mb_strpos($item->value, '*') === 0) {
                    $lines = array_map('trim', explode("\n", $item->value));
                    $comment = '';
                    foreach ($lines as $line) {
                        if (mb_strpos($line, '* ') === 0) {
                            $line = mb_substr($line, 2);
                        } else if (mb_strpos($line, '*') === 0) {
                            $line = mb_substr($line, 1);
                        }
                        $comment .= "$line\n";
                    }
                    $comment = trim($comment);
                    $comments[$item->position['end']] = $comment;
                }
            }
        }
        echo ("'$sname' " . count($payload) 
            . ' items of content and ' . count($tests) . " tests.\n");

        foreach ($payload as $c) {
            $c = $pipeline->filter($c, $err, $err, $sec);
            $code .= $c->toString($ts) . "$\n";

            // Seek for matching comment.
            $i = $c->position['start'];
            $matching = null;
            for ($j = $i+1 ; $j > $i - 40; $j--) {
                if (isset($comments[$j])) {
                    $matching = $comments[$j];
                    break;
                }
            }
            if ($matching !== null) {
                // Is it a variable or a function def?
                $name = $c->statement->lhs;
                if ($name instanceof MP_FunctionCall) {
                    $name = $name->name->toString();
                } else {
                    $name = $name->value;
                }
                $docs[$name] = $matching;

                // Cats.
                $cats = array_splice(explode('/', $sname), 0, -1);
                $ccat = '/' . implode('/', $cats);
                $lastcat = $categories[''];
                $path = '';
                foreach ($cats as $cat) {
                    $path .= '/' . $cat;
                    if (!isset($categories[$path])) {
                        // Maybe collect more meta here. Maybe the directory contains 
                        // some metafile giving better names titles descriptions etc.
                        $categories[$path] = new doc_tree_node($lastcat);
                        $categories[$path]->name = $cat;
                        if (file_exists(__DIR__ . '/../stack/maximasrc' . $path . '/description.md')) {
                            $categories[$path]->description = file_get_contents(__DIR__ . '/../stack/maximasrc' . $ccat . '/description.md');
                        }
                    }
                    $lastcat = $categories[$path];
                }
                // Just plug the item to the content list of that directory.
                $categories[$ccat]->content[] = $name;

                if (isset($nametopath[$name])) {
                    echo "\n\nWARNING! Duplicate name '$name'.\n\n";
                }
                $nametopath[$name] = $ccat;
            }

            // TODO check payload:
            //  if function definition does it reference external variables?
            //  if variable assignment is that a variable referenced by this function?
            //  Are arguments and temp variables "unique" so that they do not mask input?
        }
        $i = 1;
        foreach ($tests as $c) {
            $c = $pipeline->filter($c, $err, $err, $sec);
            $test .= $c->toString($ts) . "$\n";
            $name = (new MP_String($sname . ':' . $c->position['start-row']))->toString();
            $test .= "s_exec_test($name,$i)$\n";
            $i++;
        }
    }
}

$test .= "simp:true$\nif s_test_fails = 0 then print(\"All tests successfully executed.\")$";

// Output primary content.
file_put_contents('../stack/maxima/compiled.mac', $code);
file_put_contents('../stack/maxima/compiled_tests.mac', $test);

// Build docs.
// The structure now is to place all items of a content category into a single
// file. Then map to those from index files.
// There are many index files...


// Generate the category doc files.
foreach ($categories as $path => $node) {
    if ($path === '') {
        continue; // Special root category.
    }
    $doc = '<!-- NOTE! This file is autogenerated from files under stack/maximasrc do not edit here. -->';
    $doc .= "\n# Section documentation for $path\n\n";

    
    if ($node->description !== '') {
        $doc .= trim($node->description) . "\n\n";
    }

    sort($node->content);
    foreach ($node->content as $name) {
        $doc .= "\n## $name<a id='$name'></a>\n\n";
        $doc .= $docs[$name] . "\n\n";
    }
 
    // Crosslink all `name` style references.
    foreach ($nametopath as $name => $npath) {
        if (mb_strpos($doc, "`$name`") !== false) {
            if ($npath === $path) {
                $doc = str_replace("`$name`", "[`$name`](#$name)", $doc);
            } else {
                $to = explode('/', $npath);
                $from = explode('/', $path);
                
                while ($to[0] === $from[0] && count($to) > 0 && count($from) > 0) {
                    array_shift($to);
                    array_shift($from);
                }
                $relpath = '';
                foreach ($from as $duh) {
                    $relpath .= '../';
                }
                foreach ($to as $part) {
                    $relpath .= $part . '/';   
                }
                $relpath .= 'index.md#' . $name;
                $doc = str_replace("`$name`", "[`$name`]($relpath)", $doc);
            }
        }
    }

    // Create directory.
    mkdir(__DIR__ . '/../doc/en/CAS/Library' . $path, 0777, true);
    file_put_contents(__DIR__ . '/../doc/en/CAS/Library' . $path . '/index.md', $doc);
}


// Generate general indexfiles.
// Alphabetical full index.
$names = array_keys($nametopath);
sort($names);

$doc = '<!-- NOTE! This file is autogenerated from files under stack/maximasrc do not edit here. -->';
$doc .= "\n# Alphabetical index of STACK specific Maxima functions\n\n";
$doc .= "[Tree index](index.md)\n\n";
foreach ($names as $name) {
    $doc .= "\n- [$name](" . substr($nametopath[$name], 1) . "/index.md#$name)";
}
$doc .= "\n";
file_put_contents(__DIR__ . '/../doc/en/CAS/Library/alphabetical.md', $doc);

// A tree version of the index TODO: naming of sections needs to 
// get something more descriptive, through some metadata etc...
$doc = '<!-- NOTE! This file is autogenerated from files under stack/maximasrc do not edit here. -->';
$doc .= "\n# Tree index of STACK specific Maxima functions\n\n";
$doc .= "[Alphabetical index](alphabetical.md)\n\n";

function node_handler(doc_tree_node $node, $indent = 0, $path = ''): string {
    global $nametopath;
    sort($node->content);
    $r = '';
    foreach ($node->content as $name) {
        $r .= str_repeat(' ', $indent) . "- [$name](" . substr($nametopath[$name], 1) . "/index.md#$name)\n"; 
    }
    foreach ($node->children as $n) {
        $r .= str_repeat(' ', $indent) . '- [' . $n->name . "]($path" . $n->name  . "/index.md) \n";
        $r .= node_handler($n, $indent + 2, $path . $n->name . '/');
    }
    return $r;
}
$doc .= node_handler($categories[''], 0);
$doc .= "\n";
file_put_contents(__DIR__ . '/../doc/en/CAS/Library/index.md', $doc);