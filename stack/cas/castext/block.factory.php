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

defined('MOODLE_INTERNAL') || die();

// Functions related to dealing with scientific units in STACK.
//
// @copyright  2017 Aalto University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

class castext_block_factory {
    /**
     * Creates a block of a given type. Or null if non existing type.
     */
    public static function make($type, $node, $session=null, $seed=null) {
        $class = self::class_for_type($type);
        if ($class === null) {
            return null;
        }
        return new $class($node, $session, $seed);
    }

    /**
     * The class name corresponding to a block type.
     * @param string $type block type name.
     * @return string corresponding class name. Or NULL.
     */
    protected static function class_for_type($type) {
        $types = self::get_available_types();

        if (array_key_exists($type, $types)) {
            return $types[$type];
        }
        return null;
    }

    /**
     * @return array of available type names.
     */
    public static function get_available_types() {
        static $types = array();
        if (count($types) > 0) {
            return $types;
        }

        foreach (new DirectoryIterator(__DIR__) as $item) {
            // Skip . and .. and dirs.
            if ($item->isDot() or $item->isDir()) {
                continue;
            }

            $itemname = $item->getFilename();
            if (substr($itemname, strlen($itemname) - strlen('.block.php')) === '.block.php') {
                $file = __DIR__ . "/$itemname";
                include_once($file);
                $blockname = substr($itemname, 0, strlen($itemname) - strlen('.block.php'));
                $class = "stack_cas_castext_{$blockname}";
                if (!class_exists($class)) {
                    continue;
                }
                $types[$blockname] = $class;
            }
        }

        return $types;
    }
}
