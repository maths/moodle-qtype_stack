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

import initAscii from '../../ascii/stackascii.js';
import './stack-web.css';
import '../../ascii/ASCIIMathTeXImg.js';

let nextGeneratedId = 1;
const defaultAsciiStrings = typeof __STACK_ASCII_STRINGS__ === 'undefined' ? {} : __STACK_ASCII_STRINGS__;

/**
 * StackAsciiDisplay - Wrapper for STACK ASCII display blocks in standalone mode.
 *
 * Provides a simple API to initialize ASCII display blocks that render
 * ASCIIMath input as formatted mathematical output.
 *
 * @example
 * // Container mode. Creates a textarea and output element inside the container.
 * const display = new StackAsciiDisplay({
 *     containerId: 'ascii-block',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' }
 *     ]
 * });
 *
 * @example
 * // Existing live input/output mode.
 * const display = new StackAsciiDisplay({
 *     inputElementId: 'ascii-input-mount',
 *     outputElementId: 'ascii-output-mount',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
 *         { operation: 'extractor', type: 'lastexpr', targetinput: 'ans2' }
 *     ]
 * });
 *
 * @example
 * // Static supplied-text mode.
 * const display = new StackAsciiDisplay({
 *     outputElementId: 'ascii-output-mount',
 *     initialText: 'Read-only text with `x^2`',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' }
 *     ]
 * });
 */
export class StackAsciiDisplay {
    /**
     * Create a StackAsciiDisplay instance.
     *
     * @param {Object} options - Configuration options
     * @param {string} options.containerId - ID of container element. Mutually exclusive with outputElementId.
     * @param {string} options.inputElementId - ID of input mount element. Used only with outputElementId.
     * @param {string} options.outputElementId - ID of output mount element. Mutually exclusive with containerId.
     * @param {Object[]} options.operations - Array of filter/extractor operations. Extractors require inputElementId.
     * @param {string} options.initialText - Initial text for generated textareas or static output mode source.
     * @param {string} options.placeholder - Placeholder for generated textareas.
     * @param {string|number} options.initialWidth - Initial generated container width. Number values are pixels.
     * @param {string|number} options.initialHeight - Initial generated container height. Number values are pixels.
     * @param {string|number} options.minWidth - Minimum generated container width. Number values are pixels.
     * @param {string|number} options.minHeight - Minimum generated container height. Number values are pixels.
     * @param {string|number} options.maxWidth - Maximum generated container width. Number values are pixels.
     * @param {string|number} options.maxHeight - Maximum generated container height. Number values are pixels.
     * @param {Object} options.asciistrings - Optional language string overrides keyed by STACK asciistring id.
     */
    constructor(options) {
        if (!options) {
            throw new Error('StackAsciiDisplay: options are required');
        }

        this.operations = options.operations || [];
        this.asciistrings = {
            ...defaultAsciiStrings,
            ...(options.asciistrings || {})
        };

        const hasContainer = Boolean(options.containerId);
        const hasOutputElement = Boolean(options.outputElementId);
        if (hasContainer === hasOutputElement) {
            throw new Error('StackAsciiDisplay: specify exactly one of containerId or outputElementId');
        }

        const hasInputElement = Boolean(options.inputElementId);

        if (hasContainer) {
            if (hasInputElement) {
                throw new Error('StackAsciiDisplay: containerId cannot be used with inputElementId');
            }
            this.setupContainerMode(options);
        } else {
            this.setupExistingOutputMode(options, hasInputElement);
        }

        this.inputIds = this.inputElement ? [this.inputElement.id] : [];

        this.operations.forEach(op => {
            if (op.operation === 'extractor' && op.targetinput) {
                this.inputIds.push(op.targetinput);
            }
        });

        const initOptions = {
            outputElementId: this.outputElement.id,
            shellElementId: this.shellElement.id,
            renderedOutputElementId: this.renderedOutputElement.id,
            errorOutputElementId: this.errorOutputElement.id,
            asciistrings: this.asciistrings
        };

        if (this.suppliedTextElement) {
            initOptions.suppliedTextElementId = this.suppliedTextElement.id;
        }

        initAscii(this.inputIds, this.operations, initOptions);

        if (this.container) {
            this.setupOutputResizeSync();
        }

        if (this.inputElement) {
            this.setupStandaloneScrollSync();
        }
    }

