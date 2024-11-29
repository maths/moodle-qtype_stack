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
    let descriptionDiv = null;
    let importListDiv = null;
    let importSuccessDiv = null;
    let importSuccessFileDiv = null;
    let displayedDiv = null;
    let dashLink = null;
    let errorDiv = null;
    let currentPath = null;
    let currentName = null;
    let isstack = false;

    /**
     * Sets up event listeners.
     *
     */
    function setup() {
        libraryDiv = document.querySelector('.stack_library_display');
        rawDiv = document.querySelector('.stack_library_raw_display');
        variablesDiv = document.querySelector('.stack_library_variables_display');
        importListDiv = document.querySelector('.stack-library-imported-list');
        displayedDiv = document.querySelector('.stack_library_selected_question');
        descriptionDiv = document.querySelector('.stack_library_description_display');
        errorDiv = document.querySelector('.stack-library-error');
        importSuccessDiv = document.querySelector('.stack-library-import-success');
        importSuccessFileDiv = document.querySelector('.stack-library-import-success-file');
        dashLink = document.querySelector('#dashboard-link-holder').innerHTML.trim();
        dashLink = dashLink.includes('?') ? dashLink = dashLink + '&questionid=' : dashLink = dashLink + '?questionid=';
        loading(true);
        const linksArray = document.querySelectorAll('.library-file-link');
        linksArray.forEach(function(elem) {
            elem.addEventListener('click', libraryRender);
        });
        const importButton = document.querySelector('.library-import-link');
        importButton.addEventListener('click', libraryImport);
        // Remove number of questions from category dropdown as we're not
        // updating them and that will confuse users.
        const catOptions = document.querySelectorAll('#id_category option');
        for (let option of catOptions) {
            let optionText = option.text;
            const sections = optionText.split('(');
            if (sections.length > 1) {
                if (sections[0] || sections.length > 2) {
                    sections.pop();
                    option.text = sections.join('(');
                }
            }
        }
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
        categoryId = Number(document.getElementById('id_category').value.split(',')[0]);
        Ajax.call([{
            methodname: 'qtype_stack_library_render',
            args: {category: categoryId, filepath: filepath},
            done: function(response) {
                loading(false);
                libraryDiv.innerHTML = response.questionrender;
                for (const iframe of response.iframes) {
                    require(['qtype_stack/stackjsvle'],
                        function(stackjsvle,) {
                            stackjsvle.create_iframe(
                                iframe.iframeid,
                                iframe.content,
                                iframe.targetdivid,
                                iframe.title,
                                iframe.scrolling,
                                iframe.evil
                            );
                        });
                  }
                rawDiv.innerHTML = response.questiontext;
                descriptionDiv.innerHTML = response.questiondescription;
                variablesDiv.innerHTML = response.questionvariables.replace(/;/g, ";<br>");
                displayedDiv.innerHTML = response.questionname + '<br>(' + currentPath.split('/').pop() + ')';
                currentName = response.questionname;
                isstack = response.isstack;
                document.querySelectorAll('.library-secondary-info')
                    .forEach(el => el.removeAttribute('hidden'));
                document.querySelector('.library-import-link').removeAttribute('disabled');
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
        categoryId = Number(document.getElementById('id_category').value.split(',')[0]);
        Ajax.call([{
            methodname: 'qtype_stack_library_import',
            args: {category: categoryId, filepath: filepath},
            done: function(response) {
                loading(false);
                if (response.success) {
                    let currentDashLink = dashLink + response.questionid;
                    if (isstack) {
                        importListDiv.innerHTML = importListDiv.innerHTML
                            + '<br>' + '<a target="_blank" href="' + currentDashLink + '">' + currentName + '</a>';
                    } else {
                        importListDiv.innerHTML = importListDiv.innerHTML + '<br>' + currentName;
                    }
                    importSuccessFileDiv.innerHTML = currentPath.split('/').pop() + ' as ' + currentName;
                    importSuccessDiv.removeAttribute('hidden');
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
            importSuccessDiv.setAttribute('hidden', true);
        } else {
            document.querySelector('.loading-display').setAttribute('hidden', true);
            document.querySelectorAll('.library-file-link').forEach(el => el.removeAttribute('disabled'));
        }
    }

    /** Export our entry point. */
    return {
        setup: setup
    };
});
