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
 * A javascript module to handle requests for library question info.
 *
 * @module     qtype_stack/input
 * @copyright  2018 The Open University
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
    let currentPath = null;

    /**
     *
     * @param {?int} cId
     * @param {?string} lDiv
     */
    function setup(cId) {
        categoryId = cId;
        libraryDiv = document.querySelector('.stack_library_display');
        rawDiv = document.querySelector('.stack_library_raw_display');
        variablesDiv = document.querySelector('.stack_library_variables_display');
        importListDiv = document.querySelector('.stack-library-imported-list');
        displayedDiv = document.querySelector('.stack_library_selected_question');
        const linksArray = document.querySelectorAll('.library-file-link');
        linksArray.forEach(function(elem) {
            elem.addEventListener('click', libraryRender);
        });
        const importButton = document.querySelector('.library-import-link');
        importButton.addEventListener('click', libraryImport);
    }

    /**
     *
     * @param {*} filepath
     */
    function libraryRender(e) {
        const filepath = e.target.getAttribute('data-filepath');
        currentPath = filepath;
        libraryDiv.classList.add('loading');
        Ajax.call([{
            methodname: 'qtype_stack_library_render',
            args: {category: categoryId, filepath: filepath},
            done: function(response) {
                libraryDiv.classList.remove('loading');
                showResults(response);
            },
            fail: function(response) {
                libraryDiv.classList.remove('loading');
                showFailure(response);
            }
        }]);
    }

    /**
     *
     * @param {*} response
     * @returns {boolean} true
     */
    function showResults(response) {
        libraryDiv.innerHTML = response.questionrender;
        rawDiv.innerHTML = response.questiontext;
        variablesDiv.innerHTML = response.questionvariables;
        displayedDiv.innerHTML = currentPath.split('/').pop();
        // This fires the Maths filters for content in the validation div.
        CustomEvents.notifyFilterContentUpdated(libraryDiv);
        return true;
    }

    /**
     *
     * @param {*} response
     */
    function showFailure(response) {
        libraryDiv.innerHTML = 'Something went wrong. ' + JSON.stringify(response);
    }

    /** Export our entry point. */
    return {
        setup: setup
    };

        /**
     *
     * @param {*} filepath
     */
        function libraryImport(e) {
            if (!currentPath) {
                return;
            }
            document.querySelector('.stack-library-error').hidden = true;
            const filepath = currentPath;
            libraryDiv.classList.add('loading');
            Ajax.call([{
                methodname: 'qtype_stack_library_import',
                args: {category: categoryId, filepath: filepath},
                done: function(response) {
                    if (response.success) {
                        importListDiv.innerHTML = importListDiv.innerHTML + '<br>' + currentPath.split('/').pop();
                    } else {
                        document.querySelector('.stack-library-error').hidden = false;
                    }
                },
                fail: function() {
                    document.querySelector('.stack-library-error').hidden = false;
                }
            }]);
        }
});
