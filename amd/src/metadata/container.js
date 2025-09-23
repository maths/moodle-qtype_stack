// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The beginner main app component.
 *
 * @module     mod_nosferatu/local/beginner
 * @class      mod_nosferatu/local/beginner
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {metadata} from 'qtype_stack/metadata/metadata';

export default class extends BaseComponent {

    /**
     * All the component definition should be initialized on the "create" method.
     */
    create() {
        // This is an optional name for the debugging messages.
        this.name = 'stack-metadata-app-container';
        this.selectors = {
            METADATACONTAINER: `[data-for='metadata-contrib']`,
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

    /**
     * Initial state ready method.
     *
     * Note in this case we want our stateReady to be async.
     *
     * @param {object} state the initial state
     */
    async stateReady(state) {
        this._reloadCityComponent({state});
    }

    async _reloadCityComponent({state}) {
        // Mustache data is not fully compatible with state object so we need to convert it
        // into a plain object. In the intermediate level you will learng how to centralize this
        // kind of operations to keep your components cleaner.
        const data = {
            people: [],
        };
        state.people.forEach(person => {
            data.people.push({...person});
        });
        data.haspeople = (data.people.length != 0);

        // To render a child component we need a container.
        const citiyContainer = this.getElement(this.selectors.METADATACONTAINER);
        if (!citiyContainer) {
            throw new Error('Missing city container.');
        }
        window.console.log(data);
        // We store the new content into an attribute in case we want to remove it in the future.
        this.cityComponent = await this.renderComponent(citiyContainer, 'qtype_stack/metadata/contributors', data);
    }
}