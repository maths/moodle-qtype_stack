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
    const scrollSync = createAsciiScrollSync(markdownContainerId, output, frameId);
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
        if (scrollSync) {
            scrollSync.applySyncedScrollPosition();
        }

        // Tell MathJax to typeset only the output container element.
        if (typeof MathJax.typesetPromise === 'function') {
            const typeset = MathJax.typesetPromise([output]); // MathJax 3
            if (typeset && typeof typeset.then === 'function' && scrollSync) {
                typeset.then(() => {
                    scrollSync.applySyncedScrollPosition();
                });
            }
        } else if (MathJax.Hub && typeof MathJax.Hub.Queue === 'function') {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'asciiContainerRow']); // MathJax 2
            if (scrollSync) {
                MathJax.Hub.Queue(() => {
                    scrollSync.applySyncedScrollPosition();
                });
            }
        }
    }
    if (markdownContainerId) {
        // Debounce rendering so rapid keystrokes don't trigger multiple MathJax typesets.
        let debounceTimer;
        document.getElementById(markdownContainerId).addEventListener('change', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(renderMath, 100); // debounce 100ms
        });
    }

    renderMath(); // initial render on load

    if (scrollSync) {
        scrollSync.start();
    }
}

/**
 * Scroll-sync helpers.
 */

/**
 * Clamp a scroll ratio into the supported 0..1 range.
 *
 * @param {number|string} position fractional scroll position.
 * @return {number} safe scroll ratio.
 */
function clampScrollPosition(position) {
    const numeric = Number(position);
    if (!Number.isFinite(numeric)) {
        return 0;
    }
    return Math.min(1, Math.max(0, numeric));
}

/**
 * Apply a fractional scroll position to an element.
 *
 * Sync updates temporarily force `scroll-behavior:auto` so CSS smooth scrolling
 * does not animate the programmatic jump to the textarea's latest position.
 *
 * @param {HTMLElement} element scrollable element to update.
 * @param {number|string} position fractional scroll position.
 * @return {number} the clamped position that was applied.
 */
function setScrollPosition(element, position) {
    const clampedPosition = clampScrollPosition(position);
    const maxScroll = element.scrollHeight - element.clientHeight;
    const previousScrollBehavior = element.style ? element.style.scrollBehavior : null;
    if (element.style) {
        element.style.scrollBehavior = 'auto';
    }
    if (maxScroll <= 0) {
        element.scrollTop = 0;
        if (element.style) {
            element.style.scrollBehavior = previousScrollBehavior;
        }
        return clampedPosition;
    }
    element.scrollTop = clampedPosition * maxScroll;
    if (element.style) {
        element.style.scrollBehavior = previousScrollBehavior;
    }
    return clampedPosition;
}

/**
 * Create the textarea/output scroll synchronisation controller for one ASCII block.
 *
 * The textarea is the only source of truth. Incoming iframe messages update the
 * ASCII output, but scrolling the ASCII output does not send anything back.
 *
 * @param {string} sourceInputId DOM id of the linked freetext input.
 * @param {HTMLElement} output rendered ASCII output container.
 * @param {string} frameId iframe identifier used by STACK-JS messaging.
 * @return {?Object} sync controller, or null when sync cannot be initialised.
 */
function createAsciiScrollSync(sourceInputId, output, frameId) {
    if (!sourceInputId || !output || !frameId) {
        return null;
    }

    let syncedScrollPosition = 0;

    const applySyncedScrollPosition = () => {
        syncedScrollPosition = setScrollPosition(output, syncedScrollPosition);
    };

    window.addEventListener('message', (event) => {
        if (!(typeof event.data === 'string' || event.data instanceof String)) {
            return;
        }

        let message = null;
        try {
            message = JSON.parse(event.data);
        } catch (error) {
            return;
        }

        if (!(('version' in message) && message.version.startsWith('STACK-JS'))) {
            return;
        }
        if (!(('tgt' in message) && message.tgt === frameId && message.type === 'input-scroll-position')) {
            return;
        }
        if (message.name !== sourceInputId) {
            return;
        }

        syncedScrollPosition = clampScrollPosition(message.position);
        applySyncedScrollPosition();
    });

    return {
        applySyncedScrollPosition() {
            applySyncedScrollPosition();
        },
        start() {
            const registration = {
                version: 'STACK-JS:1.6.0',
                type: 'track-input-scroll',
                name: sourceInputId,
                'limit-to-question': true,
                src: frameId
            };
            window.parent.postMessage(JSON.stringify(registration), '*');
        }
    };
}
