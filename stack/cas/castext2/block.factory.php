<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

class castext2_block_factory {
    /**
     * Creates a block of a given type. Or null if non existing type.
     */
    public static function make($type, $params=array(), $children=array(), $mathmode=false) {
        $class = self::class_for_type($type);
        if ($class === null) {
            return null;
        }
        return new $class($params, $children, $mathmode);
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
        foreach (new DirectoryIterator(__DIR__ . '/blocks') as $item) {
            // Skip . and .. and dirs.
            if ($item->isDot() or $item->isDir()) {
                continue;
            }
            $itemname = $item->getFilename();
            if (substr($itemname, strlen($itemname) - strlen('.block.php')) === '.block.php') {
                $file = __DIR__ . "/blocks/$itemname";
                include_once($file);
                $blockname = substr($itemname, 0, strlen($itemname) - strlen('.block.php'));
                $class = "stack_cas_castext2_{$blockname}";
                if (!class_exists($class)) {
                    continue;
                }
                $types[$blockname] = $class;
            }
        }
        return $types;
    }
}
