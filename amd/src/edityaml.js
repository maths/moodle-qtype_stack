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
    if (!document.querySelector('#id_yamlinput').value) {
        yamlRevert();
    } else {
        if (document.querySelector('#id_yamlinput').value != yamlParse()) {
            changeCallback();
        } else {
            document.querySelector('#id_yamlinput').addEventListener('change', changeCallback);
        }
    }
    document.querySelector('#id_stack-revert-yaml').addEventListener('click', yamlRevert);
    document.querySelector('#id_stack-convert-yaml').addEventListener('click', yamlConvert);
};

/**
 * Set up the change callback to warn about unsaved changes.
 */
function changeCallback() {
    document.querySelector('#id_updatebutton').addEventListener('click', yamlChanged);
    document.querySelector('#id_submitbutton').addEventListener('click', yamlChanged);
    document.querySelector('#id_yamlinput').removeEventListener('change', changeCallback);
}

/**
 * Set up the change callback to warn about unsaved changes.
 */
function changeRemove() {
    document.querySelector('#id_updatebutton').removeEventListener('click', yamlChanged);
    document.querySelector('#id_submitbutton').removeEventListener('click', yamlChanged);
    document.querySelector('#id_yamlinput').addEventListener('change', changeCallback);
}

/**
 * Copy original YAML of PRTs from hidden field to the text edit area.
 */
function yamlRevert() {
    let yamltext = yamlParse();
    document.querySelector('#id_yamlinput').value = yamltext;
    changeRemove();
}

/**
 *
 * @returns {string} YAML representation of the PRTs.
 */
function yamlParse() {
    let json = document.querySelector('input[name="stack-yamloriginal"]').value;
    let prtsObject = JSON.parse(json);
    Object.values(prtsObject).forEach(prt => {
        delete prt.name;
        delete prt.id;
        delete prt.questionid;
        delete prt.firstnodename;
        prt.nodes.forEach(node => {
            delete node.id;
            delete node.questionid;
            delete node.prtname;
        });
    });

    return yaml.dump(prtsObject, { lineWidth: -1 });
}

/**
 *
 * @param {*} event
 */
function yamlChanged(event) {
    event.preventDefault();
    event.returnValue = '';
}

/**
 * Convert YAML in the text area to an object and put the values in the PRT fields.
 */
