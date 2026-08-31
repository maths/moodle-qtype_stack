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

// Bundle entry point for the full ascii bundle.
// Includes all dependencies so cors.php only needs to serve one file.
// Build with: npm run build
// Output: stackascii.bundle.js

// ASCIIMathTeXImg.js is loaded as a plain <script> tag by the PHP (sloppy mode).
// It sets window.AMparseMath before init() is called.

import calculation from './filters/calculation.js';
import cas from './filters/cas.js';
import markdown from './filters/markdown.js';
import plain from './filters/plain.js';

const filterlib = { calculation, cas, markdown, plain };

import lastblock from './extractors/lastblock.js';
import lastcalc from './extractors/lastcalc.js';
import lastexpr from './extractors/lastexpr.js';
import laststringremainder from './extractors/laststringremainder.js';
import laststringremainderwhitespace from './extractors/laststringremainderwhitespace.js';
import lastregexmatch from './extractors/lastregexmatch.js';
import lastregexremainder from './extractors/lastregexremainder.js';
import allregexmatch from './extractors/allregexmatch.js';
import allregexremainder from './extractors/allregexremainder.js';

const extractorlib = {
    lastblock,
    lastcalc,
    lastexpr,
    laststringremainder,
    laststringremainderwhitespace,
    lastregexmatch,
    lastregexremainder,
    allregexmatch,
    allregexremainder
};

/**
 * Initialise the ASCII block for one question instance.
 * Called by the PHP-compiled [[ascii]] block once all required input elements
 * are available in the DOM.
 *
 * @param {string[]} inputIds   - DOM element ids; inputIds[0] is the free-text
 *   textarea (source), inputIds[1..N] are the answer inputs for extractors,
 *   in the same order as the [[extractor]] blocks.
 * @param {Object[]} operations - ordered array of operation objects compiled from
 *   [[filter]] and [[extractor]] child blocks, e.g.
 *   [{ operation:'filter',    type:'markdown', transforms:'aligneq' },
 *    { operation:'extractor', type:'lastexpr', targetinput:'ans2'     }]
 */
