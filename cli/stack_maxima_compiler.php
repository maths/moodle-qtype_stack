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
// Also generates some documentation.
//
// Remember to run this when working under `stack/maximasrc/`.
//
// @copyright  2023 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/error.interpreter.class.php');


// Collect script names. From a hard-coded depth.
$scripts = [];
$depth = 5;
for ($i = 0; $i < $depth; $i++) {
    $d = str_repeat('*/', $i);
    foreach (glob("../stack/maximasrc/$d*.mac") as $filename) {
        if ($filename !== false) {
            $scripts[] = $filename;
        }
    }
}

// Return them into some sort of order, not the depth last order.
sort($scripts);
$n = count($scripts);

// Set the parser options, include LISP-support and stop all inserts.
$po = stack_parser_options::get_cas_config();
$po->lispids = true;
$po->dropcomments = false;
$parser = $po->get_parser();

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
$title = []; // Name -> string

// An AST filter of sorts. Modifies function/lambda definitions so that
// all arguments and block local variables. Does not track the `local()`
// variables. Only the `block([vars],...)`
function var_rename(MP_Statement $ast): MP_Statement {
    $r = $ast;
    $ast->callbackRecurse(null); // Fill the parents.
    if ($r->statement instanceof MP_Operation
        && $r->statement->op === ':='
        && $r->statement->lhs instanceof MP_FunctionCall) {
        // We are actually working on something.
        $annotations = $r->position['annotations'];
        // Add certain names saved by default from rename.
        $flags = stack_cas_security::get_all_with_feature('evflag');
        foreach ($flags as $id) {
            $annotations['ignore']['rename'][$id] = $id;
        }
        // Fetch all identifiers and classify them.
        $ids = [];
        $idcrawl = function ($node) use (&$ids, $ast, $flags) {
            if ($node instanceof MP_Identifier) {
                if (!isset($ids[$node->value])) {
                    $ids[$node->value] = [
                        'arg' => [],
                        'lambda-arg' => [],
                        'block-local' => [],
                        'call' => [],
                        'var' => [],
                        'tick' => []
                    ];
                }
                if ($node->parentnode instanceof MP_FunctionCall
                    && $node->parentnode->is_definition()
                    && $node->parentnode->parentnode->parentnode === $ast
                    && array_search($node, $node->parentnode->arguments) !== false) {
                    // f(X...):=...
                    // but only this local one no nested defs.
                    $ids[$node->value]['arg'][] = $node;
                } else if ($node->parentnode instanceof MP_List
                    && $node->parentnode->parentnode instanceof MP_FunctionCall
                    && $node->parentnode->parentnode->name->toString() === 'lambda'
                    && array_search($node->parentnode, $node->parentnode->parentnode->arguments) === 0) {
                    // ...lambda([X...],...)...
                    $ids[$node->value]['lambda-arg'][] = $node;
                } else if ($node->parentnode instanceof MP_List
                    && $node->parentnode->parentnode instanceof MP_FunctionCall
                    && $node->parentnode->parentnode->name->toString() === 'block'
                    && array_search($node->parentnode, $node->parentnode->parentnode->arguments) === 0) {
                    // block([X...],...)
                    $ids[$node->value]['block-local'][] = $node;
                } else if ($node->parentnode instanceof MP_Operation
                    && $node->parentnode->lhs === $node
                    && $node->parentnode->op === ':'
                    && $node->parentnode->parentnode instanceof MP_List
                    && $node->parentnode->parentnode->parentnode instanceof MP_FunctionCall
                    && $node->parentnode->parentnode->parentnode->name->toString() === 'block'
                    && array_search($node->parentnode->parentnode, $node->parentnode->parentnode->parentnode->arguments) === 0) {
                    // block([X:...,...],...)
                    $ids[$node->value]['block-local'][] = $node;
                } else if ($node->parentnode instanceof MP_PrefixOp && $node->parentnode->op === "'") {
                    $ids[$node->value]['tick'][] = $node;
                } else if ($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name === $node) {
                    $ids[$node->value]['call'][] = $node;
                } else if (isset($flags[$node->value]) && $node->parentnode instanceof MP_FunctionCall && $node->parentnode->name->toString() === 'ev' && array_search($node, $node->parentnode->arguments) > 0) {
                    // Specially ignore use of flags in ev(...,flag).
                } else if (isset($flags[$node->value]) && $node->parentnode instanceof MP_Operation && $node->parentnode->lhs === $node && ($node->parentnode->op === ':' || $node->parentnode->op === '=') && $node->parentnode->parentnode instanceof MP_FunctionCall && $node->parentnode->parentnode->name->toString() === 'ev' && array_search($node->parentnode, $node->parentnode->parentnode->arguments) > 0) {
                    // Specially ignore use of flags in ev(...,flag:...).
                } else {
                    $ids[$node->value]['var'][] = $node;
                }
            }
            return true;
        };
        $r->callbackRecurse($idcrawl);

        // Generate some variables to use. And skip those already there.
        // Someone might have done manual pre-emptive work with these.
        $internal_vars = [];
        // Generate an excessive number, just in case there exists a lot of lambdas.
        for ($i = 1; $i < 10*count($ids); $i++) {
            $iv = "%_si$i";
            if (!isset($ids[$iv])) {
                $internal_vars[] = $iv;
                // And we don't touch that at all.
                unset($ids[$iv]);
            }
        }
        
        // Start with arguments of lambdas, moving up the logic tree.
        foreach ($ids as $id => $details) {
            if (isset($annotations['ignore']['rename'][$id])) {
                continue;
            }
            foreach (array_reverse($details['lambda-arg']) as $node) {
                // Handle in inverse order just in case of nesting.
                // All the nodes inside the lambda.
                $neighborhood = $node->parentnode->parentnode->asAList();
                // Pick a name.
                $newname = array_shift($internal_vars);
                // Now assuming that there are no ticks or calls...
                $remainder = [];
                foreach ($details['var'] as $v) {
                    if (array_search($v, $neighborhood) !== false) {
                        $v->value = $newname;
                    } else {
                        $remainder[] = $v;
                    }
                }
                $ids[$id]['var'] = $remainder;
                $details['var'] = $ids[$id]['var'];
                $node->value = $newname;
            }
        }
        // Then function arguments.
        foreach ($ids as $id => $details) {
            if (isset($annotations['ignore']['rename'][$id]) || count($details['arg']) === 0 ) {
                continue;
            }
            // Skip if someone does interesting things with ticks.
            if (count($details['tick']) > 0) {
                echo(" Rename issue related to '$id in " . $ast->statement->lhs->name->toString() . "\n");
                echo("  To disable this WARNING place /* @ignore[rename=$id] */ inside that function definition.\n");
                continue;
            }
            // Skip if someone does interesting things with calls.
            if (count($details['call']) > 0) {
                echo(" Rename issue related to $id() in " . $ast->statement->lhs->name->toString() . "\n");
                echo("  To disable this WARNING place /* @ignore[rename=$id] */ inside that function definition.\n");
                continue;
            }
            foreach ($details['arg'] as $node) {
                // Realistically, only one can exist.
                $newname = array_shift($internal_vars);
                foreach ($details['var'] as $v) {
                    $v->value = $newname;
                }
                $ids[$id]['var'] = [];
                $details['var'] = $ids[$id]['var'];
                $node->value = $newname;
            }
        }
        // Then the block locals.
        foreach ($ids as $id => $details) {
            if (isset($annotations['ignore']['rename'][$id]) || count($details['block-local']) === 0 ) {
                continue;
            }
            // Skip if someone does interesting things with ticks.
            if (count($details['tick']) > 0) {
                echo(" Rename issue related to '$id in " . $ast->statement->lhs->name->toString() . "\n");
                echo("  To disable this WARNING place /* @ignore[rename=$id] */ inside that function definition.\n");
                continue;
            }
            // Skip if someone does interesting things with calls.
            if (count($details['call']) > 0) {
                echo(" Rename issue related to $id() in " . $ast->statement->lhs->name->toString() . "\n");
                echo("  To disable this WARNING place /* @ignore[rename=$id] */ inside that function definition.\n");
                continue;
            }
            foreach ($details['block-local'] as $node) {
                // Realistically, only one can exist.
                $newname = array_shift($internal_vars);
                foreach ($details['var'] as $v) {
                    $v->value = $newname;
                }
                $ids[$id]['var'] = [];
                $details['var'] = $ids[$id]['var'];
                $node->value = $newname;
            }
        }

        // Then check for globals.
        foreach ($ids as $id => $details) {
            if (isset($annotations['ignore']['global'][$id]) || count($details['block-local']) > 0 || count($details['arg']) > 0) {
                continue;
            }
            if (count($details['var']) > 0 && (count($details['block-local']) === 0 && count($details['arg']) === 0)) {
                echo(" Reference to outside $id in " . $ast->statement->lhs->name->toString() . "\n");
                echo("  To disable this WARNING place /* @ignore[global=$id] */ inside that function definition.\n");
                echo("  Check lines: ");
                foreach ($details['var'] as $node) {
                    echo ($node->position['start-line'] . ', ');
                }
                echo("\n");
            }
        }
    }

    return $r;
}


