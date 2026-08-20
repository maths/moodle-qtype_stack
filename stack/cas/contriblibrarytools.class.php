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

require_once(__DIR__ . '/../maximaparser/parser.options.class.php');
require_once(__DIR__ . '/../maximaparser/error.interpreter.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/castext2/utils.php');


/**
 * Set of tools for dealing with contrib libraries and their manifests.
 *
 * @package    qtype_stack
 * @copyright  2026 Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_cas_contrib_library_tools {

    /**
     * Fetches sources referred by a `genmanifest:` address, parses them and
     * produces a matching manifest, i.e., a dictionary with certain keys.
     * 
     * This does not build any documentation. See the CLI-tool for that.
     * 
     * Note! That `genmanifest:` addresses are meant for development time access
     * to newly constructed libraries, once ready one really should store that
     * manifest as a `.stacklib` file and refer to it using the normal require logic.
     *
     * Does automated requirement generation among the received code, i.e.,
     * a function `f` using function `g` in its body, and both being present
     * in the received code, will be interpreted to contain an inline
     * comment with the annotation `@require local/g`. `local` is the context
     * of the generated manifest, whatever the name of the actual library might
     * be does not matter. However, any library name must not be `local`.
     * 
     * Note that in the current model of libraries a function and an identifer
     * cannot share a name.
     * 
     * @param string a `genmanifest:` "address".
     * @param array optionally an array matching the `src` key in the result.
     * @param array optionally an array with the contents of those files, for 
     *      those cases where we run with different limitations...
     * @return a manifest or `false` if `src` hashes match
     */
    public static function generate_manifest(string $address, $oldhashes = null, $overridecontent = null) {
        static $cache = [];

        if (isset($cache[$address])) {
            return $cache[$address];
        }

        $results = [
            // We add metadata about the original files, for now just a hash.
            'src' => [],
            // For any named global variable or function, its initialisation or 
            // definition and a list of local or remote requirements.
            'contents' => [],
            // For all test cases found in the code the testcase and what local
            // functions it seems to rely on.
            'tests' => []
        ];

        // Strip the `genmanifest:` away and then explode things. No spaces in
        // component addresses, just between them.
        $parts = explode(' ', mb_substr($address, 12));
        $parts = array_map('trim', $parts);

        // Fetch the code and build the meta. Then check if we can abort...
        // Basically, maybe someone has a cached copy already.
        $code = [];
        foreach ($parts as $part) {
            if ($part !== '') {
                $src = false;
                if ($overridecontent !== null && isset($overridecontent[$part])) {
                    $src = $overridecontent[$part];
                } else {
                    $src = stack_fetch_included_content($part);
                }
                if ($src === false) {
                    throw new stack_exception('stack_cas_contrib_library_tools: could not fetch ' . $part);
                }
                $sha1 = sha1($src);
                $results['src'][$part] = ['sha1' => $sha1];
                $code[$part] = $src;
            }
        }
        if ($results['src'] == $oldhashes) {
            // Same content as someone expected.
            return false;
        }

        // Then identify the identifiers... we ignore redeclarations
        $identifiers = [];

        $po = stack_parser_options::get_cas_config();
        $po->dropcomments = false;
        $parser = $po->get_parser();


        foreach ($code as $part => $src) {
            $ast = null;
            try {
                $ast = $parser->parse($po->get_lexer($src));
            } catch (stack_maxima_parser_exception $e) {
                $ei = new stack_parser_error_interpreter($po);
                $errors = [];
                $notes = [];
                throw new stack_exception('stack_cas_contrib_library_tools: parsing error ' . $ei->interprete($e, $errors, $notes));
            }

            // Keep track of any non variable/function definitions.
            $preamble = [];
            // There is an actual space in this identifier and it is an intentional one as natural identifiers will never have such a thing.
            $pname = 'preamble ' . $part;
            // All the bits present in this part will be made to require those if any are found.
            $partdefs = [];

            $prevcomment = null;
            foreach ($ast->items as $item) {
                if ($item instanceof MP_Statement) {
                    if ($item->statement instanceof MP_Operation && $item->statement->op === ':=') {
                        // Function definition or a test definition.
                        if ($item->statement->lhs->value === 's_test_case') {
                            $results['tests'][] = $item;
                        } else {
                            if ($prevcomment !== null) {
                                // Keep the previous top level comment around for processing.
                                $partdefs[$item->statement->lhs->name->value] = ['ast' => [$prevcomment, $item], 'requires' => []];
                                $prevcomment = null;;
                            } else {
                                $partdefs[$item->statement->lhs->name->value] = ['ast' => [$item], 'requires' => []];
                            }
                        }
                    } elseif ($item->statement instanceof MP_Operation && $item->statement->op === ':') {
                        // Definition of a variable.
                        if ($prevcomment !== null) {
                            // Keep the previous top level comment around for processing.
                            $partdefs[$item->statement->lhs->value] = ['ast' => [$prevcomment, $item], 'requires' => []];
                            $prevcomment = null;;
                        } else {
                            $partdefs[$item->statement->lhs->value] = ['ast' => [$item], 'requires' => []];
                        }
                    } else {
                        // Something for the preamble.
                        $preamble[] = $item;
                    }
                } else if ($item instanceof MP_Comment) {
                    $prevcomment = $item;
                }
            }

            // If we had preamble, include it to all items of this part.
            if (count($preamble) > 0) {
                foreach ($partdefs as $key => $duh) {
                    $partdefs[$key]['requires'][] = 'local/' . $pname;
                }
                $results['contents'][$pname] = ['ast' => $preamble, 'requires' => []];
            }
            // Move the found id of this part to the manifest level.
            foreach ($partdefs as $id => $value) {
                $value['src'] = $part;
                $results['contents'][$id] = $value;
            }
        }

        // A filter for picking up annotations from comments.
        $ids = [];
        $commentfilter = function ($node) use (&$ids) {
            if ($node instanceof MP_Comment && $node->value !== '' && $node->value !== null) {
                // Should we have any require annotations.
                // "@require foo/bar" or even "@require foo/bar foo/baz foo/...".
                // Identifiers can have `%` but for now lets keep the library names clean.
                $matches = [];
                preg_match_all('/@require((\s+[a-zA-Z0-9_]+/[a-zA-Z0-9_%]+)+)/', $node->value, $matches);
                if (!empty($matches)) {
                    for ($i = 0; $i < count($matches[1]); $i++) {
                        $reqs = array_map('trim', explode($matches[1][$i]));
                        foreach ($reqs as $req) {
                            if ($req !== '') {
                                $ids[$req] = $req;
                            }
                        }
                    }
                }
            }
            return true;
        };

        // A filter for detecting usage inside CASText and to stop nested inclusions.
        $deepreffilter = function ($node) use (&$ids, $results) {
            if ($node instanceof MP_FunctionCall) {
                if ($node->name->toString() === 'castext') {
                    // Compile and do usage check.
                    $compiled = castext2_parser_utils::compile(
                        $node->arguments[0]->value,
                        castext2_parser_utils::RAWFORMAT
                    );
                    $used = maxima_parser_utils::variable_usage_finder($compiled);
                    foreach ($used['read'] as $key => $duh) {
                        if ($id != $key && isset($results['contents'][$key])) {
                            $ids['local/' . $key] = 'local/' . $key;
                        }
                    }
                    foreach ($used['calls'] as $key => $duh) {
                        if ($id != $key && isset($results['contents'][$key])) {
                            $ids['local/' . $key] = 'local/' . $key;
                        }
                    }
                } else if ($node->name->toString() === 'stack_include' || $node->name->toString() === 'stack_require') {
                    throw new stack_exception('stack_cas_contrib_library_tools: inclusion within inclusion, not allowed');
                }
            }
            return true;
        };

        // Now that the local identifiers are known do local requirement identification.
        foreach ($results['contents'] as $id => $value) {
            $ids = [];
            $fakeast = new MP_Root($value['ast']);
            $fakeast->callbackRecurse($commentfilter);
            $fakeast->callbackRecurse($deepreffilter);

            // Pick the identifiers used with the normal tools.
            $used = maxima_parser_utils::variable_usage_finder($fakeast);
            foreach ($used['read'] as $key => $duh) {
                if ($id != $key && isset($results['contents'][$key])) {
                    $ids['local/' . $key] = 'local/' . $key;
                }
            }
            foreach ($used['calls'] as $key => $duh) {
                if ($id != $key && isset($results['contents'][$key])) {
                    $ids['local/' . $key] = 'local/' . $key;
                }
            }

            // Push identified ones to the output.
            $results['contents'][$id]['requires'] = array_merge($value['requires'], array_keys($ids));

            // Then turn the AST back to code. TODO: can we store position data in advance, for errors?
            unset($results['contents'][$id]['ast']);
            $results['contents'][$id]['code'] = maxima_parser_utils::strip_comments($fakeast)->toString(['dealias' => false, 'nosemicolon' => false, 'nocomments' => true]);
        }

        // Also process the tests.
        for ($i = 0; $i < count($results['tests']); $i++) {
            $ids = [];
            $fakeast = new MP_Root([$results['tests'][$i]]);
            $fakeast->callbackRecurse($commentfilter);
            $fakeast->callbackRecurse($deepreffilter);

            // Pick the identifiers used with the normal tools.
            $used = maxima_parser_utils::variable_usage_finder($fakeast);
            foreach ($used['read'] as $key => $duh) {
                if (isset($results['contents'][$key])) {
                    $ids['local/' . $key] = 'local/' . $key;
                }
            }
            foreach ($used['calls'] as $key => $duh) {
                if (isset($results['contents'][$key])) {
                    $ids['local/' . $key] = 'local/' . $key;
                }
            }

            // TODO: this lacks the preamble requirement?
            $results['tests'][$i] = [
                'code' => maxima_parser_utils::strip_comments($fakeast)->toString(['dealias' => false, 'nosemicolon' => false]),
                'requires' => array_keys($ids)
            ];
        }

        $cache[$address] = $results;

        return $results;
    }


    /**
     * Loads a named library manifest from local storage .
     * @param string the name of the library.
     * @return the manifets for that library or `false` should that library not be available.
     */
    public static function fetch_library(string $name) {
        static $cache = [];

        if (isset($cache[$name])) {
            return $cache[$name];
        }

        // Confirm that this is a library name and not an attempt to explore the filesystem.
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new stack_exception('stack_cas_contrib_library_tools: bad libraryname ' . $name);
        }

        // First try local.
        $contents = file_get_contents(__DIR__ . '/../maxima/contrib/' . $name . '.stacklib');
        if ($contents !== false) {
            $cache[$name] = json_decode($contents, true);
            return $cache[$name];
        } else {
            $cache[$name] = false;

            // TODO: try fetching from github?
        }

        return $cache[$name];
    }

    /**
     * Fetches requirements and concatenates the output based on a manifest and a list of
     * required identifiers. Optionally, does not fetch items listed already loaded.
     * Will also return a list of loaded things.
     * @param string the name of the library.
     * @param array the manifest of that library, if `null` will fetch it, when working with 
     *      `genmanifest:` give the generated manifest here.
     * @param array list of the identifiers of that manifest to fetch
     * @param array list of the identifiers that have already been collected
     * @return array with two elements if successful, first the concatenated code and second 
     *      a list of identifiers that concatenation contains. If unsuccessful will contain 
     *      an error message as the only element.
     */
    public static function fetch_requirements(string $libraryname, $preloadedmanifest, array $required, $alreadyloaded = null): array {
        $manifest = $preloadedmanifest;
        if ($preloadedmanifest === null) {
            $manifest = self::fetch_library($libraryname);
        }
        if ($manifest === false) {
            return ['Failed to load library ' . $libraryname];
        }

        $extloaded = [];
        if ($alreadyloaded !== null) {
            $extloaded = $alreadyloaded;
        }
        $loaded = [];
        $edge = [];

        $result = '';

        // Start loading.
        foreach ($required as $req) {
            // These have no `local/` prefix these are raw requirements.
            if (!isset($manifest['contents'][$req])) {
                return ['Failed to find requirement from library ' . $libraryname . '/' . $req];
            }
            if (!isset($alreadyloaded[$libraryname . '/' . $req])) {
                $result = $manifest['contents'][$req]['code'] . "\n" . $result;
                $loaded[$libraryname . '/' . $req] = $libraryname . '/' . $req;
                foreach ($manifest['contents'][$req]['requires'] as $req2) {
                    $globalname = strpos($req2, 'local/') === 0 ? $libraryname . substr($req2, 5) : false;
                    if ($globalname !== false && !isset($extloaded[$globalname]) && !isset($loaded[$globalname])) {
                        // Local loaded with library name.
                        $edge[$globalname] = $globalname;
                    } else if ($globalname === false && !isset($extloaded[$req2]) && !isset($loaded[$req2])) {
                        // Non local loaded with other tools.
                        $edge[$req2] = $req2;
                    }
                }
            }
        }

        // Needed from elsewhere, keep a track of non manifest local references.
        $extreqs = [];

        // Then go and get all the requirements.
        while (!empty($edge)) {
            $newedge = [];
            foreach ($edge as $req) {
                if (isset($loaded[$req]) || isset($extloaded[$req])) {
                    continue;
                }
                list($lib, $id) = explode('/', $req, 2);
                if ($lib === $libraryname) {
                    // Local.
                    $result = $manifest['contents'][$id]['code'] . "\n" . $result;
                    $loaded[$req] = $req;
                    foreach ($manifest['contents'][$id]['requires'] as $req2) {
                        $globalname = strpos($req2, 'local/') === 0 ? $libraryname . substr($req2, 5) : false;
                        if ($globalname !== false && !isset($extloaded[$globalname]) && !isset($loaded[$globalname])) {
                            // Local loaded with library name.
                            $newedge[$globalname] = $globalname;
                        } else if ($globalname === false && !isset($extloaded[$req2]) && !isset($loaded[$req2])) {
                            // Non local loaded with other tools.
                            $newedge[$req2] = $req2;
                        }
                    }
                } else {
                    // Remote.
                    if (!isset($extreqs[$lib])) {
                        $extreqs[$lib] = [];
                    }
                    $extreqs[$lib][$id] = $id;
                }
            }
            $edge = $newedge;
        }


        // Recurse the non locals.
        foreach ($extreqs as $lib => $reqs) {
            $res = self::fetch_requirements($lib, null, $reqs, array_merge($loaded, $extloaded));
            if (count($res) === 1) {
                // Error case.
                return $res;
            }
            $result = $res[0] . "\n" . $result;
            $loaded = array_merge($loaded, $res[1]);
        }

        return [$result, $loaded];
    }
}