export default function init(inputIds, operations) {
    const markdownContainerId = inputIds.length ? inputIds[0] : null;
    const suppliedText = document.getElementById('asciiSuppliedText').innerHTML;
    const output = document.getElementById('asciiContainerRow');
    const frameId = (typeof FRAME_ID !== 'undefined') ? FRAME_ID : null;
    const syncScrollPosition = createScrollSyncHandler(markdownContainerId, frameId, output);

    // inputIds[1..N] correspond to each extractor's target answer input in order.
    const alloperations = operations;
    // blockCollector is populated by the active filter's renderer rules and then
    // read by each extractor.  It is reset at the start of every filter render pass.
    const blockCollector = { blocks: [], isHTML: false };

    /**
     * Re-render the display and re-run all extractors from the current textarea value.
     * Called on every `change` event (debounced) and once immediately on load.
     */
    function renderMath() {
        let raw = '';
        if (markdownContainerId) {
            raw = document.getElementById(markdownContainerId).value;
        } else {
            raw = suppliedText;
        }

        let processedOutput = raw;
        let isHTML = false;
        let displayfixed = false; // true once a filter with display:'true' has run
        let answerIndex = 1;      // tracks which inputIds entry the next extractor writes to

        if (alloperations) {
            alloperations.forEach((currentop, i) => {
                if (currentop.operation === 'filter') {
                    const filter = filterlib[currentop.type];
                    if (filter) {
                        // reset:'true' re-processes the original raw input rather than
                        // the output of the previous filter in the chain.
                        let filterInput = processedOutput;
                        if (currentop.reset === 'true') {
                            filterInput = raw;
                        }
                        // The filter is responsible for resetting blockCollector.blocks
                        // at the start of its own render pass (see markdownitrules.js).
                        const filterOutput = filter(filterInput, blockCollector, currentop);
                        if (!displayfixed) {
                            processedOutput = filterOutput;
                            isHTML = blockCollector.isHTML;
                        }
                        // display:'true' freezes processedOutput so subsequent filters
                        // cannot modify what is shown to the student.
                        if (currentop.display === 'true') {
                            displayfixed = true;
                        }
                    }
                } else if (currentop.operation === 'extractor') {
                    // Fall back to lastexpr if the requested extractor type is unknown.
                    const extractor = (extractorlib[currentop.type]) ? extractorlib[currentop.type] : extractorlib['lastexpr'];
                    const answerEl = document.getElementById(inputIds[answerIndex]);
                    answerIndex++;
                    if (extractor && answerEl) {
                        let value = extractor(raw, blockCollector.blocks, currentop);
                        const oldValue = answerEl.value;
                        // Clear the input on extraction failure rather than leaving a stale value.
                        if (value === 'ERROR') {
                            answerEl.value = '';
                        } else {
                            answerEl.value = value;
                        }
                        // Only fire 'change' when the value actually changed to avoid
                        // unnecessary STACK validation requests.
                        if (answerEl.value !== oldValue) {
                            answerEl.dispatchEvent(new Event('change'));
                        }
                    }
                }
            });
        }

        if (!isHTML) {
            output.classList.add("plaintext")
        }
        output.innerHTML = processedOutput;
        syncScrollPosition();

        // Tell MathJax to typeset only the output container element.
        if (typeof MathJax.typesetPromise === 'function') {
            MathJax.typesetPromise([output]).then(() => { // MathJax 3
                syncScrollPosition();
            });
        } else if (MathJax.Hub && typeof MathJax.Hub.Queue === 'function') {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'asciiContainerRow']); // MathJax 2
            MathJax.Hub.Queue(() => {
                syncScrollPosition();
            });
        }
    }
    if (markdownContainerId) {
        // Debounce rendering so rapid keystrokes don't trigger multiple MathJax typesets.
        let debounceTimer;
        const handleInput = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(renderMath, 100); // debounce 100ms
        };
        document.getElementById(markdownContainerId).addEventListener('change', handleInput);
        if (!frameId) {
            document.getElementById(markdownContainerId).addEventListener('input', handleInput);
        }
    }

    renderMath(); // initial render on load
}

/**
 * Scroll-sync helpers.
 */

/**
 * Apply a fractional scroll position to an element.
 *
 * Sync updates temporarily force `scroll-behavior:auto` so CSS smooth scrolling
 * does not animate the programmatic jump to the textarea's latest position.
 *
 * @param {HTMLElement} element scrollable element to update.
 * @param {number} position fractional scroll position.
 */
function setScrollPosition(element, position) {
    const maxScroll = element.scrollHeight - element.clientHeight;
    const previousScrollBehavior = element.style.scrollBehavior;
    element.style.scrollBehavior = 'auto';
    element.scrollTop = maxScroll > 0 ? position * maxScroll : 0;
    element.style.scrollBehavior = previousScrollBehavior;
}

/**
 * Register iframe scroll syncing for the rendered ASCII output and return a
 * callback that reapplies the last known input scroll position.
 *
 * @param {?string} markdownContainerId textarea element id.
 * @param {?string} frameId parent frame id for postMessage coordination.
 * @param {HTMLElement} output rendered output container.
 * @returns {Function}
 */
function createScrollSyncHandler(markdownContainerId, frameId, output) {
    // We don't have two containers so nothing to synch.
    if (!markdownContainerId || !frameId) {
        return () => undefined;
    }

    let syncedScrollPosition = 0;
    const syncScrollPosition = () => {
        setScrollPosition(output, syncedScrollPosition);
    };

    window.addEventListener('message', (event) => {
        const message = JSON.parse(event.data);
        if (message.tgt !== frameId || message.type !== 'input-scroll-position' || message.name !== markdownContainerId) {
            return;
        }
        syncedScrollPosition = message.position;
        syncScrollPosition();
    });

    const registration = {
        version: 'STACK-JS:1.6.0',
        type: 'track-input-scroll',
        name: markdownContainerId,
        'limit-to-question': true,
        src: frameId
    };
    window.parent.postMessage(JSON.stringify(registration), '*');

    return syncScrollPosition;
}
