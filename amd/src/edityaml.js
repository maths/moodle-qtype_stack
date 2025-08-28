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
 * A javascript module to allow editing of PRTs in YAML format.
 *
 * @module     qtype_stack/edityaml
 * @copyright  2025 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as yaml from 'qtype_stack/js-yaml-lazy';

export const setup = () => {
    let json = document.querySelector('#id_yamlinput').innerHTML;
    let obj = JSON.parse(json);
    let yamltext = yaml.dump(obj, { lineWidth: -1 });
    let obj2 = yaml.load(yamltext);
    let json2 = JSON.stringify(obj2);
    document.querySelector('#id_yamlinput').innerHTML = json + yamltext + json2;
    // Add button to convert YAML to filled boxes. Listeners.
    // Figure out about double call to server.

};
