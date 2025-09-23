
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

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Javascript events for the `mod_nosferatu` activity.
 *
 * @module     mod_nosferatu/events
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 */

/**
 * Events for the `mod_nosferatu` activity.
 *
 * @constant
 * @property {String} modNosferatuStateUpdated See {@link event:modNosferatuStateUpdated}
 */
export const eventTypes = {
    /**
     * Event triggered when the activity reactive state is updated.
     *
     * @event modNosferatuStateUpdated
     * @type {CustomEvent}
     * @property {Array} nodes The list of parent nodes which were updated
     */
    qtypeStackStateUpdated: 'qtype_stack/stateUpdated',
};

/**
 * Trigger an event to indicate that the activity state is updated.
 *
 * @method notifyModNosferatuStateUpdated
 * @param {object} detail the full state
 * @param {HTMLElement} container the custom event target (document if none provided)
 * @returns {CustomEvent}
 * @fires modNosferatuStateUpdated
 */
export const notifyQtypeStackStateUpdated = (detail, container) => {
    return dispatchEvent(eventTypes.qtypeStackStateUpdated, detail, container);
};