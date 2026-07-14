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
//   Opening marker: the delimiter string, optionally followed by spaces/tabs, at end of line.
//   Content:        any lines until the closing marker.
//   Closing marker: any line whose first non-whitespace characters are the delimiter.
//
// A delimiter followed by non-whitespace characters is left untouched so that
// inline parsing can handle it instead.

// UMD wrapper: works as a plain <script> (sets window.asciimathBlock) and as
// an esbuild-bundled ES module import (exports the function as default).
(function(global, factory) {
    if (typeof module !== 'undefined' && typeof exports === 'object') {
        module.exports = factory();
    } else {
        global.asciimathBlock = factory();
    }
})(typeof globalThis !== 'undefined' ? globalThis : this, function() {

/**
 * Markdown-it plugin that registers the asciimath_block block rule.
 * @param {Object} mdit - the markdownit instance to extend.
 * @param {Object} [options] - plugin options.
 * @param {string} [options.marker='`'] - delimiter marker string.
 * @param {Object} [options.state] - shared mutable state with a runtime delimiter.
 */
function asciimathBlock(mdit, delimiters) {
    /**
     * Return the active delimiter strings.
     * @returns {{openDelimiter: string, closeDelimiter: string}} delimiter marker strings.
     */
    function getDelimiters() {
        if (typeof delimiters === 'string' && delimiters.length > 0) {
            return { openDelimiter: delimiters, closeDelimiter: delimiters };
        }
        if (delimiters && typeof delimiters === 'object') {
            const openDelimiter = (typeof delimiters.openDelimiter === 'string' && delimiters.openDelimiter.length > 0)
                ? delimiters.openDelimiter
                : '`';
            const closeDelimiter = (typeof delimiters.closeDelimiter === 'string' && delimiters.closeDelimiter.length > 0)
                ? delimiters.closeDelimiter
                : openDelimiter;
            return { openDelimiter, closeDelimiter };
        }
        return { openDelimiter: '`', closeDelimiter: '`' };
    }

    /**
     * Check whether a line starts with the delimiter and contains only trailing
     * spaces or tabs after it.
     *
     * @param {Object} state  - markdown-it state object.
     * @param {number} pos    - start position of the candidate delimiter.
     * @param {number} end    - end position of the line.
     * @param {string} marker - delimiter marker string.
     * @returns {boolean} true when the line contains only the delimiter plus whitespace.
     */
    function lineHasOnlyDelimiter(state, pos, end, marker) {
        if (state.src.slice(pos, pos + marker.length) !== marker) {
            return false;
        }

        for (let i = pos + marker.length; i < end; i++) {
            const ch = state.src.charCodeAt(i);
            if (ch !== 0x20 /* space */ && ch !== 0x09 /* tab */) {
                return false;
            }
        }

        return true;
    }

    /**
     * Markdown-it block rule for multi-line AsciiMath blocks.
     * Registered before 'paragraph' so it takes priority over plain text.
     *
     * Opening marker: the delimiter on its own line (trailing spaces/tabs allowed).
     * Closing marker: the delimiter on its own line (no other non-whitespace content).
     * Emits an 'asciimath_block' token whose content is the joined interior lines.
     *
     * @param {Object}  state     - markdown-it state object.
     * @param {number}  startLine - index of the candidate opening line.
     * @param {number}  endLine   - index of the last line in the current block context.
     * @param {boolean} silent    - if true, probe only; do not emit tokens.
     * @returns {boolean} true if the rule consumed input, false otherwise.
     */
    function asciimathBlockRule(state, startLine, endLine, silent) {
        // Position of first non-whitespace character on the opening line.
        const startPos = state.bMarks[startLine] + state.tShift[startLine];
        const lineEnd  = state.eMarks[startLine];

        // Blank lines cannot be opening markers.
        if (state.tShift[startLine] < 0) {
            return false;
        }

        const { openDelimiter, closeDelimiter } = getDelimiters();

        // Opening line must contain only the delimiter plus optional spaces/tabs.
        if (!lineHasOnlyDelimiter(state, startPos, lineEnd, openDelimiter)) {
            return false;
        }

        // In silent (probe) mode, just confirm we can handle this line.
        if (silent) {
            return true;
        }

        // Scan forward to find the closing line (first non-blank line starting with marker).
        let closingLine = -1;
        const contentLines = [];

        for (let line = startLine + 1; line < endLine; line++) {
            if (state.tShift[line] < 0) {
                // Blank line — include as empty content.
                contentLines.push('');
                continue;
            }

            const lPos = state.bMarks[line] + state.tShift[line];
            const lEnd = state.eMarks[line];

            if (lineHasOnlyDelimiter(state, lPos, lEnd, closeDelimiter)) {
                // Found the closing marker: the delimiter with no other non-whitespace content.
                closingLine = line;
                break;
            }

            contentLines.push(state.src.slice(lPos, state.eMarks[line]));
        }

        // No closing marker found — do not consume this block.
        if (closingLine === -1) {
            return false;
        }

        // Emit the asciimath_block token.
        const token = state.push('asciimath_block', '', 0);
        token.content = contentLines.join('\n');
        token.map    = [startLine, closingLine + 1];
        token.markup = openDelimiter;
        token.meta = { closingMarkup: closeDelimiter };

        // Advance the parser past the closing line.
        state.line = closingLine + 1;
        return true;
    }

    mdit.block.ruler.before('paragraph', 'asciimath_block', asciimathBlockRule,
        { alt: ['paragraph', 'reference', 'blockquote', 'list'] });
}

return asciimathBlock;

}); // end UMD factory
