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
 * Default mutation manager
 *
 * @module     mod_nosferatu/local/beginner/mutations
 * @class     mod_nosferatu/local/beginner/mutations
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Mutations {
    /**
     * Bite a person.
     *
     * All mutations recive a StateManager object as a first parameter. Whith this object the mutation
     * can acces the state (stateManager.state) but also set the read mode (statemanager.setReadOnly(true|false)).
     * In next steps we will see some other stateManager features. But for now you don't need them.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {Number} personId the person id to bite
     */
    bite(stateManager, personId) {
        // The first thing we need to do is get the current state.
        const state = stateManager.state;
        // State is always on read mode. To change any value first we need to unlock it.
        stateManager.setReadOnly(false);
        // Now we do as many state changes as we need.
        state.people.get(personId).bitten = true;
        // All mutations should restore the read mode. This will trigger all the reactive events.
        stateManager.setReadOnly(true);
    }

    /**
     * The cureAll mutation.
     *
     * @param {StateManager} stateManager the current state manager
     */
    cureAll(stateManager) {
        // We call our hipotetical webservice.
        const result = this._callCureAll(stateManager.state);
        // And now we send the results to the stateManager.
        stateManager.processUpdates(result);
    }
    /**
     * Ok. we don't have a webservice yet, so we fake it.
     *
     * @param {object} state if this was a real webservice we probably won't need the full state.
     * @returns {array} the state updates object.
     */
    _callCureAll(state) {
        const result = [];
        state.people.forEach(person => {
            result.push({
                name: 'people',
                action: 'update',
                fields: {
                    ...person,
                    bitten: false,
                }
            });
        });
        return result;
    }
}

export const mutations = new Mutations();