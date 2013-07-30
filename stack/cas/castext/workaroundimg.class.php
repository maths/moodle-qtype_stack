<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * workaroundimg is a workaround for embedding static images into stack-questions.
 * Expect this class to dissappear and not to appear in any documentation. Once
 * TinyMCE embedded images work this will have no role.
 *
 * This class was deprecated from the very moment of its creation.
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../conditionalcasstring.class.php');
require_once(dirname(__FILE__) . '/../casstring.class.php');

class stack_cas_castext_workaroundimg extends stack_cas_castext_block {

    private $src = NULL;

    private $style = NULL;
 
    private $hashed = NULL;

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        global $CFG;

        $this->src = $this->get_node()->get_parameter("src",NULL);
        $this->style = $this->get_node()->get_parameter("style",NULL);

        // Nothing to do...
        if ($this->src == NULL) {
            return;
        }

        // Map the url to multiple hashes to form the filename
        $hash = md5($this->src).sha1($this->src).hash('crc32',$this->src);

        // Should the url have a file-type obviously present...
        $type = "";
        $lastdot = strrpos($this->src,".");
        if ($lastdot !== FALSE && (strlen($this->src)-$lastdot) < 6) {
            $type = substr($this->src,$lastdot);
        }
        $this->hashed = $hash.$type;

        // Do we have that file?
        if (file_exists($CFG->dataroot . "/stack/plots/wimg-" . $this->hashed)) {
            // Ok so do not retrieve again...
            return;
        } else {
            $contents = file_get_contents($this->src);
            if ($contents === FALSE) {
                // Got nothing
                $this->hashed = NULL;
                return;
            } else {
                file_put_contents($CFG->dataroot . "/stack/plots/wimg-" . $this->hashed,$contents);
            }
        }
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Zero effect
        return $conditionstack;
    }

    public function process_content($evaluatedcassession,$conditionstack = NULL) {
        $img = "";
        if ($this->hashed == NULL) {
            $img = "<b>NO SRC NO PIC</b>";
        } else if ($this->style == NULL) {
            $url = moodle_url::make_file_url('/question/type/stack/plot.php', '/wimg-') . $this->hashed;
            $img = "<img src='$url'/>";
        } else {
            $url = moodle_url::make_file_url('/question/type/stack/plot.php', '/wimg-') . $this->hashed;
            if (strpos($this->style,"'")===FALSE) {
                $img = "<img src='$url' style='".$this->style."'/>";
            } else {
                $img = "<img src='$url' style=\"".$this->style."\"/>";
            }
        }

        $this->get_node()->convert_to_text($img);

        return false;
    }

}
