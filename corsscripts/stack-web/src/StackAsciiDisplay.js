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
import '@ascii/ASCIIMathTeXImg.js';
/**
 * StackAsciiDisplay - Wrapper for STACK ASCII display blocks in standalone mode.
 *
 * Provides a simple API to initialize ASCII display blocks that render
 * ASCIIMath input as formatted mathematical output.
 *
 * @example
 * const display = new StackAsciiDisplay({
 *     containerId: 'ascii-block',
 *     inputElementId: 'ans1',
 *     operations: [
 *         { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
 *         { operation: 'extractor', type: 'lastexpr', targetinput: 'ans2' }
 *     ]
 * });
 * display.enable();
 */
export default class StackAsciiDisplay {
    /**
     * Create a StackAsciiDisplay instance.
     *
     * @param {Object} options - Configuration options
     * @param {string} options.containerId - ID of container element for the ASCII block
     * @param {string} options.inputElementId - ID of source textarea input element
     * @param {Object[]} options.operations - Array of filter/extractor operations
     */
    constructor(options) {
        // Resolve container by ID
        this.container = document.getElementById(options.containerId);
        if (!this.container) {
            console.error('StackAsciiDisplay: container not found:', options.containerId);
        }

        // Resolve input element by ID
        this.inputElement = document.getElementById(options.inputElementId);
        if (!this.inputElement) {
            console.error('StackAsciiDisplay: inputElement not found:', options.inputElementId);
        }

        this.operations = options.operations || [];

        // Extract inputIds from operations:
        // - First ID is the input element ID (source)
        // - Subsequent IDs come from targetinput properties of extractor operations
        this.inputIds = [options.inputElementId];

        this.operations.forEach(op => {
            if (op.operation === 'extractor' && op.targetinput) {
                this.inputIds.push(op.targetinput);
            }
        });

        // Call the core stackascii.js init function
        // Note: FRAME_ID will be undefined in standalone mode,
        // so scroll sync will be skipped automatically
        initAscii(this.inputIds, this.operations);

        this.setupStandaloneScrollSync();
    }

    /**
     * Set up scroll synchronization for standalone mode.
     * Uses direct event listeners instead of postMessage.
     */
    setupStandaloneScrollSync() {
        const inputEl = this.inputElement;
        const outputEl = document.getElementById('asciiContainerRow');

        if (!inputEl || !outputEl) {
            return;
        }

        inputEl.addEventListener('scroll', () => {
            const maxScroll = inputEl.scrollHeight - inputEl.clientHeight;
            const ratio = maxScroll > 0 ? inputEl.scrollTop / maxScroll : 0;

            const outputMaxScroll = outputEl.scrollHeight - outputEl.clientHeight;
            outputEl.style.scrollBehavior = 'auto';
            outputEl.scrollTop = outputMaxScroll > 0 ? ratio * outputMaxScroll : 0;
        });
    }
}
