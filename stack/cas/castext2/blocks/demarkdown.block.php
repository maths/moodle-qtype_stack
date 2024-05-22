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

global $CFG;
require_once(__DIR__ . '/../block.interface.php');
require_once($CFG->libdir . '/weblib.php');

/**
 * Block that will simply convert anything inside it from Markdown
 * to HTML. Allowing certain types of mixed contents. Primarily exists
 * to map the problem of Markdown back to the normal HTML-processing.
 */
class stack_cas_castext2_demarkdown extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        // Basically mark the contents for post-processing.
        $r = new MP_List([new MP_String('demarkdown')]);

        // Problem is that we need to figure out if the content has HTML-blocks.
        // https://spec.commonmark.org/0.30/#html-block
        // We do not consider the injected values or any line changes they may contain.
        // Luckily the math-paint logic already does this and brings an estimate of format to
        // us.

        foreach ($this->children as $item) {
            $c = $item->compile($item->paintformat === null ? castext2_parser_utils::MDFORMAT : $item->paintformat, $options);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        return $r;
    }

    public function is_flat(): bool {
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor=null): string {
        // First collapse the content.
        $content = [''];
        $dontproc = [];
        for ($i = 1; $i < count($params); $i++) {
            if (is_array($params[$i]) && $params[$i][0] !== 'demoodle' &&
                    $params[$i][0] !== 'demarkdown' && $params[$i][0] !== 'htmlformat') {
                $content[count($content) - 1] .= $processor->process($params[$i][0], $params[$i]);
            } else if (is_array($params[$i])) {
                $dontproc[count($content)] = true;
                $content[] = $processor->process($params[$i][0], $params[$i]);
                $content[] = '';
            } else {
                $content[count($content) - 1] .= $params[$i];
            }
        }
        if ($content[count($content) - 1] === '') {
            unset($content[count($content) - 1]);
        }
        $r = '';
        foreach ($content as $k => $v) {
            if (isset($dontproc[$k])) {
                $r .= $v;
            } else {
                $v = markdown_to_html($v);
                // Note that at this point most of the interesting chars are entities.
                // We need to revert some of those conversions to allow later processign to
                // detect LaTeX for MathJax.
                // This makes the text such that it should not be reprocessed in any Markdown
                // filter luckily we will not do that.
                $r .= str_replace(['&#92;', '&#40;', '&#91;', '&#123;', '&#41;', '&#93;', '&#125;', '&#95;'],
                    ["\\", '(', '[', '{', ')', ']', '}', '_'], $v);
            }
        }

        return $r;
    }

    public function validate_extract_attributes(): array {
        return [];
    }
}
