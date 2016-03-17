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
 * A type for the external block handling LaTeX to PNG processing, requires
 * ImageMagick and a LaTeX installation.
 *
 * Allows complete source-code generation or using templates.
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../external.class.php');

class stack_cas_castext_external_latex extends stack_cas_castext_external_handler {

    private $template = false;

    private $templates = array("basic" => "template/basic.tex");

    private $timeout = 10;

    private $error = false;

    public function extract_attributes($node, &$tobeevaluatedcassession, $conditionstack) {
        $this->template = $node->get_parameter("template", false);
        if ($this->template !== false && !$this->templates[$this->template]) {
            $this->template = false;
            echo "bad template";
        }
    }

    public function set_attributes($evaluatedcassession) {
        // Nothing needed.
    }

    public function get_generated_files() {
        global $CFG;

        return array($CFG->dataroot . "/stack/plots/latex-" . $this->name . ".png");
    }

    public function get_source_file_extension() {
        return ".tex";
    }

    public function process($labelmap) {
        global $CFG;
        if ($this->template !== false) {
            $code = file_get_contents(__DIR__ . '/'.$this->templates[$this->template]);
            $code = str_replace("__TEMPLATE__", file_get_contents($labelmap["__SOURCE_CODE__"]), $code);
            file_put_contents($labelmap["__SOURCE_CODE__"], $code);
        }
        $cwd = dirname($labelmap["__SOURCE_CODE__"]);
        $pipes = array();
        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'));

        $config = stack_utils::get_config();

        $latexcommand = $config->externalblocklatexcommand;

        if (!$latexcommand) {
            throw new stack_exception('stack_cas_castext_external_latex_connection: LaTeX-command undefined');
        }

        $latexprocess = proc_open($latexcommand. " " .$labelmap["__SOURCE_CODE__"], $descriptors, $pipes, $cwd);

        if (!is_resource($latexprocess)) {
            throw new stack_exception('stack_cas_castext_external_latex_connection: could not open a LaTeX process');
        }

        $starttime = microtime(true);
        $continue = true;

        if (!stream_set_blocking($pipes[1], false)) {
            $this->debug->log('', 'Warning: could not stream_set_blocking to be false on the LaTeX process.');
        }

        $ret = "";
        while ($continue and !feof($pipes[1])) {
            $now = microtime(true);
            if (($now - $starttime) > $this->timeout) {
                $procarray = proc_get_status($latexprocess);
                if ($procarray['running']) {
                    proc_terminate($latexprocess);
                }
                $continue = false;
            } else {
                $out = fread($pipes[1], 1024);
                if ('' == $out) {
                    // Pause.
                    usleep(10000);
                }
                $ret .= $out;
            }
        }

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        if (file_exists(str_replace(".tex", ".pdf", $labelmap["__SOURCE_CODE__"]))) {
            $image = new Imagick();
            $image->setResolution(150, 150);
            $image->readImage(str_replace(".tex", ".pdf", $labelmap["__SOURCE_CODE__"]));

            $image->trimImage(0);

            $image->writeImage($CFG->dataroot . "/stack/plots/latex-" . $this->name . ".png");

            // Clean up.
            unlink(str_replace(".tex", ".pdf", $labelmap["__SOURCE_CODE__"]));
            unlink(str_replace(".tex", ".aux", $labelmap["__SOURCE_CODE__"]));
            unlink(str_replace(".tex", ".log", $labelmap["__SOURCE_CODE__"]));
        } else {
            // Something went wrong...
            $this->error = $ret;
        }
    }

    public function get_replacement_text() {
        if ($this->error !== false) {
            return "There was a problem with the LaTeX generation:<pre>".$this->error."</pre>";
        }

        $url = moodle_url::make_file_url('/question/type/stack/plot.php', '/') . "latex-" . $this->name . ".png";

        return "<img src='$url'/>";
    }

    public function validate_extract_attributes() {
        // The attributes of this block contain nothing to validate.
        return array();
    }

}
