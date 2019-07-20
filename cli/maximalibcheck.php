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

// This script parses STACKs Maxima libraries and tries to find variables that leak.
//
// @copyright  2019 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');


// Data collection.
$functionsdeclared = array();
$variablesdeclared = array();
$functionscalled = array();
$globalvariablesused = array();


// Get the files ../stack/maxima/*.mac.
foreach (glob("../stack/maxima/*.mac") as $filename) {
    if (strpos($filename, 'rtest_') !== false) {
        continue;
    }
    if (strpos($filename, 'unittests_load.mac') !== false) {
        continue;
    }

    cli_heading($filename);

    $contents = file_get_contents($filename);
    // We need to remove some comments as the parser cannot deal with all of them.
    if (strstr($contents, '/*')) {
        $match = array();
        preg_match_all('|/\*(.*)\*/|U', $contents, $match);
        foreach ($match[0] as $val) {
            // We want to match the number of lines...
            $replace = '';
            $i = 1;
            $lines = explode("\n", $val);
            while ($i < count($lines)) {
                $i++;
                $replace .= "\n";
            }
            $contents = str_replace($val, $replace, $contents);
        }
    }

    if (strpos($filename, 'assessment.mac') !== false) {
        // Some parser breaking cases. List calling and the wacky syntax of defines.
        $contents = str_replace('?\*autoconf\-version\*', '"cencored"', $contents);
        $contents = str_replace('define(UNARY_RECIP a, a^(-1)),', '"cencored",', $contents);
        $contents = str_replace('ex:subst(lambda([ex], UNARY_MINUS noun* ex), "-", ex),', '"cencored",', $contents);
        $contents = str_replace('ex:subst(lambda([ex1, ex2], ex1 noun* (UNARY_RECIP ex2)), "/", ex),', '"cencored",', $contents);
        $contents = str_replace('define(UNARY_RECIP a, a noun^ (-1)),', '"cencored",', $contents);

        $contents = str_replace('distrib_and(apply(?%and, append([apply(?%or, maplist(lambda([ex2], first(orlist2) %and ex2), ' .
                'args(orlist1)))], rest(orlist2))))', 'distrib_and(apply(and, append([apply(or, ' .
                'maplist(lambda([ex2], first(orlist2) and ex2), args(orlist1)))], rest(orlist2))))', $contents);
        $contents = str_replace('ret:ev(exc1 %or exc2, simp)', 'ret:ev(exc1 or exc2, simp)', $contents);
    }

    if (strpos($filename, 'stackmaxima.mac') !== false) {
        // Some parser breaking cases.
        $contents = str_replace('?\*autoconf\-version\*', '"cencored"', $contents);
    }

    if (strpos($filename, 'intervals.mac') !== false) {
        // Some parser breaking cases.
        $contents = str_replace('single_variable_solver_real_rec(ex %and (v>=0), v)',
                'single_variable_solver_real_rec(ex and (v>=0), v)', $contents);
        $contents = str_replace('ex:realsetmake(v, rs1) %or apply("%or", rs2)',
                'ex:realsetmake(v, rs1) or apply("%or", rs2)', $contents);
        $contents = str_replace('return(sol1 %or sol2)', 'return(sol1 or sol2)', $contents);
        $contents = str_replace('(second(args(ex))>0) %or (second(args(ex))<0)',
                '(second(args(ex))>0) or (second(args(ex))<0)', $contents);
        $contents = str_replace('', '', $contents);
        $contents = str_replace('', '', $contents);
        $contents = str_replace('', '', $contents);
    }

    // The parser does not deal with dollars.
    $contents = str_replace('$', ';', $contents);

    $filename = explode('/', $filename);
    $filename = $filename[count($filename) - 1];

    // Parse.
    try {
        $ast = maxima_parser_utils::parse($contents);
        $ast = maxima_parser_utils::position_remap($ast, $contents);
        $ast = maxima_parser_utils::strip_comments($ast);
        // Null recurse to get things tagged.
        $ast->callbackRecurse();

        cli_writeln('Items at top ' . count($ast->items));
        foreach ($ast->items as $top) {
            // All things are statements.
            $top = $top->statement;
            if ($top instanceof MP_PrefixOp && $top->op === '?') {
                $top = $top->rhs;
            }
            if ($top instanceof MP_FunctionCall) {
                if (isset($functionscalled[$top->name->toString()])) {
                    $functionscalled[$top->name->toString()][] = $filename . ' ' . $top->position['start'];
                } else {
                    $functionscalled[$top->name->toString()] = array($filename . ' ' . $top->position['start']);
                }
            } else if ($top instanceof MP_Operation && $top->op === ':') {
                if (isset($variablesdeclared[$top->lhs->toString()])) {
                    $variablesdeclared[$top->lhs->toString()][] = $filename . ' ' . $top->position['start'];
                } else {
                    $variablesdeclared[$top->lhs->toString()] = array($filename . ' ' . $top->position['start']);
                }
            } else if ($top instanceof MP_Operation && $top->op === ':=') {
                if (isset($functionsdeclared[$top->lhs->name->toString()])) {
                    $functionsdeclared[$top->lhs->name->toString()][] = $filename . ' ' . $top->position['start'];
                } else {
                    $functionsdeclared[$top->lhs->name->toString()] = array($filename . ' ' . $top->position['start']);
                }

                $vars = array();
                $usagesearch = function($node) use (&$vars, &$functionscalled, $filename) {
                    if ($node instanceof MP_Identifier &&
                        $node->is_variable_name() &&
                        $node->is_global()) {
                        $vars[$node->value] = true;
                    } else if ($node instanceof MP_FunctionCall) {
                        if (isset($functionscalled[$node->name->toString()])) {
                            $functionscalled[$node->name->toString()][] = $filename . ' ' . $node->position['start'];
                        } else {
                            $functionscalled[$node->name->toString()] = array($filename . ' ' . $node->position['start']);
                        }
                    } else if ($node instanceof MP_Identifier && $node->is_function_name() &&
                            !($node->parentnode instanceof MP_FunctionCall && $node->parentnode->name === $node)) {
                        if (isset($functionscalled[$node->toString()])) {
                            $functionscalled[$node->toString()][] = $filename . ' ' . $node->position['start'];
                        } else {
                            $functionscalled[$node->toString()] = array($filename . ' ' . $node->position['start']);
                        }
                    }
                    return true;
                };
                $top->rhs->callbackRecurse($usagesearch);

                if (count($vars) > 0) {
                    $globalvariablesused[$top->lhs->name->toString()] = $vars;
                }
            } else if ($top instanceof MP_If) {
                $usagesearch = function($node) use (&$functionscalled, $filename) {
                    if ($node instanceof MP_FunctionCall) {
                        if (isset($functionscalled[$node->name->toString()])) {
                            $functionscalled[$node->name->toString()][] = $filename . ' ' . $node->position['start'];
                        } else {
                            $functionscalled[$node->name->toString()] = array($filename . ' ' . $node->position['start']);
                        }
                    }
                    return true;
                };
                $top->callbackRecurse($usagesearch);
            } else {
                echo "\n something else @ " . $top->position['start'] ."\n " . $top->toString() . "\n";
            }
        }
    } catch (SyntaxError $e) {
        cli_writeln('parse error @ ' . $e->grammarOffset . ' - ' . $e->getMessage());
        $c = $e->grammarOffset;
        $l = 0;
        $theline = false;
        $lastlines = array();
        foreach (explode("\n", $contents) as $line) {
            $lastlines[] = $line;
            $l++;
            $c -= strlen($line) + 1;
            if ($c <= 0 && $theline === false) {
                $theline = $l;
            }
            if ($c < -50 ) {
                cli_writeln('   ~ line: ' . $theline);
                cli_writeln(implode("\n", $lastlines));
                break;
            }
            if (count($lastlines) > 5) {
                array_shift($lastlines);
            }
        }
    }
}


