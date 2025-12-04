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
    updateAll(stateManager, inputArray) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        for (const field of inputArray) {
            const parts = field[0].split('_');
            const id = parts[1];
            const property = parts[2];
            const subproperty = parts[3];
            if (id != 0) {
                const existing = state[property].get(id);
                if (existing) {
                    existing[subproperty] = field[1];
                }
            } else {
                state[property][subproperty] = field[1];
            }
        }
        stateManager.setReadOnly(true);
    }
    deleteContributor(stateManager, id) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.contributor.delete(id);
        stateManager.setReadOnly(true);
    }

    addContributor(stateManager) {
        const state = stateManager.state;
        const keys = Array.from(state.contributor);
        keys.sort((a, b) => b[0] - a[0]);

        const newCon = {
            id: 1 + parseInt(keys[0][0]),
            firstName: "",
            lastName: "",
            institution: "",
            year: 2025
        };
        console.log(keys);
        stateManager.setReadOnly(false);
        state.contributor.add(newCon);
        stateManager.setReadOnly(true);
        console.log(state.contributor);
    }

    updateFromJson(stateManager, data) {
        const state = stateManager.state;
        data = JSON.parse(data);
        stateManager.setReadOnly(false);
        for (const prop in data) {
            state[prop] = data[prop];
        }
        stateManager.setReadOnly(true);
    }
}

export const mutations = new Mutations();