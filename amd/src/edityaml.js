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
    const inputElement = document.querySelector('#id_yamlinput');
    if (!inputElement) {
        return;
    }
    if (!inputElement.value) {
        yamlRevert();
    } else {
        if (inputElement.value != yamlParse()) {
            changeCallback();
        } else {
            inputElement.addEventListener('input', changeCallback);
        }
    }
    document.querySelector('#id_stack-revert-yaml')?.addEventListener('click', yamlRevert);
    document.querySelector('#id_stack-convert-yaml')?.addEventListener('click', yamlConvert);
};

/**
 * Set up the change callback to warn about unsaved changes.
 */
function changeCallback() {
    const errorDiv = document.querySelector('#fgroup_id_error_yamlbuttons');
    if (errorDiv) {
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = getTranslation('yamlwarning');
    }
    document.querySelector('#id_yamlinput')?.removeEventListener('input', changeCallback);
}

/**
 * Set up the change callback to warn about unsaved changes.
 */
function changeRemove() {
    const errorDiv = document.querySelector('#fgroup_id_error_yamlbuttons');
    if (errorDiv) {
        errorDiv.style.display = 'none';
        errorDiv.innerHTML = '';
    }
    document.querySelector('#id_yamlinput')?.addEventListener('input', changeCallback);
}

/**
 * Copy original YAML of PRTs from hidden field to the text edit area.
 */
function yamlRevert() {
    const inputElement = document.querySelector('#id_yamlinput');
    if (!inputElement) {
        return;
    }
    let yamltext;
    try {
        yamltext = yamlParse();
    } catch (e) {
        yamltext = '';
    }
    inputElement.value = yamltext;
    changeRemove();
}

/**
 *
 * @returns {string} YAML representation of the PRTs.
 */
function yamlParse() {
    const json = document.querySelector('input[name="stack-yamloriginal"]')?.value;
    if (!json) {
        return '';
    }
    let prtsObject;
    try {
        prtsObject = JSON.parse(json);
    } catch (e) {
        return '';
    }

    Object.values(prtsObject).forEach(prt => {
        delete prt.name;
        delete prt.id;
        delete prt.questionid;
        delete prt.firstnodename;
        if (prt.nodes) {
            prt.nodes.forEach(node => {
                delete node.id;
                delete node.questionid;
                delete node.prtname;
            });
        }
    });
    try {
        return yaml.dump(prtsObject, { lineWidth: -1 });
    } catch (e) {
        return '';
    }
}

/**
 *
 * @param {*} key
 * @returns
 */
function getTranslation(key) {
    const transElement = document.querySelector('input[name="stack-yamltranslations"]');
    if (!transElement) {
        return '';
    }
    let json = transElement.value;
    if (!json) {
        return '';
    }
    try {
        json = JSON.parse(json);
    } catch (e) {
        return '';
    }
    return json[key] ?? '';
}

/**
 * Convert YAML in the text area to an object and put the values in the PRT fields.
 */