// General purpose comment annotation processor.
function comment_annotations(string $comment): array {
    $r = [
        'remainder' => $comment,
        'params' => [],
        'return-block' => '',
        'param-block' => '',
        'virtual-name' => null,
        'virtual-title' => null
    ];

    // Parse the `@param` and `@return` bits.
    $matches = [];
    preg_match_all('/@([a-z]+)\[([^\]]*)\]([^@]*)/', $comment, $matches);
    for ($i = 0; $i < count($matches[0]); $i++) {
        switch ($matches[1][$i]) {
            case 'param':
                // First drop the matched bits from the matching comment.
                $r['remainder'] = str_replace($matches[0][$i], '', $r['remainder']);
                if ($r['param-block'] === '') {
                    $r['param-block'] = "| Argument name | type | description |\n";
                    $r['param-block'] .= "| ------------- | ---- | ----------- |\n";
                }
                $aname = trim(explode(',', $matches[3][$i], 2)[0]);
                $adesc = trim(explode(',', $matches[3][$i], 2)[1]);
                $r['params'][] = $aname;
                $r['param-block'] .= "| $aname | " . $matches[2][$i] . ' | ';
                $r['param-block'] .= str_replace("\n", ' ', str_replace("\n\n", '<br>', trim($adesc))) . " |\n";
                break;
            case 'return':
                $r['remainder'] = str_replace($matches[0][$i], '', $r['remainder']);
                $r['return-block'] = "\n\n| Return type | description |";
                $r['return-block'] .= "\n| ----------- | ------------|\n| ";
                $r['return-block'] .= $matches[2][$i] . ' | '; 
                $r['return-block'] .= str_replace("\n", ' ', str_replace("\n\n", '<br>', trim($matches[3][$i]))) . " |\n";
                break;
            case 'inertfunction':
                $r['remainder'] = str_replace($matches[0][$i], '', $r['remainder']);
                $r['virtual-name'] = trim(explode('(', $matches[3][$i], 2)[0]);
                $r['virtual-title'] = trim($matches[3][$i]);
            default:
                // Maybe be vocal?
        }
    }
    return $r;
}

