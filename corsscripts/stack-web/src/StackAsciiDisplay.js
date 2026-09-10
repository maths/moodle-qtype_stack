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

        const outputPane = document.createElement('div');
        outputPane.className = 'stack-ascii-output-pane';
        outputPane.appendChild(this.outputElement);

        this.container.appendChild(inputPane);
        this.container.appendChild(outputPane);

        this.suppliedTextElement = null;
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
