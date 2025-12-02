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
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class Mutations {
    updateContributor(stateManager, id, firstName, lastName) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.contributor.get(id).firstName = firstName;
        state.contributor.get(id).lastName = lastName;
        stateManager.setReadOnly(true);
        console.log(state.contributor.get(id).firstName);
        console.log(state.contributor.get(id).lastName);
        console.log(state);
    }
}

export const mutations = new Mutations();