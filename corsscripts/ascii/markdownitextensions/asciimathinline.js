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

// Markdown-it inline rule plugin.
//
// Syntax:
//   Opening marker: one or more delimiter characters.
//   Content:        any inline text until the matching closing marker run.
//   Closing marker: the same number of delimiter characters as the opener.
//
// When the delimiter is a backtick, this mirrors markdown-it's built-in
// code_inline parsing, but emits an 'asciimath_inline' token instead.

// UMD wrapper: works as a plain <script> (sets window.asciimathInline) and as
// an esbuild-bundled ES module import (exports the function as default).
(function(global, factory) {
    if (typeof module !== 'undefined' && typeof exports === 'object') {
        module.exports = factory();
    } else {
        global.asciimathInline = factory();
    }
})(typeof globalThis !== 'undefined' ? globalThis : this, function() {

/**
 * Markdown-it plugin that registers the asciimath_inline inline rule.
 * @param {Object} mdit - the markdownit instance to extend.
 * @param {Object} [options] - plugin options.
 * @param {string} [options.delimiter='`'] - single-character delimiter marker.
 * @param {Object} [options.state] - shared mutable state with a runtime delimiter.
 */
function asciimathInline(mdit, delimiter) {
    /**
     * Match markdown-it's built-in inline text terminators so a custom delimiter
     * can also stop plain-text scanning before the stock text rule swallows it.
     *
     * @param {number} ch - character code to test.
     * @returns {boolean} true when markdown-it treats this as a text terminator.
     */
    function isTextTerminatorChar(ch) {
        switch (ch) {
            case 10:  // \n
            case 33:  // !
            case 35:  // #
            case 36:  // $
            case 37:  // %
            case 38:  // &
            case 42:  // *
            case 43:  // +
            case 45:  // -
            case 58:  // :
            case 60:  // <
            case 61:  // =
            case 62:  // >
            case 64:  // @
            case 91:  // [
            case 92:  // backslash
            case 93:  // ]
            case 94:  // ^
            case 95:  // _
            case 96:  // `
            case 123: // {
            case 125: // }
            case 126: // ~
                return true;

            default:
                return false;
        }
    }

    /**
     * Stop plain-text scanning at the configured delimiter when markdown-it's
     * built-in text rule would otherwise consume through it.
     *
     * @param {Object}  state  - markdown-it state object.
     * @param {boolean} silent - if true, probe only; do not emit tokens.
     * @returns {boolean} true if the rule consumed input, false otherwise.
     */
    function asciimathTextRule(state, silent) {
        let pos = state.pos;
        const markerChar = delimiter ?? '`';
        const markerCode = markerChar.charCodeAt(0);

        while (pos < state.posMax &&
            !isTextTerminatorChar(state.src.charCodeAt(pos)) &&
            state.src.charCodeAt(pos) !== markerCode) {
            pos++;
        }

        if (pos === state.pos) {
            return false;
        }

        if (!silent) {
            state.pending += state.src.slice(state.pos, pos);
        }
        state.pos = pos;
        return true;
    }

    /**
     * Get or initialise the per-parse cache used to skip impossible closer searches.
     * Backticks reuse markdown-it's native cache because this rule replaces backticks.
     *
     * @param {Object} state  - markdown-it state object.
     * @param {string} marker - active delimiter marker.
     * @returns {{positions: Object, scanned: boolean, markScanned: Function}}
     *   cache bookkeeping helpers for the current delimiter.
     */
    function getDelimiterCache(state, marker) {
        if (marker === '`') {
            return {
                positions: state.backticks,
                scanned: state.backticksScanned,
                markScanned: () => {
                    state.backticksScanned = true;
                }
            };
        }

        if (!state.asciimathInlineMarkers) {
            state.asciimathInlineMarkers = {};
        }
        if (!state.asciimathInlineMarkers[marker]) {
            state.asciimathInlineMarkers[marker] = {};
        }
        if (!state.asciimathInlineScanned) {
            state.asciimathInlineScanned = {};
        }

        return {
            positions: state.asciimathInlineMarkers[marker],
            scanned: !!state.asciimathInlineScanned[marker],
            markScanned: () => {
                state.asciimathInlineScanned[marker] = true;
            }
        };
    }

    /**
     * Markdown-it inline rule for AsciiMath spans between delimiter runs.
     * For backticks, this intentionally mirrors markdown-it's built-in backtick rule
     * so the same source text becomes an asciimath_inline token instead of code_inline.
     *
     * @param {Object}  state  - markdown-it state object.
     * @param {boolean} silent - if true, probe only; do not emit tokens.
     * @returns {boolean} true if the rule consumed input, false otherwise.
     */
    function asciimathInlineRule(state, silent) {
        let pos = state.pos;
        const markerChar = delimiter ?? '`';
        const markerCode = markerChar.charCodeAt(0);

        if (state.src.charCodeAt(pos) !== markerCode) {
            return false;
        }

        const start = pos;
        pos++;

        const max = state.posMax;
        while (pos < max && state.src.charCodeAt(pos) === markerCode) {
            pos++;
        }

        const markup = state.src.slice(start, pos);
        const openerLength = markup.length;
        const cache = getDelimiterCache(state, markerChar);

        if (cache.scanned && (cache.positions[openerLength] || 0) <= start) {
            if (!silent) {
                state.pending += markup;
            }
            state.pos += openerLength;
            return true;
        }

        let matchEnd = pos;
        let matchStart;

        while ((matchStart = state.src.indexOf(markerChar, matchEnd)) !== -1) {
            matchEnd = matchStart + 1;

            while (matchEnd < max && state.src.charCodeAt(matchEnd) === markerCode) {
                matchEnd++;
            }

            const closerLength = matchEnd - matchStart;
            if (closerLength === openerLength) {
                if (!silent) {
                    const token = state.push('asciimath_inline', 'code', 0);
                    token.markup = markup;
                    token.content = state.src.slice(pos, matchStart)
                        .replace(/\n/g, ' ')
                        .replace(/^ (.+) $/, '$1');
                }
                state.pos = matchEnd;
                return true;
            }

            cache.positions[closerLength] = matchStart;
        }

        cache.markScanned();

        if (!silent) {
            state.pending += markup;
        }
        state.pos += openerLength;
        return true;
    }

    mdit.inline.ruler.before('text', 'asciimath_inline', asciimathInlineRule);

    const markerCode = (delimiter ?? '`').charCodeAt(0);
    if (!isTextTerminatorChar(markerCode)) {
        mdit.inline.ruler.before('text', 'asciimath_text', asciimathTextRule);
    }
}

return asciimathInline;

}); // end UMD factory
