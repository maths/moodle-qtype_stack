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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\util;

class StackPlotReplacer {
    public static function replace_plots(&$plots, &$text, $nameprefix, $storeprefix) {
        $i = 0;

        $text = preg_replace_callback('/["\']!ploturl!([\w\-\.]*?)\.(\w+)["\']/m',
            function ($matches) use (&$i, $nameprefix, &$plots) {
                $name = $nameprefix . "-" . $i . "." . $matches[2];
                $plots[$name] = $matches[1]. "." . $matches[2];
                $i++;
                return "\"".$name."\"";
            }, $text);

        $text = preg_replace_callback('/["\']@@PLUGINFILE@@\/([\w\-\.]*?)["\']/m', function ($matches) use ($storeprefix, &$plots) {
            $plots[$matches[1]] = $storeprefix . "-" . $matches[1];
            return "\"".$matches[1]."\"";
        }, $text);
    }

    public static function persist_plugin_files(\qtype_stack_question $question, $storeprefix) {
        global $CFG;
        foreach ($question->pluginfiles as $name => $content) {
            file_put_contents($CFG->dataroot . '/stack/plots/' . $storeprefix . '-' . $name, base64_decode($content));
        }
    }

}
