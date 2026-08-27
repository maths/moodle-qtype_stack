// Markdown-it plugin — bundled into stackascii.bundle.js via stackascii.js.
// Registers custom renderer rules and a collector-reset core rule on the shared
// markdownit instance created in markdown.js.
//
// To change how a token type is rendered, edit this file.
// To change the behaviour of a named transform (aligneq, boldfilter, …),
// edit the corresponding file in markdownittransforms/, then run: npm run build
//
// The `state` object is defined in markdown.js and passed in via options.state.
// It carries three fields that are refreshed before every render pass:
//   state.transforms   — ordered array of transform names to apply
//   state.transformLib — map from name → transform function
//   state.collector    — { blocks: [], isHTML: false } object populated here for use by extractors.
//                        Null when not initialised.

/**
 * Markdown-it plugin that registers custom renderer rules and a collector-reset
 * core rule on the provided markdownit instance.
 * @param {Object} mdit    - the markdownit instance to extend.
 * @param {Object} options - plugin options; must contain options.state (see markdown.js).
 */
export default function markdownitrules(mdit, options) {
    "use strict";
    const state = options.state;
    const originalCodeRule = mdit.renderer.rules.code_inline;

    // Core rule: runs before rendering to clear the block list from the previous pass.
    mdit.core.ruler.push('reset_collector', () => {
        if (state.collector) {
            state.collector.isHTML = true;
            state.collector.blocks = [];
        }
    });

    /**
     * Inline AsciiMath: a single backtick expression, e.g. `x^2 + 1`.
     */
    mdit.renderer.rules.code_inline = function(tokens, idx, options, env, self) {
        const code = tokens[idx].content;
        let rendered = '';
        if (state.transforms.length === 0) {
            rendered = originalCodeRule(tokens, idx, options, env, self);
        } else {
            rendered = applyTransforms(code, 'code_inline');
        }
        if (state.collector) {
            state.collector.blocks.push({ type: 'code_inline', raw: code, rendered });
        }
        return rendered;
    };

    /**
     * Multi-line AsciiMath block: opened and closed by a solitary backtick on its own line.
     */
    mdit.renderer.rules.asciimath_block = function(tokens, idx) {
        const code = tokens[idx].content;
        let rendered = '';
        if (state.transforms.length === 0) {
            rendered = '`' + mdit.render(code) + '`';
        } else {
            rendered = applyTransforms(code, 'asciimath_block');
        }
        if (state.collector) {
            state.collector.blocks.push({ type: 'asciimath_block', raw: code, rendered });
        }
        return rendered;
    };

    /**
     * Inline LaTeX: \(...\).
     * Recorded for extractors as a math_inline block.
     */
    mdit.renderer.rules.math_inline = function(tokens, idx) {
        const code = tokens[idx].content;
        let rendered = '';
        if (state.transforms.length === 0) {
            rendered = '\\(' + mdit.renderInline(code) + '\\)';
        } else {
            rendered = applyTransforms(code, 'math_inline');
        }
        if (state.collector) {
            state.collector.blocks.push({ type: 'math_inline', raw: code, rendered });
        }
        return rendered;
    };

    /**
     * Display LaTeX: \[...\].
     * Recorded for extractors as a math_block block.
     */
    mdit.renderer.rules.math_block = function(tokens, idx) {
        const code = tokens[idx].content;
        let rendered = '';
        if (state.transforms.length === 0) {
            rendered =  '\\[' + mdit.render(code) + '\\]';
        } else {
            rendered = applyTransforms(code, 'math_block');
        }
        if (state.collector) {
            state.collector.blocks.push({ type: 'math_block', raw: code, rendered });
        }
        return rendered;
    };

    /**
     * Split a raw block string into trimmed, non-empty lines ready for processing.
     * Blank lines are removed here so transforms do not need to handle them.
     * @param {string}   code - raw multi-line string from a markdown-it token.
     * @returns {string[]} array of trimmed non-empty lines.
     */
    function splitBlock(code) {
        return code.split(/\r?\n/).map(line => line.trim()).filter(line => line !== '');
    }

    /**
     * Convert a raw block string to a rendered string by:
     *   1. Splitting into lines and trimming.
     *   2. Passing the line array through each named transform in order.
     * Transforms receive an array of strings and must return an array of strings.
     * @param {string}  code         - raw multi-line string from a markdown-it token.
     * @returns {string} rendered string with a trailing newline.
     */
    function applyTransforms(code, rule) {
        let lines = splitBlock(code);
        for (const transform of state.transforms) {
            if (!state.transformLib[transform]) {
                // This should already be prevented by filter.block.php.
                throw new Error(`markdownitrules: unknown transform "${transform}"`);
            }
            lines = state.transformLib[transform](lines, rule);
        }
        return lines.join('\n') + '\n';
    }
}