function yamlConvert() {
    const yamltext = document.querySelector('#id_yamlinput')?.value;
    const errorDiv = document.querySelector('#id_error_yamlinput');
    if (!errorDiv) {
        return;
    }
    errorDiv.innerHTML = '';
    errorDiv.style.display = 'none';
    let isError = false;

    let prtsObject;
    try {
        prtsObject = yaml.load(yamltext);
    } catch (e) {
        errorDiv.innerHTML += getTranslation('yamlerror') + ': ' + e.message + '<br>';
        errorDiv.style.display = 'block';
        return;
    }

    for (const prtkey in prtsObject) {
        if (!document.querySelector('#id_' + prtkey + 'prtheader')) {
            // No such PRT in the form.
            errorDiv.innerHTML += getTranslation('yamlprtwarning') + ' ' + prtkey + '<br>';
            errorDiv.style.display = 'block';
            isError = true;
            continue;
        }
        const currentPrt = prtsObject[prtkey];
        if (currentPrt.nodes ) {
            for (const currentNode of currentPrt.nodes) {
                // Would need to submit add node multiple times to sort this automatically.
                // We would still run into issues if the node naming in the YAML was dubious.
                const nodeKey = currentNode.nodename;
                if (!document.querySelector('#id_' + prtkey + 'description' + '_' + nodeKey)) {
                    errorDiv.innerHTML += getTranslation('yamlnodewarning') + ' ' + prtkey + ' - ' + nodeKey + '<br>';
                    errorDiv.style.display = 'block';
                    isError = true;
                }
            }
        }
    }
    if (isError) {
        return;
    }
    for (const prtkey in prtsObject) {
        const currentPrt = prtsObject[prtkey];
        if ('value' in currentPrt ?? document.querySelector('#id_' + prtkey + 'value')) {
            document.querySelector('#id_' + prtkey + 'value').value = currentPrt.value;
        }
        if ('autosimplify' in currentPrt && document.querySelector('#id_' + prtkey + 'autosimplify')) {
            document.querySelector('#id_' + prtkey + 'autosimplify').value = currentPrt.autosimplify;
        }
        if ('feedbackstyle' in currentPrt && document.querySelector('#id_' + prtkey + 'feedbackstyle')) {
            document.querySelector('#id_' + prtkey + 'feedbackstyle').value = currentPrt.feedbackstyle;
        }
        if ('feedbackvariables' in currentPrt && document.querySelector('#id_' + prtkey + 'feedbackvariables')) {
            document.querySelector('#id_' + prtkey + 'feedbackvariables').value = currentPrt.feedbackvariables;
        }
        if (!currentPrt.nodes) {
            continue;
        }
        for (const currentNode of currentPrt.nodes) {
            const nodeKey = currentNode.nodename;
            if ('description' in currentNode && document.querySelector('#id_' + prtkey + 'description' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'description' + '_' + nodeKey).value = currentNode.description;
            }
            if ('answertest' in currentNode && document.querySelector('#id_' + prtkey + 'answertest' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'answertest' + '_' + nodeKey).value = currentNode.answertest;
            }
            if ('sans' in currentNode && document.querySelector('#id_' + prtkey + 'sans' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'sans' + '_' + nodeKey).value = currentNode.sans;
            }
            if ('tans' in currentNode && document.querySelector('#id_' + prtkey + 'tans' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'tans' + '_' + nodeKey).value = currentNode.tans;
            }
            if ('testoptions' in currentNode && document.querySelector('#id_' + prtkey + 'testoptions' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'testoptions' + '_' + nodeKey).value = currentNode.testoptions;
            }
            if ('quiet' in currentNode && document.querySelector('#id_' + prtkey + 'quiet' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'quiet' + '_' + nodeKey).value = currentNode.quiet;
            }
            if ('truescoremode' in currentNode && document.querySelector('#id_' + prtkey + 'truescoremode' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'truescoremode' + '_' + nodeKey).value = currentNode.truescoremode;
            }
            if ('truescore' in currentNode && document.querySelector('#id_' + prtkey + 'truescore' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'truescore' + '_' + nodeKey).value = currentNode.truescore;
            }
            if ('truepenalty' in currentNode && document.querySelector('#id_' + prtkey + 'truepenalty' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'truepenalty' + '_' + nodeKey).value = currentNode.truepenalty;
            }
            if ('truenextnode' in currentNode && document.querySelector('#id_' + prtkey + 'truenextnode' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'truenextnode' + '_' + nodeKey).value = currentNode.truenextnode;
            }
            if ('trueanswernote' in currentNode && document.querySelector('#id_' + prtkey + 'trueanswernote' + '_' + nodeKey)) {
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
            if ('falsescoremode' in currentNode && document.querySelector('#id_' + prtkey + 'falsescoremode' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'falsescoremode' + '_' + nodeKey).value = currentNode.falsescoremode;
            }
            if ('falsescore' in currentNode && document.querySelector('#id_' + prtkey + 'falsescore' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'falsescore' + '_' + nodeKey).value = currentNode.falsescore;
            }
            if ('falsepenalty' in currentNode && document.querySelector('#id_' + prtkey + 'falsepenalty' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'falsepenalty' + '_' + nodeKey).value = currentNode.falsepenalty;
            }
            if ('falsenextnode' in currentNode && document.querySelector('#id_' + prtkey + 'falsenextnode' + '_' + nodeKey)) {
                document.querySelector('#id_' + prtkey + 'falsenextnode' + '_' + nodeKey).value = currentNode.falsenextnode;
            }
            if ('falseanswernote' in currentNode && document.querySelector('#id_' + prtkey + 'falseanswernote' + '_' + nodeKey)) {
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
}
