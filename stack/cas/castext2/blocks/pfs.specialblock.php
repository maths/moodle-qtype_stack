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


require_once(__DIR__ . '/../block.interface.php');
require_once($CFG->libdir . '/questionlib.php');


/**
 * Special block allowing one to define plugin file details for subtrees
 * pf the concatenated CASText. Basically, tag content like 
 * the PRT-feedback that can consist of multiple sources with different 
 * filestores.
 */
class stack_cas_castext2_special_rewrite_pluginfile_urls extends stack_cas_castext2_block {
    public $filearea;
    public $itemid;
    public $component = 'qtype_stack';

    public function __construct($params, $children=array(), $mathmode=false, $value='') {
        parent::__construct($params, $children, $mathmode);
        $this->filearea = $params['filearea'];
        $this->itemid = $params['itemid'];
        if (isset($params['component'])) {
        	// For times when this library is in use on the other side.
        	$this->component = $params['component'];
        }
    }

    public function compile($format, $options): ?string {
        $r = '["%pfs",' . stack_utils::php_string_to_maxima_string($this->filearea) .
        	',' . stack_utils::php_string_to_maxima_string($this->filearea) .
        	',' . stack_utils::php_string_to_maxima_string($this->component);

        $flat = $this->is_flat;
        if (!$flat) {
            $r .= '["%root",';
        } else {
            $r .= 'sconcat(';
        }

        $items = array();
        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $items[] = $c;
            }   
        }
        $r .= implode(',', $items);

        if (!$flat) {
            $r .= ']';
        } else {
            $r .= ')';
        }
        $r .= ']';

        return $r;
    }

    public function is_flat(): bool {
        // Now then the problem here is that the flatness depends on the flatness of 
        // the blocks contents. If they all generate strings then we are flat but if not...
        $flat = true;

        foreach ($this->children as $child) {
            $flat = $flat && $child->is_flat();
        }

        return $flat;
    }

    public function postprocess(array $params, castext2_processor $processor): string {
    	// First collapse the content.
    	$content    = '';
        for ($i = 4; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $content .= $params[$i];
            }
        }

        // Then do the rewrite. Note we expect the processor used to have access to the relevant details.
        // You will need a parametric processor to do this.
        $content = $processor->qa->rewrite_pluginfile_urls($content, $params[3], $params[1], $params[2]);
        return $content;
    }

    public function validate_extract_attributes(): array {
        return array();
    }
}