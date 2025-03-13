<?php
// This file is part of Stateful.
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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2017 Matti Harjula.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class castext2_block_factory {
    /**
     * Cache the block types so that we do not need to check from the
     * filesystem every time.
     */
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    private static $types = [];

    /**
     * Creates a block of a given type. Or null if non existing type.
     */
    public static function make($type, $params=[], $children=[], $mathmode=false) {
        $class = self::class_for_type($type);
        if ($class === null) {
            $class = self::class_for_type('unknown');
            // Add a param for the unknown case handling.
            // The space in " type" is intentional.
            $params = array_merge($params, [' type' => $type]);
        }
        return new $class($params, $children, $mathmode);
    }
    /**
     * The class name corresponding to a block type.
     * @param string $type block type name.
     * @return string corresponding class name. Or NULL.
     */
    protected static function class_for_type($type) {
        $ts = self::get_available_types();
        if (array_key_exists($type, $ts)) {
            return $ts[$type];
        }
        return null;
    }
    /**
     * Add description here.
     * @return array of available type names.
     */
    public static function get_available_types() {
        if (count(self::$types) > 0) {
            return self::$types;
        }
        foreach (new DirectoryIterator(__DIR__ . '/blocks') as $item) {
            // Skip . and .. and dirs.
            if ($item->isDot() || $item->isDir()) {
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
                self::$types[$blockname] = $class;
            }
        }
        // Add some specials, not all of them.
        self::$types['pfs'] = 'stack_cas_castext2_special_rewrite_pluginfile_urls';
        return self::$types;
    }
    /**
     * Register a new block type from outside the normal logic.
     */
    public static function register($blockname, $class) {
        if (count(self::$types) === 0) {
            // Make sure the normal types have been loaded.
            self::get_available_types();
        }
        self::$types[$blockname] = $class;
    }
}
