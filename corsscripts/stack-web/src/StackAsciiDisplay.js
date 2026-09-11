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
     */
    constructor(options) {
        if (!options) {
            throw new Error('StackAsciiDisplay: options are required');
        }

        this.operations = options.operations || [];

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
        if (this.suppliedTextElement) {
            initOptions.suppliedTextElementId = this.suppliedTextElement.id;
        }

        initAscii(this.inputIds, this.operations, initOptions);

        this.setupOutputResizeSync();
        this.setupStandaloneScrollSync();
    }

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

        if (hasSuppliedTextElement && this.operations.some(op => op.operation === 'extractor')) {
            throw new Error('StackAsciiDisplay: extractors require inputElementId');
        }
    }

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
        applyContainerDimensions(this.container, options);

        this.inputElement = document.createElement('textarea');
        this.inputElement.id = createUniqueId(options.containerId, 'input');
        this.inputElement.className = 'stack-ascii-input';
        this.inputElement.value = options.initialText || '';
        this.inputElement.placeholder = options.placeholder || '';

        this.outputElement = document.createElement('div');
        this.outputElement.id = createUniqueId(options.containerId, 'output');
        this.outputElement.className = 'stack-ascii-output';

        const inputPane = document.createElement('div');
        inputPane.className = 'stack-ascii-input-pane';
        inputPane.appendChild(this.inputElement);
        this.inputPane = inputPane;
        this.resizeHandle = document.createElement('div');
        this.resizeHandle.className = 'stack-ascii-resize-handle';
        this.resizeHandle.setAttribute('aria-hidden', 'true');
        inputPane.appendChild(this.resizeHandle);

        const outputPane = document.createElement('div');
        outputPane.className = 'stack-ascii-output-pane';
        outputPane.appendChild(this.outputElement);
        this.outputPane = outputPane;

        this.container.appendChild(inputPane);
        this.container.appendChild(outputPane);

        this.suppliedTextElement = null;
    }

    setupOutputResizeSync() {
        if (!this.container || !this.inputElement || !this.outputElement) {
            return;
        }

        const resizeState = {
            width: null,
            height: null,
            userResizing: false,
            drag: null
        };

        const syncOutputSize = (width, height) => {
            if (!width && !height) {
                return;
            }

            const chrome = getContainerChrome(this.container);
            const limits = getContainerDimensionLimits(this.container, this.dimensionOptions);
            const minInputWidth = limits.minWidth !== null ? Math.max(0, (limits.minWidth - chrome.horizontal) / 2) : null;
            const minInputHeight = limits.minHeight !== null ? Math.max(0, limits.minHeight - chrome.vertical) : null;
            const maxInputWidth = limits.maxWidth !== null ? Math.max(0, (limits.maxWidth - chrome.horizontal) / 2) : null;
            const maxInputHeight = limits.maxHeight !== null ? Math.max(0, limits.maxHeight - chrome.vertical) : null;

            width = clampLength(width, minInputWidth, maxInputWidth);
            height = clampLength(height, minInputHeight, maxInputHeight);

            if (width > 0) {
                resizeState.width = width;
                this.inputElement.style.width = `${width}px`;
                if (this.inputPane) {
                    this.inputPane.style.width = `${width}px`;
                }
                this.outputElement.style.width = `${width}px`;
                if (this.outputPane) {
                    this.outputPane.style.width = `${width}px`;
                }
                const containerWidth = clampLength((width * 2) + chrome.horizontal, limits.minWidth, limits.maxWidth);
                if (containerWidth > 0) {
                    this.container.style.width = `${containerWidth}px`;
                }
            }
            if (height > 0) {
                resizeState.height = height;
                this.inputElement.style.height = `${height}px`;
                if (this.inputPane) {
                    this.inputPane.style.height = `${height}px`;
                }
                this.outputElement.style.height = `${height}px`;
                if (this.outputPane) {
                    this.outputPane.style.height = `${height}px`;
                }
                const containerHeight = clampLength(height + chrome.vertical, limits.minHeight, limits.maxHeight);
                if (containerHeight > 0) {
                    this.container.style.height = `${containerHeight}px`;
                }
            }
        };

        const beginUserResize = (event) => {
            if (!isResizeHandlePointer(event, this.inputElement)) {
                return;
            }
            const point = getEventPoint(event);
            const rect = this.inputElement.getBoundingClientRect();
            resizeState.drag = {
                startX: point ? point.clientX : 0,
                startY: point ? point.clientY : 0,
                startWidth: resizeState.width || rect.width || this.inputElement.offsetWidth,
                startHeight: resizeState.height || rect.height || this.inputElement.offsetHeight
            };
            resizeState.userResizing = true;
            if (event.preventDefault) {
                event.preventDefault();
            }
        };
        const updateUserResize = (event) => {
            if (!resizeState.userResizing || !resizeState.drag) {
                return;
            }
            const point = getEventPoint(event);
            if (!point) {
                return;
            }
            syncOutputSize(
                resizeState.drag.startWidth + point.clientX - resizeState.drag.startX,
                resizeState.drag.startHeight + point.clientY - resizeState.drag.startY
            );
            if (event.preventDefault) {
                event.preventDefault();
            }
        };
        const finishUserResize = () => {
            if (!resizeState.userResizing) {
                return;
            }
            resizeState.userResizing = false;
            resizeState.drag = null;
        };

        this.inputElement.addEventListener('pointerdown', beginUserResize);
        this.inputElement.addEventListener('mousedown', beginUserResize);
        this.inputElement.addEventListener('touchstart', beginUserResize);
        if (this.resizeHandle) {
            this.resizeHandle.addEventListener('pointerdown', beginUserResize);
            this.resizeHandle.addEventListener('mousedown', beginUserResize);
            this.resizeHandle.addEventListener('touchstart', beginUserResize);
        }
        window.addEventListener('pointermove', updateUserResize);
        window.addEventListener('mousemove', updateUserResize);
        window.addEventListener('touchmove', updateUserResize, { passive: false });
        window.addEventListener('pointerup', finishUserResize);
        window.addEventListener('pointercancel', finishUserResize);
        window.addEventListener('mouseup', finishUserResize);
        window.addEventListener('touchend', finishUserResize);
        window.addEventListener('touchcancel', finishUserResize);

        if (typeof ResizeObserver === 'function') {
            this.outputResizeObserver = new ResizeObserver(() => {
                if (!resizeState.userResizing && resizeState.width !== null && resizeState.height !== null) {
                    syncOutputSize(resizeState.width, resizeState.height);
                }
            });
            this.outputResizeObserver.observe(this.inputElement);
            return;
        }

        window.addEventListener('resize', () => {
            if (resizeState.width !== null && resizeState.height !== null) {
                syncOutputSize(resizeState.width, resizeState.height);
            }
        });
    }

    /**
     * Set up scroll synchronization for standalone mode.
     * Uses direct event listeners instead of postMessage.
     */
    setupStandaloneScrollSync() {
        const inputEl = this.inputElement;
        const outputEl = this.outputElement;

        if (!inputEl || !outputEl) {
            return;
        }

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

function isResizeHandlePointer(event, element) {
    const point = getEventPoint(event);
    if (!point) {
        return true;
    }

    const rect = element.getBoundingClientRect();
    if (!rect.width || !rect.height) {
        return true;
    }

    const handleSize = 24;
    return point.clientX >= rect.right - handleSize && point.clientY >= rect.bottom - handleSize;
}

function getEventPoint(event) {
    if (!event) {
        return null;
    }

    if (typeof event.clientX === 'number' && typeof event.clientY === 'number') {
        return event;
    }

    if (event.touches && event.touches.length > 0) {
        return event.touches[0];
    }

    return null;
}

function applyContainerDimensions(container, options) {
    setCssLength(container, 'width', options.initialWidth);
    setCssLength(container, 'height', options.initialHeight);
    setCssLength(container, 'minWidth', options.minWidth);
    setCssLength(container, 'minHeight', options.minHeight);
    setCssLength(container, 'maxWidth', options.maxWidth);
    setCssLength(container, 'maxHeight', options.maxHeight);
}

function setCssLength(element, property, value) {
    if (value === undefined || value === null || value === '') {
        return;
    }

    element.style[property] = typeof value === 'number' ? `${value}px` : value;
}

function getContainerChrome(container) {
    const style = window.getComputedStyle(container);
    const paddingLeft = parseCssPixels(style.paddingLeft, 12);
    const paddingRight = parseCssPixels(style.paddingRight, 12);
    const paddingTop = parseCssPixels(style.paddingTop, 12);
    const paddingBottom = parseCssPixels(style.paddingBottom, 12);
    const gap = parseCssPixels(style.columnGap || style.gap, 12);

    return {
        horizontal: paddingLeft + paddingRight + gap,
        vertical: paddingTop + paddingBottom
    };
}

function getContainerDimensionLimits(container, options) {
    const style = window.getComputedStyle(container);
    return {
        minWidth: resolveCssLength(options.minWidth, container, 'width') || parseCssPixels(style.minWidth, 0),
        minHeight: resolveCssLength(options.minHeight, container, 'height') || parseCssPixels(style.minHeight, 0),
        maxWidth: resolveCssLength(options.maxWidth, container, 'width'),
        maxHeight: resolveCssLength(options.maxHeight, container, 'height')
    };
}

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

function parseCssPixels(value, fallback) {
    const parsed = parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function clampLength(value, min, max) {
    if (min !== null) {
        value = Math.max(value, min);
    }
    if (max !== null) {
        value = Math.min(value, max);
    }
    return value;
}
