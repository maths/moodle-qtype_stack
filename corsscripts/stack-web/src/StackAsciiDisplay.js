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
 *     inputElementId: 'ascii-input',
 *     outputElementId: 'ascii-output',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
 *         { operation: 'extractor', type: 'lastexpr', targetinput: 'ans2' }
 *     ]
 * });
 *
 * @example
 * // Static supplied-text mode.
 * const display = new StackAsciiDisplay({
 *     suppliedTextElementId: 'ascii-supplied-text',
 *     outputElementId: 'ascii-output',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' }
 *     ]
 * });
 */
export default class StackAsciiDisplay {
    /**
     * Create a StackAsciiDisplay instance.
     *
     * @param {Object} options - Configuration options
     * @param {string} options.containerId - ID of container element. Mutually exclusive with outputElementId.
     * @param {string} options.inputElementId - ID of source textarea input element. Used only with outputElementId.
     * @param {string} options.suppliedTextElementId - ID of static source element. Used only with outputElementId.
     * @param {string} options.outputElementId - ID of rendered output element. Mutually exclusive with containerId.
     * @param {Object[]} options.operations - Array of filter/extractor operations. Extractors require inputElementId.
     * @param {string} options.initialText - Initial text for generated container-mode textarea.
     * @param {string} options.placeholder - Placeholder for generated container-mode textarea.
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
        const hasSuppliedTextElement = Boolean(options.suppliedTextElementId);

        if (hasContainer) {
            if (hasInputElement || hasSuppliedTextElement) {
                throw new Error('StackAsciiDisplay: containerId cannot be used with inputElementId or suppliedTextElementId');
            }
            this.setupContainerMode(options);
        } else {
            if (hasInputElement === hasSuppliedTextElement) {
                throw new Error('StackAsciiDisplay: specify exactly one of inputElementId or suppliedTextElementId');
            }
            this.setupExistingOutputMode(options, hasInputElement, hasSuppliedTextElement);
        }

        this.inputIds = this.inputElement ? [this.inputElement.id] : [];

        this.operations.forEach(op => {
            if (op.operation === 'extractor' && op.targetinput) {
                this.inputIds.push(op.targetinput);
            }
        });

        const initOptions = { outputElementId: this.outputElement.id };
        if (this.shellElement) {
            initOptions.shellElementId = this.shellElement.id;
        }
        if (this.renderedOutputElement) {
            initOptions.renderedOutputElementId = this.renderedOutputElement.id;
        }
        if (this.errorOutputElement) {
            initOptions.errorOutputElementId = this.errorOutputElement.id;
        }
        if (this.suppliedTextElement) {
            initOptions.suppliedTextElementId = this.suppliedTextElement.id;
        }
        initOptions.asciistrings = this.asciistrings;

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
     * @param {boolean} hasInputElement - Whether a live source input was provided.
     * @param {boolean} hasSuppliedTextElement - Whether a static source element was provided.
     */
    setupExistingOutputMode(options, hasInputElement, hasSuppliedTextElement) {
        this.container = null;

        this.inputElement = hasInputElement ? document.getElementById(options.inputElementId) : null;
        if (hasInputElement && !this.inputElement) {
            throw new Error(`StackAsciiDisplay: inputElement not found: ${options.inputElementId}`);
        }

        this.suppliedTextElement = hasSuppliedTextElement ? document.getElementById(options.suppliedTextElementId) : null;
        if (hasSuppliedTextElement && !this.suppliedTextElement) {
            throw new Error(`StackAsciiDisplay: suppliedTextElement not found: ${options.suppliedTextElementId}`);
        }

        this.outputElement = document.getElementById(options.outputElementId);
        if (!this.outputElement) {
            throw new Error(`StackAsciiDisplay: outputElement not found: ${options.outputElementId}`);
        }
        this.shellElement = null;
        this.renderedOutputElement = null;
        this.errorOutputElement = null;

        if (hasSuppliedTextElement && this.operations.some(op => op.operation === 'extractor')) {
            throw new Error('StackAsciiDisplay: extractors require inputElementId');
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

        this.container.classList.add('stack-ascii-display');
        this.dimensionOptions = {
            minWidth: options.minWidth,
            minHeight: options.minHeight,
            maxWidth: options.maxWidth,
            maxHeight: options.maxHeight
        };
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

        this.outputElement = document.createElement('div');
        this.outputElement.id = createUniqueId(options.containerId, 'output');
        this.outputElement.className = 'stack-ascii-output';
        this.shellElement = this.outputElement;

        this.renderedOutputElement = document.createElement('div');
        this.renderedOutputElement.id = createUniqueId(options.containerId, 'content');
        this.renderedOutputElement.className = 'stackascii-content';

        this.errorOutputElement = document.createElement('div');
        this.errorOutputElement.id = createUniqueId(options.containerId, 'errors');
        this.errorOutputElement.className = 'stackascii-errors';
        this.outputElement.appendChild(this.renderedOutputElement);
        this.outputElement.appendChild(this.errorOutputElement);

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
     * Mirror native textarea resizing onto the generated output pane.
     */
    setupOutputResizeSync() {
        const initialContainerWidth = this.container.style.width;
        let resizeStart = null;
        let userResized = false;

        /**
         * Apply the textarea's rendered size to the generated output pane.
         *
         * @param {number} width - Textarea width in pixels.
         * @param {number} height - Textarea height in pixels.
         * @param {boolean} updateContainerHeight - Whether to update the outer container height.
         */
        const syncOutputSize = (width, height, updateContainerHeight = true) => {
            const style = window.getComputedStyle(this.container);
            const chrome = {
                horizontal: parseCssPixels(style.paddingLeft, 12) +
                    parseCssPixels(style.paddingRight, 12) +
                    parseCssPixels(style.columnGap || style.gap, 12),
                vertical: parseCssPixels(style.paddingTop, 12) + parseCssPixels(style.paddingBottom, 12)
            };
            const limits = {
                minWidth: resolveCssLength(this.dimensionOptions.minWidth, this.container, 'width') ||
                    parseCssPixels(style.minWidth, 0),
                minHeight: resolveCssLength(this.dimensionOptions.minHeight, this.container, 'height') ||
                    parseCssPixels(style.minHeight, 0),
                maxWidth: resolveCssLength(this.dimensionOptions.maxWidth, this.container, 'width'),
                maxHeight: resolveCssLength(this.dimensionOptions.maxHeight, this.container, 'height')
            };
            const minInputWidth = limits.minWidth !== null ? Math.max(0, (limits.minWidth - chrome.horizontal) / 2) : null;
            const minInputHeight = limits.minHeight !== null ? Math.max(0, limits.minHeight - chrome.vertical) : null;
            const maxInputWidth = limits.maxWidth !== null ? Math.max(0, (limits.maxWidth - chrome.horizontal) / 2) : null;
            const maxInputHeight = limits.maxHeight !== null ? Math.max(0, limits.maxHeight - chrome.vertical) : null;

            width = clampLength(width, minInputWidth, maxInputWidth);
            height = clampLength(height, minInputHeight, maxInputHeight);

            if (width > 0) {
                this.inputElement.style.width = `${width}px`;
                this.inputPane.style.width = `${width}px`;
                this.outputElement.style.width = `${width}px`;
                this.outputPane.style.width = `${width}px`;
            }
            if (height > 0) {
                this.inputElement.style.height = `${height}px`;
                this.inputPane.style.height = `${height}px`;
                this.outputElement.style.height = `${height}px`;
                this.outputPane.style.height = `${height}px`;
                if (updateContainerHeight) {
                    const containerHeight = clampLength(height + chrome.vertical, limits.minHeight, limits.maxHeight);
                    if (containerHeight > 0) {
                        this.container.style.height = `${containerHeight}px`;
                    }
                }
            }
        };

        /**
         * Return generated horizontal sizing to the responsive CSS defaults.
         */
        const clearPaneWidth = () => {
            this.container.classList.remove('stack-ascii-display-resized');
            this.container.style.width = initialContainerWidth;
            this.inputElement.style.width = '';
            this.inputPane.style.width = '';
            this.outputElement.style.width = '';
            this.outputPane.style.width = '';
        };

        /**
         * Return generated pane sizing to the responsive CSS defaults.
         */
        const clearPaneSize = () => {
            clearPaneWidth();
            this.inputElement.style.height = '';
            this.inputPane.style.height = '';
            this.outputElement.style.height = '';
            this.outputPane.style.height = '';
        };

        /**
         * Prepare the responsive layout for a possible native textarea resize.
         */
        const startResizeSession = () => {
            const rect = this.inputElement.getBoundingClientRect();
            resizeStart = {
                width: rect.width,
                height: rect.height
            };
            userResized = false;
            syncOutputSize(rect.width, rect.height, false);
            this.container.classList.add('stack-ascii-display-resized');
            this.container.style.width = 'max-content';
        };

        /**
         * Keep explicit pane sizing only if the textarea actually changed size.
         */
        const finishResizeSession = () => {
            if (!resizeStart) {
                return;
            }

            const rect = this.inputElement.getBoundingClientRect();
            if (Math.abs(rect.width - resizeStart.width) > 1 || Math.abs(rect.height - resizeStart.height) > 1) {
                userResized = true;
                syncOutputSize(rect.width, rect.height);
            }

            if (!userResized) {
                clearPaneSize();
            }
            resizeStart = null;
        };

        /**
         * Drop manual widths when the viewport changes so they do not become a layout minimum.
         */
        const clearPaneWidthOnWindowResize = () => {
            if (!userResized || resizeStart) {
                return;
            }
            userResized = false;
            clearPaneWidth();
        };

        this.inputElement.addEventListener('pointerdown', startResizeSession);
        this.inputElement.addEventListener('mousedown', startResizeSession);
        this.inputElement.addEventListener('touchstart', startResizeSession);
        window.addEventListener('pointerup', finishResizeSession);
        window.addEventListener('pointercancel', finishResizeSession);
        window.addEventListener('mouseup', finishResizeSession);
        window.addEventListener('touchend', finishResizeSession);
        window.addEventListener('touchcancel', finishResizeSession);
        window.addEventListener('resize', clearPaneWidthOnWindowResize);

        if (typeof ResizeObserver === 'function') {
            this.outputResizeObserver = new ResizeObserver(() => {
                if (!resizeStart && !userResized) {
                    return;
                }

                const rect = this.inputElement.getBoundingClientRect();
                if (resizeStart &&
                        (Math.abs(rect.width - resizeStart.width) > 1 ||
                        Math.abs(rect.height - resizeStart.height) > 1)) {
                    userResized = true;
                }
                syncOutputSize(rect.width, rect.height);
            });
            this.outputResizeObserver.observe(this.inputElement);
        }
    }

    /**
     * Set up scroll synchronization for standalone mode.
     * Uses direct event listeners instead of postMessage.
     */
    setupStandaloneScrollSync() {
        const inputEl = this.inputElement;
        const outputEl = this.outputElement;

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
 * Resolve a numeric, px, %, rem, vh, or vw CSS length to pixels.
 *
 * @param {string|number} value - Length value to resolve.
 * @param {HTMLElement} element - Element whose parent supplies the percentage basis.
 * @param {string} axis - Dimension axis: "width" or "height".
 * @returns {?number} Resolved pixel length, or null when unsupported.
 */
function resolveCssLength(value, element, axis) {
    if (value === undefined || value === null || value === '' || value === 'none') {
        return null;
    }

    if (typeof value === 'number') {
        return value;
    }

    const text = String(value).trim();
    if (text.endsWith('px')) {
        return parseFloat(text);
    }

    if (text.endsWith('%')) {
        const basisElement = element.parentElement || document.documentElement;
        const rect = basisElement.getBoundingClientRect();
        const basis = axis === 'width' ? rect.width : rect.height;
        return basis > 0 ? basis * parseFloat(text) / 100 : null;
    }

    if (text.endsWith('rem')) {
        return parseFloat(text) * parseCssPixels(window.getComputedStyle(document.documentElement).fontSize, 16);
    }

    if (text.endsWith('vh')) {
        return window.innerHeight * parseFloat(text) / 100;
    }

    if (text.endsWith('vw')) {
        return window.innerWidth * parseFloat(text) / 100;
    }

    return null;
}

/**
 * Parse a CSS pixel value, returning a fallback when parsing fails.
 *
 * @param {string} value - CSS value to parse.
 * @param {number} fallback - Value to return for non-numeric input.
 * @returns {number} Parsed pixel value or fallback.
 */
function parseCssPixels(value, fallback) {
    const parsed = parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

/**
 * Clamp a length against optional minimum and maximum pixel limits.
 *
 * @param {number} value - Length to clamp.
 * @param {?number} min - Optional minimum.
 * @param {?number} max - Optional maximum.
 * @returns {number} Clamped length.
 */
function clampLength(value, min, max) {
    if (min !== null) {
        value = Math.max(value, min);
    }
    if (max !== null) {
        value = Math.min(value, max);
    }
    return value;
}
