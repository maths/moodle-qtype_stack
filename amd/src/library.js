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

    let courseId = null;
    let categoryId = null;
    let libraryDiv = null;
    let rawDiv = null;
    let variablesDiv = null;
    let descriptionDiv = null;
    let importListDiv = null;
    let importSuccessDiv = null;
    let importFailureDiv = null;
    let importSuccessFileDiv = null;
    let importFailureFileDiv = null;
    let displayedDiv = null;
    let quizLink = null;
    let dashLink = null;
    let errorDiv = null;
    let errorDetailsDiv = null;
    let currentPath = null;

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
        errorDetailsDiv = document.querySelector('.stack-library-error-details');
        importSuccessDiv = document.querySelector('.stack-library-import-success');
        importFailureDiv = document.querySelector('.stack-library-import-failure');
        importSuccessFileDiv = document.querySelector('.stack-library-import-success-file');
        importFailureFileDiv = document.querySelector('.stack-library-import-failure-file');
        dashLink = document.querySelector('#dashboard-link-holder').innerHTML.trim();
        quizLink = document.querySelector('#quiz-link-holder').innerHTML.trim();
        dashLink = dashLink.includes('?') ? dashLink = dashLink + '&questionid=' : dashLink = dashLink + '?questionid=';
        loading(true);
        const linksArray = document.querySelectorAll('.library-file-link');
        linksArray.forEach(function(elem) {
            elem.addEventListener('click', libraryRender);
        });
        courseId = document.querySelector('[data-id="stack_library_course_id"]').getAttribute('data-value');
        const importButton = document.querySelector('.library-import-link');
        importButton.addEventListener('click', ()=>libraryImport(false));
        const importFolderButton = document.querySelector('.library-import-link-folder');
        importFolderButton.addEventListener('click', ()=>libraryImport(true));
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
                displayedDiv.innerHTML = response.questionname + '<br>(' + filepath.split('/').pop() + ')';
                document.querySelectorAll('.library-secondary-info')
                    .forEach(el => el.removeAttribute('hidden'));
                document.querySelector('.library-import-link').removeAttribute('disabled');
                if (filepath.endsWith('_quiz.json')) {
                    document.querySelector('.stack-library-category-holder').setAttribute('hidden', true);
                    document.querySelector('.stack-library-course').removeAttribute('hidden');
                } else {
                    document.querySelector('.stack-library-course').setAttribute('hidden', true);
                    document.querySelector('.stack-library-category-holder').removeAttribute('hidden');
                    document.querySelector('.library-import-link-folder').removeAttribute('disabled');
                }
                // This fires the Maths filters for content in the validation div.
                CustomEvents.notifyFilterContentUpdated(libraryDiv);
            },
            fail: function(response) {
                loading(false);
                errorDetailsDiv.innerHTML = (response.message) ? response.message : '';
                errorDiv.hidden = false;
            }
        }]);
    }

    /**
     * Performs AJAX call to Moodle to import a question.
     *
     * @param {boolean} isFolder is this a request to load the whole folder
     */
    function libraryImport(isFolder) {
        if (!currentPath) {
            return;
        }
        const filepath = currentPath;
        loading(true);
        categoryId = Number(document.getElementById('id_category').value.split(',')[0]);
        Ajax.call([{
            methodname: 'qtype_stack_library_import',
            args: {courseid: courseId, category: categoryId, filepath: filepath, isfolder: (isFolder) ? 1 : 0},
            done: function(response) {
                loading(false);
                for (const currentQuestion of response) {
                    if (currentQuestion.success) {
                        let currentDashLink = dashLink + currentQuestion.questionid;
                        if (currentQuestion.isstack) {
                            importListDiv.innerHTML += '<br>' + '<a target="_blank" href="'
                                + currentDashLink + '">' + currentQuestion.questionname + '</a>';
                        } else if (filepath.endsWith('_quiz.json')) {
                            importListDiv.innerHTML += '<br>' + '<a target="_blank" href="'
                                + quizLink + '?id=' + currentQuestion.questionid + '">'
                                + currentQuestion.questionname + '</a>';
                        } else {
                            importListDiv.innerHTML += '<br>' + currentQuestion.questionname;
                        }
                        importSuccessFileDiv.innerHTML += '<br>' +
                            currentQuestion.filename.split('/').pop() + ' --> ' + currentQuestion.questionname;
                        importSuccessDiv.removeAttribute('hidden');
                    } else {
                        importFailureFileDiv.innerHTML += '<br>' +
                            currentQuestion.filename.split('/').pop();
                        importFailureDiv.removeAttribute('hidden');
                    }
                }
            },
            fail: function(response) {
                loading(false);
                errorDetailsDiv.innerHTML = (response.message) ? response.message : '';
                errorDiv.hidden = false;
            }
        }]);
    }

    /**
     * Disable/enable features before/after loading.
     *
     * @param {boolean} isLoading Is an AJAX call taking place?
     */
    function loading(isLoading) {
        errorDiv.hidden = true;
        if (isLoading) {
            document.querySelector('.loading-display').removeAttribute('hidden');
            document.querySelector('.library-import-link').setAttribute('disabled', 'disabled');
            document.querySelector('.library-import-link-folder').setAttribute('disabled', 'disabled');
            document.querySelectorAll('.library-file-link').forEach(el => el.setAttribute('disabled', 'disabled'));
            importSuccessFileDiv.innerHTML = '';
            importSuccessDiv.setAttribute('hidden', true);
            importFailureFileDiv.innerHTML = '';
            importFailureDiv.setAttribute('hidden', true);
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