foreach ($scripts as $filename) {
    $content = trim(file_get_contents($filename));
    $sname = substr($filename, 19);
    if ($content === '') {
        echo ("'$sname' seems to be empty.");
    } else {
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline([
            '601_castext',
            '602_castext_simplifier', 
            '680_gcl_sconcat'], 
            ['601_castext' => [
                'context' => 'maximasrc/' . $sname
            ]], false);


        $ast = null;
        try {
            $ast = $parser->parse($po->get_lexer($content));
        } catch (stack_maxima_parser_exception $e) {
            cli_error("PARSING FAIL: in $sname.");
             $ei = new stack_parser_error_interpreter($po);
            $result['exception'] = $e;
            $errors = [];
            $notes = [];
            cli_error($ei->interprete($e, $errors, $notes));
            exit(1);
        }
        $payload = [];
        $tests = [];
        $comments = []; // Endpos -> value 
        $virtualitems = [];      

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
                // Read annotations and add them to the position array.
                $item->position['annotations'] = [
                    'ignore' => [
                        'global' => [],
                        'rename' => []
                    ]
                ];
                foreach ($item->internalcomments as $c) {
                    $matches = [];
                    preg_match_all('/@([a-z]+)\[([a-z]+)=([^\]]*)\]/', $c->value, $matches);
                    if (count($matches[0]) > 0) {
                        for ($i = 0; $i < count($matches[0]); $i++) {
                            switch ($matches[1][$i]) {
                                case 'ignore':
                                    switch ($matches[2][$i]) {
                                        case 'global':
                                        case 'rename':
                                           $item->position['annotations']['ignore'][$matches[2][$i]][$matches[3][$i]] = $matches[3][$i];
                                            break;
                                        default:
                                            echo('Unknown @ignore target ' . $matches[0][$i] . "\n");
                                    }
                                    break;
                                default:
                                    echo('Unknown annotation ' . $matches[0][$i] . "\n");
                            }
                        }
                    }
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
                    if (strpos($comment, '@inertfunction')) {
                        // Extract documentation for virtual things.
                        $virtualitems[] = $comment;
                    } else {
                        $comments[$item->position['end']] = $comment;    
                    }
                }
            }
        }
        echo ("'$sname' " . count($payload) 
            . ' items of content and ' . count($tests) . " tests.\n");

        foreach ($payload as $corg) {
            // Note that the annotations we already red in.
            $c = $pipeline->filter($corg, $err, $err, $sec);
            // Collect the title row of a function before renaming.
            $pre_rename_title = null;
            if ($c->statement instanceof MP_Operation 
                && $c->statement->lhs instanceof MP_FunctionCall) {   
                $pre_rename_title = $c->statement->lhs->toString();
            }
            $c = var_rename($c); // Variable rename must happen after CASText compilation.
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
            if ($matching !== null && $c->statement instanceof MP_Operation) {
                // Is it a variable or a function def?
                $name = $c->statement->lhs;
                if ($name instanceof MP_FunctionCall) {
                    $name = $name->name->toString();
                    $title[$name] = $pre_rename_title;
                } else {
                    $name = $name->value;
                    $title[$name] = $name;
                }

                // Parse the `@param` and `@return` bits.
                $r = comment_annotations($matching);

                if ($c->statement->lhs instanceof MP_FunctionCall) {
                    if ($r['return-block'] === '') {
                        echo(" Return value not documented for '$name'.\n");
                    }
                    if (count($r['params']) !== count($c->statement->lhs->arguments)) {
                        echo(" Arguments for '$name' are not documented.\n");
                    }
                }

                // Then add the formatted ones back in.
                $docs[$name] = trim($r['remainder'] . "\n\n" . $r['param-block'] . "\n\n" . $r['return-block']);

                // Cats.
                $cats = array_slice(explode('/', $sname), 0, -1);
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
                            $categories[$path]->description = file_get_contents(__DIR__ . '/../stack/maximasrc' . $path . '/description.md');
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
        // Handle virtual ones.
        foreach ($virtualitems as $virtualitem) {
            // Parse the `@param` and `@return` bits.
            $r = comment_annotations($virtualitem);
            // Get the name and title.
            if ($r['virtual-name'] === null) {
                echo "\n\nWARNING! something virtual but no name.\n\n";
            } else {
                $name = $r['virtual-name'];
                $title[$name] = $r['virtual-title'] !== null ? $r['virtual-title'] : $name;
                // Then add the formatted ones back in.
                $docs[$name] = trim($r['remainder'] . "\n\n" . $r['param-block'] . "\n\n" . $r['return-block']);
                // Add to the tree.
                $categories[$ccat]->content[] = $name;
                $nametopath[$name] = $ccat;
            }

        }

        $i = 1;
        foreach ($tests as $corg) {
            $c = $pipeline->filter($corg, $err, $err, $sec);
            $test .= $c->toString($ts) . "$\n";
            $name = (new MP_String($sname . ':' . $c->position['start-line']))->toString();
            $test .= "s_exec_test($name,$i)$\n";
            $i++;
        }
    }
}

