// This file is part of Stack - http://stack.maths.ed.ac.uk/
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
 * Main STACK metadata component
 *
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

import {BaseComponent} from 'core/reactive';
import {metadata} from 'qtype_stack/metadata/metadata';

export default class extends BaseComponent {
    create() {
        this.name = 'stack-metadata-datarow';
        this.selectors = {
            DELETE: `[name="smd_delete"]`,
            ADD: `[name="smd_add"]`,
        };
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new this({
            element: document.querySelector(target),
            reactive: metadata,
            selectors,
        });
    }

    stateReady() {
        this.addEventListener(
            this.getElement(this.selectors.DELETE),
            'click',
            this.delete
        );
        const addButton = this.getElement(this.selectors.ADD);
        if (addButton) {
            this.addEventListener(
                addButton,
                'click',
                this.addItem
            );
        }
    }

    delete(event) {
        const parts = event.target.id.split('_');
        this.reactive.dispatch('deleteRow', parts[1], parts[2]);
    }

    addItem(event) {
        const parts = event.target.id.split('_');
        this.reactive.dispatch('addItem', parts[1], parts[2]);
    }
}