    /**
     * Bind the display to caller-provided input/output elements.
     *
     * @param {Object} options - Configuration options.
     * @param {boolean} hasInputElement - Whether a live input mount was provided.
     */
    setupExistingOutputMode(options, hasInputElement) {
        this.container = null;

        this.inputMountElement = hasInputElement ? document.getElementById(options.inputElementId) : null;
        if (hasInputElement && !this.inputMountElement) {
            throw new Error(`StackAsciiDisplay: inputElement not found: ${options.inputElementId}`);
        }
        this.inputElement = null;
        this.suppliedTextElement = null;

        if (hasInputElement) {
            this.inputMountElement.innerHTML = '';
            this.inputMountElement.classList.add('stack-ascii-input-mount');
            this.inputElement = document.createElement('textarea');
            this.inputElement.dir = 'auto';
            this.inputElement.id = createUniqueId(options.inputElementId, 'input');
            this.inputElement.className = 'stack-ascii-input';
            this.inputElement.value = options.initialText || '';
            this.inputElement.placeholder = options.placeholder || '';
            this.inputMountElement.appendChild(this.inputElement);
        }

        this.outputElement = document.getElementById(options.outputElementId);
        this.outputElement.dir = 'auto';
        if (!this.outputElement) {
            throw new Error(`StackAsciiDisplay: outputElement not found: ${options.outputElementId}`);
        }
        this.outputElement.innerHTML = '';
        this.outputElement.classList.add('stack-ascii-output-mount');
        this.shellElement = this.createOutputElement(options.outputElementId, 'shell');
        this.outputElement.appendChild(this.shellElement);

        if (!hasInputElement && this.operations.some(op => op.operation === 'extractor')) {
            throw new Error('StackAsciiDisplay: extractors require inputElementId');
        }

        if (!hasInputElement) {
            this.suppliedTextElement = document.createElement('div');
            this.suppliedTextElement.id = createUniqueId(options.outputElementId, 'supplied-text');
            this.suppliedTextElement.hidden = true;
            this.suppliedTextElement.innerHTML = options.initialText === undefined || options.initialText === null ?
                '' : String(options.initialText);
            this.outputElement.parentNode.insertBefore(this.suppliedTextElement, this.outputElement);
        }
    }

    /**
     * Build the textarea/output pair inside a caller-provided container.
     *
     * @param {Object} options - Configuration options.
     */
    setupContainerMode(options) {
        this.container = document.getElementById(options.containerId);
        if (!this.container) {
            throw new Error(`StackAsciiDisplay: container not found: ${options.containerId}`);
        }
        this.container.dir = 'auto';

        this.container.classList.add('stack-ascii-display');
        [
            ['width', options.initialWidth],
            ['height', options.initialHeight],
            ['minWidth', options.minWidth],
            ['minHeight', options.minHeight],
            ['maxWidth', options.maxWidth],
            ['maxHeight', options.maxHeight]
        ].forEach(([property, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                this.container.style[property] = typeof value === 'number' ? `${value}px` : value;
            }
        });

        this.inputElement = document.createElement('textarea');
        this.inputElement.id = createUniqueId(options.containerId, 'input');
        this.inputElement.className = 'stack-ascii-input';
        this.inputElement.value = options.initialText || '';
        this.inputElement.placeholder = options.placeholder || '';

        this.outputElement = this.createOutputElement(options.containerId, 'output');
        this.shellElement = this.outputElement;

        const inputPane = document.createElement('div');
        inputPane.className = 'stack-ascii-input-pane';
        inputPane.appendChild(this.inputElement);
        this.inputPane = inputPane;

        const outputPane = document.createElement('div');
        outputPane.className = 'stack-ascii-output-pane';
        outputPane.appendChild(this.outputElement);
        this.outputPane = outputPane;

        this.container.appendChild(inputPane);
        this.container.appendChild(outputPane);

        this.suppliedTextElement = null;
    }

    /**
     * Create the rendered output shell and its content/error regions.
     *
     * @param {string} idBase - Source element id used as the generated id base.
     * @param {string} outputSuffix - Role suffix for the output shell id.
     * @returns {HTMLElement} Output shell element.
     */
    createOutputElement(idBase, outputSuffix) {
        const outputElement = document.createElement('div');
        outputElement.id = createUniqueId(idBase, outputSuffix);
        outputElement.className = 'stack-ascii-output';

        this.renderedOutputElement = document.createElement('div');
        this.renderedOutputElement.id = createUniqueId(idBase, 'content');
        this.renderedOutputElement.className = 'stackascii-content';

        this.errorOutputElement = document.createElement('div');
        this.errorOutputElement.id = createUniqueId(idBase, 'errors');
        this.errorOutputElement.className = 'stackascii-errors';
        outputElement.appendChild(this.renderedOutputElement);
        outputElement.appendChild(this.errorOutputElement);

        return outputElement;
    }