$test .= "simp:true$\nif s_test_fails = 0 then print(\"All tests successfully executed.\")$";

// Output primary content.
file_put_contents('../stack/maxima/maximasrccompiled.mac', $code);
file_put_contents('../stack/maxima/maximasrccompiled_tests.mac', $test);

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
    $rootlink = str_repeat('../', substr_count($path, '/'));

    $doc .= "\n# Section documentation for [STACK-Maxima]($rootlink) $path\n\n";

    
    if ($node->description !== '') {
        $doc .= trim($node->description) . "\n\n";
    }
    // Add a line before the items.
    $doc .= "\n---\n\n";

    sort($node->content);
    foreach ($node->content as $name) {
        $doc .= "\n## " . $title[$name] . "<a id='$name'></a>\n\n";
        $doc .= $docs[$name] . "\n\n";
        // Add a line after each item.
        $doc .= "\n---\n\n";
    }
 
    // Crosslink all `name` style references.
    foreach ($nametopath as $name => $npath) {
        if (mb_strpos($doc, "`$name`") !== false) {
            if ($npath === $path) {
                $doc = str_replace("`$name`", "[`$name`](#$name)", $doc);
            } else {
                $to = explode('/', $npath);
                $from = explode('/', $path);
                
                while (count($to) > 0 && count($from) > 0 && $to[0] === $from[0]) {
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
    if (!is_dir(__DIR__ . '/../doc/en/CAS/Library' . $path)) {
        mkdir(__DIR__ . '/../doc/en/CAS/Library' . $path, 0777, true);
    }
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
    $doc .= "\n- [" . $title[$name] . "](" . substr($nametopath[$name], 1) . "/index.md#$name)";
}
$doc .= "\n";
file_put_contents(__DIR__ . '/../doc/en/CAS/Library/alphabetical.md', $doc);

// A tree version of the index TODO: naming of sections needs to 
// get something more descriptive, through some metadata etc...
$doc = '<!-- NOTE! This file is autogenerated from files under stack/maximasrc do not edit here. -->';
$doc .= "\n# Tree index of STACK specific Maxima functions\n\n";
$doc .= "[Alphabetical index](alphabetical.md)\n\n";

function node_handler(doc_tree_node $node, $indent = 0, $path = ''): string {
    global $nametopath, $title;
    sort($node->content);
    $r = '';
    foreach ($node->content as $name) {
        $r .= str_repeat(' ', $indent) . "- [" . $title[$name] . "](" . substr($nametopath[$name], 1) . "/index.md#$name)\n"; 
    }
    foreach ($node->children as $n) {
        $r .= str_repeat(' ', $indent) . '- [' . $n->name . "]($path" . $n->name  . "/index.md) \n";
        $r .= node_handler($n, $indent + 4, $path . $n->name . '/');
    }
    return $r;
}
$doc .= node_handler($categories[''], 0);
$doc .= "\n";
file_put_contents(__DIR__ . '/../doc/en/CAS/Library/index.md', $doc);