function yamlConvert() {
    let yamltext = document.querySelector('#id_yamlinput').value;
    let prtsObject = yaml.load(yamltext);
    const errorDiv = document.querySelector('#id_error_yamlinput');
    errorDiv.innerHTML = '';
    errorDiv.style.display = 'none';
    let isError = false;
    for (const prtkey in prtsObject) {
        if (!document.querySelector('#id_' + prtkey + 'prtheader')) {
            // No such PRT in the form.
            // How to translate this?
            errorDiv.innerHTML += 'No such PRT: ' + prtkey + '<br>';
            errorDiv.style.display = 'block';
            isError = true;
            continue;
        }
        const currentPrt = prtsObject[prtkey];
        for (const currentNode of currentPrt.nodes) {
            // Would need to submit add node multiple times to sort this automatically.
            // We would still run into issues if the node naming in the YAML was dubious.
            const nodeKey = currentNode.nodename;
            if (!document.querySelector('#id_' + prtkey + 'description' + '_' + nodeKey)) {
                errorDiv.innerHTML += 'No such node: PRT: ' + prtkey + ' Node: ' + nodeKey + '<br>';
                errorDiv.style.display = 'block';
                isError = true;
            }
        }
    }
    if (isError) {
        return;
    }
    for (const prtkey in prtsObject) {
        const currentPrt = prtsObject[prtkey];
        if ('value' in currentPrt) {
            document.querySelector('#id_' + prtkey + 'value').value = currentPrt.value;
        }
        if ('autosimplify' in currentPrt) {
            document.querySelector('#id_' + prtkey + 'autosimplify').value = currentPrt.autosimplify;
        }
        if ('feedbackstyle' in currentPrt) {
            document.querySelector('#id_' + prtkey + 'feedbackstyle').value = currentPrt.feedbackstyle;
        }
        if ('feedbackvariables' in currentPrt) {
            document.querySelector('#id_' + prtkey + 'feedbackvariables').value = currentPrt.feedbackvariables;
        }
        for (const currentNode of currentPrt.nodes) {
            const nodeKey = currentNode.nodename;
            if ('description' in currentNode) {
                document.querySelector('#id_' + prtkey + 'description' + '_' + nodeKey).value = currentNode.description;
            }
            if ('answertest' in currentNode) {
                document.querySelector('#id_' + prtkey + 'answertest' + '_' + nodeKey).value = currentNode.answertest;
            }
            if ('sans' in currentNode) {
                document.querySelector('#id_' + prtkey + 'sans' + '_' + nodeKey).value = currentNode.sans;
            }
            if ('tans' in currentNode) {
                document.querySelector('#id_' + prtkey + 'tans' + '_' + nodeKey).value = currentNode.tans;
            }
            if ('testoptions' in currentNode) {
                document.querySelector('#id_' + prtkey + 'testoptions' + '_' + nodeKey).value = currentNode.testoptions;
            }
            if ('quiet' in currentNode) {
                document.querySelector('#id_' + prtkey + 'quiet' + '_' + nodeKey).value = currentNode.quiet;
            }
            if ('truescoremode' in currentNode) {
                document.querySelector('#id_' + prtkey + 'truescoremode' + '_' + nodeKey).value = currentNode.truescoremode;
            }
            if ('truescore' in currentNode) {
                document.querySelector('#id_' + prtkey + 'truescore' + '_' + nodeKey).value = currentNode.truescore;
            }
            if ('truepenalty' in currentNode) {
                document.querySelector('#id_' + prtkey + 'truepenalty' + '_' + nodeKey).value = currentNode.truepenalty;
            }
            if ('truenextnode' in currentNode) {
                document.querySelector('#id_' + prtkey + 'truenextnode' + '_' + nodeKey).value = currentNode.truenextnode;
            }
            if ('trueanswernote' in currentNode) {
                document.querySelector('#id_' + prtkey + 'trueanswernote' + '_' + nodeKey).value = currentNode.trueanswernote;
            }
            if ('truefeedback' in currentNode) {
                if (document.querySelector('#id_' + prtkey + 'truefeedback' + '_' + nodeKey + 'editable')) {
                    document.querySelector('#id_' + prtkey + 'truefeedback' + '_' + nodeKey + 'editable').value
                        = currentNode.truefeedback;
                } else if (document.querySelector('#id_' + prtkey + 'truefeedback' + '_' + nodeKey)) {
                    document.querySelector('#id_' + prtkey + 'truefeedback' + '_' + nodeKey).value
                        = currentNode.truefeedback;
                }
            }
            if ('truefeedbackformat' in currentNode
                    && document.querySelector('#id_' + prtkey + 'truefeedbackformat' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'truefeedbackformat' + '_' + nodeKey).value
                        = currentNode.truefeedbackformat;
            }
            if ('falsescoremode' in currentNode) {
                document.querySelector('#id_' + prtkey + 'falsescoremode' + '_' + nodeKey).value = currentNode.falsescoremode;
            }
            if ('falsescore' in currentNode) {
                document.querySelector('#id_' + prtkey + 'falsescore' + '_' + nodeKey).value = currentNode.falsescore;
            }
            if ('falsepenalty' in currentNode) {
                document.querySelector('#id_' + prtkey + 'falsepenalty' + '_' + nodeKey).value = currentNode.falsepenalty;
            }
            if ('falsenextnode' in currentNode) {
                document.querySelector('#id_' + prtkey + 'falsenextnode' + '_' + nodeKey).value = currentNode.falsenextnode;
            }
            if ('falseanswernote' in currentNode) {
                document.querySelector('#id_' + prtkey + 'falseanswernote' + '_' + nodeKey).value = currentNode.falseanswernote;
            }
            if ('falsefeedback' in currentNode) {
                if (document.querySelector('#id_' + prtkey + 'falsefeedback' + '_' + nodeKey + 'editable')) {
                    document.querySelector('#id_' + prtkey + 'falsefeedback' + '_' + nodeKey + 'editable').value
                        = currentNode.falsefeedback;
                } else if (document.querySelector('#id_' + prtkey + 'falsefeedback' + '_' + nodeKey)) {
                    document.querySelector('#id_' + prtkey + 'falsefeedback' + '_' + nodeKey).value
                        = currentNode.falsefeedback;
                }
            }
            if ('falsefeedbackformat' in currentNode
                    && document.querySelector('#id_' + prtkey + 'falsefeedbackformat' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'falsefeedbackformat' + '_' + nodeKey).value
                        = currentNode.falsefeedbackformat;
            }
        }
    }
    changeRemove();
}
