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

    var contextId = null;
    var libraryDiv = null;

    /**
     *
     * @param {?int} cId
     * @param {?string} lDiv
     */
    function setup(cId, lDiv) {
        contextId = cId;
        libraryDiv = document.getElementById(lDiv);
        let linksArray = document.querySelectorAll(".library-file-link");
        linksArray.forEach(function(elem) {
            elem.addEventListener("click", libraryRender);
        });
    }

    /**
     *
     * @param {*} filepath
     */
    function libraryRender(e) {
        const filepath = e.target.getAttribute('data-filepath');
        libraryDiv.classList.add('loading');
        Ajax.call([{
            methodname: 'qtype_stack_library_render',
            args: {context: contextId, filepath: filepath},
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
});
