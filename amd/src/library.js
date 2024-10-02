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
 * A javascript module to handle requests for library question info
 * and to import questions.
 *
 * @module     qtype_stack/library
 * @copyright  2024 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/ajax',
    'core_filters/events'
], function(
    Ajax,
    CustomEvents
) {

    let categoryId = null;
    let libraryDiv = null;
    let rawDiv = null;
    let variablesDiv = null;
    let importListDiv = null;
    let displayedDiv = null;
    let errorDiv = null;
    let currentPath = null;

    /**
     * Sets up event listeners.
     *
     * @param {int} cId ID of question category that questions will be imported into.
     */
    function setup(cId) {
        categoryId = cId;
        libraryDiv = document.querySelector('.stack_library_display');
        rawDiv = document.querySelector('.stack_library_raw_display');
        variablesDiv = document.querySelector('.stack_library_variables_display');
        importListDiv = document.querySelector('.stack-library-imported-list');
        displayedDiv = document.querySelector('.stack_library_selected_question');
        errorDiv = document.querySelector('.stack-library-error');
        loading(true);
        const linksArray = document.querySelectorAll('.library-file-link');
        linksArray.forEach(function(elem) {
            elem.addEventListener('click', libraryRender);
        });
        const importButton = document.querySelector('.library-import-link');
        importButton.addEventListener('click', libraryImport);
        loading(false);
    }

    /**
     * Performs AJAX call to Moodle to get info on a question when
     * a link containing the questions filename is clicked.
     *
     * @param {object} e the click event triggering the function call.
     */
    function libraryRender(e) {
        const filepath = e.target.getAttribute('data-filepath');
        currentPath = filepath;
        loading(true);
        errorDiv.hidden = true;
        Ajax.call([{
            methodname: 'qtype_stack_library_render',
            args: {category: categoryId, filepath: filepath},
            done: function(response) {
                loading(false);
                libraryDiv.innerHTML = response.questionrender;
                rawDiv.innerHTML = response.questiontext;
                variablesDiv.innerHTML = response.questionvariables.replace(/;/g, ";<br>");
                displayedDiv.innerHTML = currentPath.split('/').pop();
                document.querySelectorAll('.library-secondary-info')
                    .forEach(el => el.removeAttribute('hidden'));
                // This fires the Maths filters for content in the validation div.
                CustomEvents.notifyFilterContentUpdated(libraryDiv);
            },
            fail: function() {
                loading(false);
                errorDiv.hidden = false;
            }
        }]);
    }

    /**
     * Performs AJAX call to Moodle to import a question.
     *
     */
    function libraryImport() {
        if (!currentPath) {
            return;
        }
        errorDiv.hidden = true;
        const filepath = currentPath;
        loading(true);
        Ajax.call([{
            methodname: 'qtype_stack_library_import',
            args: {category: categoryId, filepath: filepath},
            done: function(response) {
                loading(false);
                if (response.success) {
                    importListDiv.innerHTML = importListDiv.innerHTML + '<br>' + currentPath.split('/').pop();
                } else {
                    errorDiv.hidden = false;
                }
            },
            fail: function() {
                loading(false);
                errorDiv.hidden = false;
            }
        }]);
    }

    /**
     * Disable/enable features before/after loading.
     *
     * @param {*} isLoading Is an AJAX call taking place?
     */
    function loading(isLoading) {
        errorDiv.hidden = true;
        if (isLoading) {
            document.querySelector('.loading-display').removeAttribute('hidden');
            document.querySelector('.library-import-link').setAttribute('disabled', 'disabled');
            document.querySelectorAll('.library-file-link').forEach(el => el.setAttribute('disabled', 'disabled'));
        } else {
            document.querySelector('.loading-display').setAttribute('hidden', true);
            document.querySelector('.library-import-link').removeAttribute('disabled');
            document.querySelectorAll('.library-file-link').forEach(el => el.removeAttribute('disabled'));
        }
    }

    /** Export our entry point. */
    return {
        setup: setup
    };
});
