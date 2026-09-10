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

/**
 * StackAsciiDisplay - Wrapper for STACK ASCII display blocks in standalone mode.
 *
 * Provides a simple API to initialize ASCII display blocks that render
 * ASCIIMath input as formatted mathematical output.
 *
 * @example
 * // Live input mode.
 * const display = new StackAsciiDisplay({
 *     containerId: 'ascii-block',
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
 *     containerId: 'ascii-block',
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
     * @param {string} options.containerId - ID of container element for the ASCII block
     * @param {string} options.inputElementId - ID of source textarea input element. Required unless suppliedTextElementId is set.
     * @param {string} options.suppliedTextElementId - ID of static source element. Required unless inputElementId is set.
     * @param {string} options.outputElementId - ID of rendered output element
     * @param {Object[]} options.operations - Array of filter/extractor operations. Extractors require inputElementId.
     */
    constructor(options) {
        if (!options) {
            throw new Error('StackAsciiDisplay: options are required');
        }

        this.container = document.getElementById(options.containerId);
        if (!this.container) {
            throw new Error(`StackAsciiDisplay: container not found: ${options.containerId}`);
        }

        const hasInputElement = Boolean(options.inputElementId);
        const hasSuppliedTextElement = Boolean(options.suppliedTextElementId);
        if (hasInputElement === hasSuppliedTextElement) {
            throw new Error('StackAsciiDisplay: specify exactly one of inputElementId or suppliedTextElementId');
        }

        this.inputElement = hasInputElement ? document.getElementById(options.inputElementId) : null;
        if (hasInputElement && !this.inputElement) {
            throw new Error(`StackAsciiDisplay: inputElement not found: ${options.inputElementId}`);
        }

        this.suppliedTextElement = hasSuppliedTextElement ? document.getElementById(options.suppliedTextElementId) : null;
        if (hasSuppliedTextElement && !this.suppliedTextElement) {
            throw new Error(`StackAsciiDisplay: suppliedTextElement not found: ${options.suppliedTextElementId}`);
        }

        this.outputElementId = options.outputElementId || this.findDefaultOutputElementId();
        this.outputElement = document.getElementById(this.outputElementId);
        if (!this.outputElement) {
            throw new Error(`StackAsciiDisplay: outputElement not found: ${this.outputElementId}`);
        }

        this.operations = options.operations || [];
        if (hasSuppliedTextElement && this.operations.some(op => op.operation === 'extractor')) {
            throw new Error('StackAsciiDisplay: extractors require inputElementId');
        }

        this.inputIds = hasInputElement ? [options.inputElementId] : [];

        this.operations.forEach(op => {
            if (op.operation === 'extractor' && op.targetinput) {
                this.inputIds.push(op.targetinput);
            }
        });

        const initOptions = { outputElementId: this.outputElementId };
        if (hasSuppliedTextElement) {
            initOptions.suppliedTextElementId = options.suppliedTextElementId;
        }

        initAscii(this.inputIds, this.operations, initOptions);

        this.setupStandaloneScrollSync();
    }

    findDefaultOutputElementId() {
        const outputElement = (
            this.container.matches('.stack-ascii-output')
                ? this.container
                : this.container.querySelector('.stack-ascii-output')
        ) || document.getElementById('asciiContainerRow');

        if (!outputElement) {
            return 'asciiContainerRow';
        }

        if (!outputElement.id) {
            throw new Error('StackAsciiDisplay: outputElementId is required when the output element has no ID');
        }

        return outputElement.id;
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