// Process data.
ksort($functionsdeclared);
ksort($variablesdeclared);
ksort($functionscalled);
ksort($globalvariablesused);

$raw = array('declared functions' => $functionsdeclared, 'declared values' => $variablesdeclared,
    'called functions' => $functionscalled, 'global variables used in functions' => $globalvariablesused);

$data = array('security-map' => array('undeclared functions' => array(), 'undeclared variables' => array()),
    'declared functions not used internaly' => array(), 'external functions used' => array(),
    'functions with undeclared global variables' => array(), 'raw' => $raw);

// Check the security-map, if the identifiers are not there maybe they should be.
$security = false;
if (is_readable('../stack/cas/security-map.json')) {
    $security = file_get_contents('../stack/cas/security-map.json');
}
if ($security !== false) {
    $security = json_decode($security, true);
} else {
    $security = array();
}
foreach ($functionsdeclared as $name => $declarations) {
    if (!isset($security[$name]) || !isset($security[$name]['function'])) {
        $data['security-map']['undeclared functions'][$name] = true;
    }
}
foreach ($functionscalled as $name => $declarations) {
    if (!isset($security[$name]) || !isset($security[$name]['function'])) {
        $data['security-map']['undeclared functions'][$name] = true;
    }
}
foreach ($variablesdeclared as $name => $declarations) {
    if (!isset($security[$name]) || ((!isset($security[$name]['variable'])) && (!isset($security[$name]['constant'])))) {
        $data['security-map']['undeclared variables'][$name] = true;
    }
}

// Check for internal and external usage.
$declared = array() + $functionsdeclared;

foreach ($functionscalled as $name => $calls) {
    if (isset($declared[$name])) {
        unset($declared[$name]);
    } else {
        $data['external functions used'][$name] = true;
    }
}
foreach ($declared as $key => $value) {
    $data['declared functions not used internaly'][$key] = true;
}

// Compare global scoped variable usage in functions to declared variables.
// If not one of them then probably bad. But the raw list should be checked anyway.
foreach ($globalvariablesused as $name => $vars) {
    $remainder = array();
    foreach ($vars as $var => $t) {
        if (!isset($variablesdeclared[$var])) {
            $remainder[] = $var;
        }
    }
    if (count($remainder) > 0) {
        $data['functions with undeclared global variables'][$name] = $remainder;
    }
}

// Write out.
file_put_contents('maxima-code-analysis.json', json_encode($data, JSON_PRETTY_PRINT));