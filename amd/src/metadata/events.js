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

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Javascript events for STACK metadata.
 *
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * Events for STACK metadata
 *
 * @constant
 * @property {String} qtypeStackStateUpdated See {@link event:qtypeStackStateUpdated}
 */
export const eventTypes = {
    /**
     * Event triggered when the activity reactive state is updated.
     *
     * @event qtypeStackStateUpdated
     * @type {CustomEvent}
     * @property {Array} nodes The list of parent nodes which were updated
     */
    qtypeStackStateUpdated: 'qtype_stack/stateUpdated',
};

/**
 * Trigger an event to indicate that the activity state is updated.
 *
 * @method qtypeStackStateUpdated
 * @param {object} detail the full state
 * @param {HTMLElement} container the custom event target (document if none provided)
 * @returns {CustomEvent}
 * @fires qtypeStackStateUpdated
 */
export const notifyQtypeStackStateUpdated = (detail, container) => {
    return dispatchEvent(eventTypes.qtypeStackStateUpdated, detail, container);
};