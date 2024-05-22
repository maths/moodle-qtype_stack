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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/blocks/root.specialblock.php');
require_once(__DIR__ . '/blocks/commonstring.block.php');
require_once(__DIR__ . '/blocks/stack_translate.specialblock.php');
require_once(__DIR__ . '/blocks/ioblock.specialblock.php');
require_once(__DIR__ . '/blocks/smlt.specialblock.php');
require_once(__DIR__ . '/blocks/pfs.specialblock.php');
require_once(__DIR__ . '/block.factory.php');

/**
 * In certain cases one may wish to collect a specialised processor to
 * override processing specific blocks. To do that one can provide a wrapper over
 * the normal processor.
 *
 * Typical use case is to override the handling of e.g. `[[validation:ans1]]` or
 * similar io-blocks.
 */

interface castext2_processor {
    // The override helps when you want to chain things. Basically, use it to
    // give the top most processor to the lower ones so that they can pass things
    // back when processing nested things.
    public function process(string $blocktype, array $arguments, castext2_processor $override = null): string;
}

class castext2_default_processor implements castext2_processor {
    public function process(string $blocktype, array $arguments, castext2_processor $override = null): string {
        $proc = $this;
        $block = null;
        if ($override !== null) {
            $proc = $override;
        }
        if ($blocktype === '%root') {
            $block = new stack_cas_castext2_special_root([]);
        } else if ($blocktype === '%strans') {
            $block = new stack_cas_castext2_special_stack_translate([]);
        } else if ($blocktype === '%cs') { // An alias for shorter content.
            $block = new stack_cas_castext2_commonstring([]);
        } else if ($blocktype === 'ioblock') {
            $block = new stack_cas_castext2_special_ioblock([]);
        } else if ($blocktype === 'smlt') {
            $block = new stack_cas_castext2_special_stack_maxima_latex_tidy([]);
        } else if ($blocktype === '%pfs') {
            $block = new stack_cas_castext2_special_rewrite_pluginfile_urls([]);
        } else {
            $block = castext2_block_factory::make($blocktype);
        }
        return $block->postprocess($arguments, $proc);
    }
}

class castext2_qa_processor extends castext2_default_processor {
    // Special one giving access to a question-attempt so that the blocks
    // can call things like `rewrite_pluginfile_urls`.
    public $qa;
    public function __construct($qa) {
        if (!method_exists($qa, 'rewrite_pluginfile_urls')) {
            stack_exception('Error in constructing castext2_qa_processor: argument must provide rewrite_pluginfile_urls.');
        }
        $this->qa = $qa;
    }
}
