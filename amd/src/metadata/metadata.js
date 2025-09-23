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
 * Metadata entry reactive component
 *
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Reactive} from 'core/reactive';
import {mutations} from 'qtype_stack/metadata/mutations';
import {eventTypes, notifyQtypeStackStateUpdated} from 'qtype_stack/metadata/events';

const state = {
    'people': [
        {
            id: 1,
            name: 'Carlos',
            bitten: false,
        },
        {
            id: 2,
            name: 'Amaia',
            bitten: false,
        },
        {
            id: 3,
            name: 'Sara',
            bitten: false,
        },
        {
            id: 4,
            name: 'Ilya',
            bitten: true,
        },
        {
            id: 5,
            name: 'Ferran',
            bitten: false,
        },
    ],
};

class StackMetadata extends Reactive {
}

/**
 * The metadata state instance.
 */
export const metadata = new StackMetadata({
    name: 'qtype_stack_metadata',
    eventName: eventTypes.qtypeStackStateUpdated,
    eventDispatch: notifyQtypeStackStateUpdated,
    state,
    mutations,
});

/**
 * Load the initial state.
 */
export const init = () => {
    //state.metadata = JSON.parse(metadata);
};