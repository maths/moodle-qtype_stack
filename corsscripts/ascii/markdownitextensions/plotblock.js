// This file is part of Stack - https://stack.maths.ed.ac.uk
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
 * This is part of the free text input/ ASCII display block.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Markdown-it block rule plugin.
//
// Syntax:
//   Opening marker: !!plot alone on a line, with optional leading/trailing whitespace.
//   Content:        concise plot instructions.
//   Closing marker: !!plot alone on a line, with optional leading/trailing whitespace.

(function(global, factory) {
    if (typeof module !== 'undefined' && typeof exports === 'object') {
        module.exports = factory();
    } else {
        global.plotBlock = factory();
    }
})(typeof globalThis !== 'undefined' ? globalThis : this, function() {

/**
 * Markdown-it plugin that registers the plot_block block rule.
 * @param {Object} mdit - the markdownit instance to extend.
 */
function plotBlock(mdit) {
    "use strict";

    function markerLine(state, line) {
        if (state.tShift[line] < 0) {
            return false;
        }
        const start = state.bMarks[line] + state.tShift[line];
        const end = state.eMarks[line];
        return state.src.slice(start, end).trim() === '!!plot';
    }

    /**
     * Markdown-it block rule for student plot blocks.
     *
     * @param {Object}  state     - markdown-it state object.
     * @param {number}  startLine - index of the candidate opening line.
     * @param {number}  endLine   - index of the last line in the current block context.
     * @param {boolean} silent    - if true, probe only; do not emit tokens.
     * @returns {boolean} true if the rule consumed input, false otherwise.
     */
    function plotBlockRule(state, startLine, endLine, silent) {
        if (!markerLine(state, startLine)) {
            return false;
        }
        if (silent) {
            return true;
        }

        let closingLine = -1;
        const contentLines = [];

        for (let line = startLine + 1; line < endLine; line++) {
            if (markerLine(state, line)) {
                closingLine = line;
                break;
            }

            if (state.tShift[line] < 0) {
                contentLines.push('');
            } else {
                const start = state.bMarks[line] + state.tShift[line];
                contentLines.push(state.src.slice(start, state.eMarks[line]));
            }
        }

        if (closingLine === -1) {
            return false;
        }

        const token = state.push('plot_block', '', 0);
        token.content = contentLines.join('\n');
        token.map = [startLine, closingLine + 1];
        token.markup = '!!plot';

        state.line = closingLine + 1;
        return true;
    }

    mdit.block.ruler.before('paragraph', 'plot_block', plotBlockRule,
        { alt: ['paragraph', 'reference', 'blockquote', 'list'] });
}

return plotBlock;

}); // end UMD factory
