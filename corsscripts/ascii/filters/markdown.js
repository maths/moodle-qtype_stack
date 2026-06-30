// Markdown filter — creates a markdownit instance with the full transformLib.
// Called by stackascii.js as: filter(rawText, blockCollector, op)
// where op is the [[filter]] block's parameter object, e.g.
//   { operation: 'filter', type: 'markdown', transforms: 'aligneq,boldfilter' }
//
// The transformLib is the authoritative registry for named transforms.
// To add a new transform:
//   1. Create its file in markdownittransforms/ (use the NN_ prefix for ordering).
//   2. Import it here and add it to transformLib.
//   3. Document it in doc/en/Authoring/Question_blocks/ASCII.md.

import markdownit from '../markdownit.js';
import asciimathBlock from '../markdownitextensions/asciimathblock.js';
import asciimathInline from '../markdownitextensions/asciimathinline.js';
import markdownitrules from './markdownitrules.js';

// tex.js uses named CJS exports (exports.tex = ...) — import the whole namespace.
import * as mdItPluginTex from '../markdownitextensions/tex.js';

import asciimath from '../markdownittransforms/100_asciimath.js';
import aligneq from '../markdownittransforms/200_aligneq.js';
import boldfilter from '../markdownittransforms/250_boldfilter.js';
import minwrap from '../markdownittransforms/900_minwrap.js';

/**
 * Registry maps the transform name strings used in the [[filter]] block's `transforms`
 * parameter to the actual transform functions.  The ordering of entries here does not
 * affect execution order — that is determined by the comma-separated list in `transforms`.
 * @type {Object.<string, function(string[]): string[]>}
 */
const transformLib = {
    asciimath,
    boldfilter,
    aligneq,
    minwrap
};

/**
 * Shared mutable state updated before each render so the converter instance uses the
 * correct transforms and collector for the current render pass.
 * @property {string[]}    transforms   - ordered array of transform names, derived from op.transforms.
 * @property {Object}      transformLib - map from name → transform function.
 * @property {Object|null} collector    - { blocks: [], isHTML = false } object populated by the renderer rules.
 *   null when not initialised by filter.
 * @property {string}      delimiter    - opening AsciiMath delimiter string.
 * @property {string}      closingdelimiter - closing AsciiMath delimiter string.
 */
const state = { transforms: [], transformLib, collector: null, delimiter: '`', closingdelimiter: '`' };
let converter = null;

/**
 * Create a markdown-it instance configured for the current delimiter.
 * mdItPluginTex.tex must come before markdownitrules.
 *
 * @param {Object} delimiters - opening and closing AsciiMath delimiter strings.
 * @returns {Object} configured markdown-it instance.
 */
function createConverter(delimiters) {
    return markdownit({ html: true })
        .use(mdItPluginTex.tex, { render: (content) => content, delimiters: 'brackets' })
        .use(asciimathBlock, delimiters)
        .use(asciimathInline, delimiters)
        .use(markdownitrules, { state });
}

/**
 * Entry point called by stackascii.js for each render pass.
 * Updates the shared state, creates the converter on first use,
 * and then renders the text.
 * @param {string}      text          - the raw student input to render.
 * @param {Object|null} blockCollector - { blocks: [] } collector for extractors, or null.
 * @param {Object}      op            - the [[filter]] block parameter object;
 *   op.transforms is a comma-separated list of transform names (e.g. 'aligneq,boldfilter').
 * @returns {string} rendered HTML string.
 */
export default function markdown(text, blockCollector, op) {
    // Split op.transforms (e.g. 'aligneq, boldfilter') into an ordered array of
    // trimmed, non-empty names (['aligneq', 'boldfilter']) that applyTransforms
    // iterates over when processing each block.
    state.transforms = (op.transforms || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
    state.delimiter = (typeof op.delimiter === 'string') ? op.delimiter : '`';
    state.closingdelimiter = (typeof op.closingdelimiter === 'string' && op.closingdelimiter !== '')
        ? op.closingdelimiter
        : state.delimiter;
    state.collector = blockCollector || null;
    if (state.collector) {
        state.collector.delimiter = state.delimiter;
        state.collector.closingdelimiter = state.closingdelimiter;
    }
    if (converter === null) {
        converter = createConverter({
            openDelimiter: state.delimiter,
            closeDelimiter: state.closingdelimiter
        });
    }
    return converter.render(text);
}
