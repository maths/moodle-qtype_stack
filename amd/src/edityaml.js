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
    yamlRevert();
    document.querySelector('#id_stack-revert-yaml').addEventListener('click', yamlRevert);
    document.querySelector('#id_stack-convert-yaml').addEventListener('click', yamlConvert);
};

/**
 * Copy original YAML of PRTs from hidden field to the text edit area.
 */
function yamlRevert() {
    let json = document.querySelector('input[name="stack-yamloriginal"]').value;
    let obj = JSON.parse(json);
    let yamltext = yaml.dump(obj, { lineWidth: -1 });
    document.querySelector('#id_yamlinput').value = yamltext;
}

/**
 * Convert YAML in the text area to an object and put the values in the PRT fields.
 */
function yamlConvert() {
    // Will need to submit verify question text, submit add node multiple times?
    let yamltext = document.querySelector('#id_yamlinput').value;
    let prtsObject = yaml.load(yamltext);
    const errorDiv = document.querySelector('#id_error_yamlinput');
    errorDiv.style.display = 'none';
    for (const prt in prtsObject) {
        if (!document.querySelector('#id_' + prt + 'prtheader')) {
            // No such PRT in the form.
            // How to translate this?
            errorDiv.innerHTML = 'No such PRT: ' + prt;
            errorDiv.style.display = 'block';
            break;
        }
        let currentPrt = prtsObject[prt];
        if ('value' in currentPrt) {
            document.querySelector('#id_' + prt + 'value').value = currentPrt.value;
        }
    }
}