    /**
     * Mirror native textarea resizing onto the generated output pane.
     */
    setupOutputResizeSync() {
        const initialContainerWidth = this.container.style.width;
        let baselineSize = null;
        let userResized = false;

        /**
         * Apply a textarea size to the generated panes.
         *
         * @param {DOMRect} size - Observed textarea dimensions.
         */
        const applySyncedSize = (size) => {
            if (size.width > 0) {
                [this.inputElement, this.inputPane, this.outputElement, this.outputPane].forEach((element) => {
                    element.style.width = `${size.width}px`;
                });
            }

            if (size.height > 0) {
                [this.inputElement, this.inputPane, this.outputElement, this.outputPane].forEach((element) => {
                    element.style.height = `${size.height}px`;
                });
                this.container.style.height = 'fit-content';
            }
        };

        /**
         * Return generated horizontal sizing to the responsive CSS defaults.
         */
        const clearManualWidths = () => {
            this.container.classList.remove('stack-ascii-display-resized');
            this.container.style.width = initialContainerWidth;
            this.inputElement.style.width = '';
            this.inputPane.style.width = '';
            this.outputElement.style.width = '';
            this.outputPane.style.width = '';
        };

        /**
         * Record the current responsive size without switching layout modes.
         */
        const setBaselineSize = () => {
            const rect = this.inputElement.getBoundingClientRect();
            baselineSize = {
                width: rect.width,
                height: rect.height
            };
        };

        const sizeChanged = (rect) => Math.abs(rect.width - baselineSize.width) > 1 ||
            Math.abs(rect.height - baselineSize.height) > 1;

        /**
         * Sync generated panes after an actual textarea resize.
         */
        const syncObservedSize = () => {
            const rect = this.inputElement.getBoundingClientRect();

            if (!userResized && !sizeChanged(rect)) {
                return;
            }

            if (!userResized) {
                userResized = true;
                this.container.classList.add('stack-ascii-display-resized');
                this.container.style.width = 'max-content';
            }
            applySyncedSize(rect);
        };

        /**
         * Drop manual widths when the viewport changes so they do not become a layout minimum.
         */
        const clearManualWidthsOnWindowResize = () => {
            if (!userResized) {
                return;
            }
            userResized = false;
            clearManualWidths();
            setBaselineSize();
        };

        setBaselineSize();
        window.addEventListener('resize', clearManualWidthsOnWindowResize);
        this.outputResizeObserver = new ResizeObserver(syncObservedSize);
        this.outputResizeObserver.observe(this.inputElement);
    }

    /**
     * Set up scroll synchronization for standalone mode.
     * Uses direct event listeners instead of postMessage.
     */
    setupStandaloneScrollSync() {
        const inputEl = this.inputElement;
        const outputEl = this.shellElement || this.outputElement;

        inputEl.addEventListener('scroll', () => {
            const maxScroll = inputEl.scrollHeight - inputEl.clientHeight;
            const ratio = maxScroll > 0 ? inputEl.scrollTop / maxScroll : 0;

            const outputMaxScroll = outputEl.scrollHeight - outputEl.clientHeight;
            const previousScrollBehavior = outputEl.style.scrollBehavior;
            outputEl.style.scrollBehavior = 'auto';
            outputEl.scrollTop = outputMaxScroll > 0 ? ratio * outputMaxScroll : 0;
            outputEl.style.scrollBehavior = previousScrollBehavior;
        });
    }
}

/**
 * Generate a unique id for an element created inside a display container.
 *
 * @param {string} containerId - Source container id.
 * @param {string} suffix - Element role to append to the id.
 * @returns {string} Unique DOM id.
 */
function createUniqueId(containerId, suffix) {
    const base = String(containerId)
        .replace(/[^A-Za-z0-9_-]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'container';

    let id;
    do {
        id = `stack-ascii-${base}-${nextGeneratedId}-${suffix}`;
        nextGeneratedId++;
    } while (document.getElementById(id));

    return id;
}

/**
 * Run a callback once StackAsciiDisplay and MathJax are ready on the page.
 *
 * @param {Function} callback - Called when dependencies are ready.
 */
export function ready(callback) {
    if (typeof callback !== 'function') {
        throw new Error('StackWeb.ready: callback is required');
    }

    const waitForDependencies = () => {
        const root = typeof window === 'undefined' ? globalThis : window;
        if (root.MathJax && typeof root.MathJax.typesetPromise === 'function') {
            callback();
            return;
        }

        setTimeout(waitForDependencies, 100);
    };

    waitForDependencies();
}

export default StackAsciiDisplay;
