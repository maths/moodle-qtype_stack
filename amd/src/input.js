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
 * A javascript module to handle the real-time validation of the input the student types
 * into STACK questions.
 *
 * The overall way this works is as follows:
 *
 *  - right at the end of this file are the init methods, which set things up.
 *  - The work common to all input types is done by StackInput.
 *     - Sending the Ajax request.
 *     - Updating the validation display.
 *  - The work specific to different input types (getting the content of the inputs) is done by
 *    the classes like
 *     - StackSimpleInput
 *     - StackTextareaInput
 *     - StackMatrixInput
 *    objects of these types need to implement the two methods addEventHandlers and getValue().
 *
 * @package    qtype_stack
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/ajax',
    'core/event'
], function(
    Ajax,
    CustomEvents
) {

    "use strict";

    /**
     * Class constructor representing an input in a Stack question.
     *
     * @constructor
     * @param {HTMLElement} validationDiv The div to display the validation in.
     * @param {String} prefix prefix added to the input name to get HTML ids.
     * @param {String} qaid id of the question_attempt.
     * @param {String} name the name of the input we are validating.
     * @param {Object} input An object representing the input element for this input.
     */
    function StackInput(validationDiv, prefix, qaid, name, input) {
        /** @type {number} delay between the user stopping typing, and the ajax request being sent. */
        var TYPING_DELAY = 1000;

        /** @type {?int} if not null, the id of the timer for the typing delay. */
        var delayTimeoutHandle = null;

        /** @type {Object} cache of validation results we have already received. */
        var validationResults = {};

        /** @type {String} the last value that we sent to be validated. */
        var lastValidatedValue = getInputValue();

        /**
         * Cancel any typing pause timer.
         */
        function cancelTypingDelay() {
            if (delayTimeoutHandle) {
                clearTimeout(delayTimeoutHandle);
            }
            delayTimeoutHandle = null;
        }

        input.addEventHandlers(valueChanging);

        /**
         * Called when the input contents changes. Will validate after TYPING_DELAY if nothing else happens.
         */
        function valueChanging() {
            cancelTypingDelay();
            showWaiting();
            delayTimeoutHandle = setTimeout(valueChanged, TYPING_DELAY);
            setTimeout(function() {
                checkNoChange();
            }, 0);
        }

        /**
         * After a small delay, detect the case where the user has got the input back
         * to where they started, so no validation is necessary.
         */
        function checkNoChange() {
            if (getInputValue() === lastValidatedValue) {
                cancelTypingDelay();
                validationDiv.classList.remove('waiting');
            }
        }

        /**
         * Called to actually validate the input now.
         */
        function valueChanged() {
            cancelTypingDelay();
            if (!showValidationResults()) {
                validateInput();
            }
        }

        /**
         * Make an ajax call to validate the input.
         */
        function validateInput() {
            Ajax.call([{
                methodname: 'qtype_stack_validate_input',
                args: {qaid: qaid, name: name, input: getInputValue()},
                done: function(response) {
                    validationReceived(response);
                },
                fail: function(response) {
                    showValidationFailure(response);
                }
            }]);
            showLoading();
        }

        /**
         * Returns the current value of the input.
         *
         * @return {String}.
         */
        function getInputValue() {
            return input.getValue();
        }

        /**
         * Update the validation div to show the results of the validation.
         *
         * @param {Object} response The data that came back from the ajax validation call.
         */
        function validationReceived(response) {
            if (response.status === 'invalid') {
                showValidationFailure(response);
                return;
            }
            validationResults[response.input] = response;
            showValidationResults();
        }

        /**
         * Some browsers cannot execute JavaScript just by inserting script tags.
         * To avoid that problem, remove all script tags from the given content,
         * and run them later.
         *
         * @param {String} html HTML content
         * @param {Array} scriptCommands An array of script tags for later use.
         * @return {String} HTML with JS removed
         */
        function extractScripts(html, scriptCommands) {
            var scriptregexp = /<script[^>]*>([\s\S]*?)<\/script>/g;
            var result;
            while ((result = scriptregexp.exec(html)) !== null) {
                scriptCommands.push(result[1]);
            }
            return html.replace(scriptregexp, '');
        }

        /**
         * Update the validation div to show the results of the validation.
         *
         * @return {boolean} true if we could show the validation. false we we are we don't have it.
         */
        function showValidationResults() {
            /* eslint no-eval: "off" */
            var val = getInputValue();
            if (!validationResults[val]) {
                showWaiting();
                return false;
            }
            var results = validationResults[val];
            lastValidatedValue = val;
            var scriptCommands = [];
            validationDiv.innerHTML = extractScripts(results.message, scriptCommands);
            // Run script commands.
            for (var i = 0; i < scriptCommands.length; i++) {
                eval(scriptCommands[i]);
            }
            removeAllClasses();
            if (!results.message) {
                validationDiv.classList.add('empty');
            }
            // This fires the Maths filters for content in the validation div.
            CustomEvents.notifyFilterContentUpdated(validationDiv);
            return true;
        }

        /**
         * Update the validation div after an ajax validation call failed.
         *
         * @param {Object} response The data that came back from the ajax validation call.
         */
        function showValidationFailure(response) {
            lastValidatedValue = '';
            // Reponse usually contains backtrace, debuginfo, errorcode, link, message and moreinfourl.
            validationDiv.innerHTML = response.message;
            removeAllClasses();
            validationDiv.classList.add('error');
            // This fires the Maths filters for content in the validation div.
            CustomEvents.notifyFilterContentUpdated(validationDiv);
        }

        /**
         * Display the loader icon.
         */
        function showLoading() {
            removeAllClasses();
            validationDiv.classList.add('loading');
        }

        /**
         * Update the validation div to show that the input contents have changed,
         * so the validation results are no longer relevant.
         */
        function showWaiting() {
            removeAllClasses();
            validationDiv.classList.add('waiting');
        }

        /**
         * Strip all our class names from the validation div.
         */
        function removeAllClasses() {
            validationDiv.classList.remove('empty');
            validationDiv.classList.remove('error');
            validationDiv.classList.remove('loading');
            validationDiv.classList.remove('waiting');
        }
    }

    /**
     * Input type for inputs that are a single input or select.
     *
     * @constructor
     * @param {HTMLElement} input the HTML input that is this STACK input.
     */
    function StackSimpleInput(input) {
        /**
         * Add the event handler to call when the user input changes.
         *
         * @param {Function} valueChanging the callback to call when we detect a value change.
         */
        this.addEventHandlers = function(valueChanging) {
            // The input event fires on any change in value, even if pasted in or added by speech
            // recognition to dictate text. Change only fires after loosing focus.
            // Should also work on mobile.
            input.addEventListener('input', valueChanging);
        };

        /**
         * Get the current value of this input.
         *
         * @return {String}.
         */
        this.getValue = function() {
            return input.value.replace(/^\s+|\s+$/g, '');
        };
    }

    /**
     * Input type for textarea inputs.
     *
     * @constructor
     * @param {Object} textarea The input element wrapped in jquery.
     */
    function StackTextareaInput(textarea) {
        /**
         * Add the event handler to call when the user input changes.
         *
         * @param {Function} valueChanging the callback to call when we detect a value change.
         */
        this.addEventHandlers = function(valueChanging) {
            textarea.addEventListener('input', valueChanging);
        };

        /**
         * Get the current value of this input.
         *
         * @return {String}.
         */
        this.getValue = function() {
            var raw = textarea.value.replace(/^\s+|\s+$/g, '');
            // Using <br> here is weird, but it gets sorted out at the PHP end.
            return raw.split(/\s*[\r\n]\s*/).join('<br>');
        };
    }

    /**
     * Input type for inputs that are a set of radio buttons.
     *
     * @constructor
     * @param {HTMLElement} container container <div> of this input.
     */
    function StackRadioInput(container) {
        /**
         * Add the event handler to call when the user input changes.
         *
         * @param {Function} valueChanging the callback to call when we detect a value change.
         */
        this.addEventHandlers = function(valueChanging) {
            // The input event fires on any change in value, even if pasted in or added by speech
            // recognition to dictate text. Change only fires after loosing focus.
            // Should also work on mobile.
            container.addEventListener('input', valueChanging);
        };

        /**
         * Get the current value of this input.
         *
         * @return {String}.
         */
        this.getValue = function() {
            var selected = container.querySelector(':checked');
            if (selected) {
                return selected.value;
            } else {
                return '';
            }
        };
    }

    /**
     * Input type for inputs that are a set of checkboxes.
     *
     * @constructor
     * @param {HTMLElement} container container <div> of this input.
     */
    function StackCheckboxInput(container) {
        /**
         * Add the event handler to call when the user input changes.
         *
         * @param {Function} valueChanging the callback to call when we detect a value change.
         */
        this.addEventHandlers = function(valueChanging) {
            // The input event fires on any change in value, even if pasted in or added by speech
            // recognition to dictate text. Change only fires after loosing focus.
            // Should also work on mobile.
            container.addEventListener('input', valueChanging);
        };

        /**
         * Get the current value of this input.
         *
         * @return {String}.
         */
        this.getValue = function() {
            var selected = container.querySelectorAll(':checked');
            var result = [];
            for (var i = 0; i < selected.length; i++) {
                result[i] = selected[i].value;
            }
            if (result.length > 0) {
                return result.join(',');
            } else {
                return '';
            }
        };
    }

    /**
     * Class constructor representing matrix inputs (one input).
     *
     * @constructor
     * @param {String} idPrefix input id, which is the start of the id of all the different text boxes.
     * @param {HTMLElement} container <div> of this input.
     */
    function StackMatrixInput(idPrefix, container) {
        var numcol = 0;
        var numrow = 0;
        container.querySelectorAll('input[type=text]').forEach(function(element) {
            if (element.name.slice(0, idPrefix.length + 5) !== idPrefix + '_sub_') {
                return;
            }
            var bits = element.name.substring(idPrefix.length + 5).split('_');
            numrow = Math.max(numrow, parseInt(bits[0], 10) + 1);
            numcol = Math.max(numcol, parseInt(bits[1], 10) + 1);
        });

        /**
         * Add the event handler to call when the user input changes.
         *
         * @param {Function} valueChanging the callback to call when we detect a value change.
         */
        this.addEventHandlers = function(valueChanging) {
            container.addEventListener('input', valueChanging);
        };

        /**
         * Get the current value of this input.
         *
         * @return {String}.
         */
        this.getValue = function() {
            var values = new Array(numrow);
            for (var i = 0; i < numrow; i++) {
                values[i] = new Array(numcol);
            }
	     container.querySelectorAll('input[type=text]').forEach(function(element) {
                if (element.name.slice(0, idPrefix.length + 5) !== idPrefix + '_sub_') {
                    return;
                }
                var bits = element.name.substring(idPrefix.length + 5).split('_');
                values[bits[0]][bits[1]] = element.value.replace(/^\s+|\s+$/g, '');
            });
            return 'matrix([' + values.join('],[') + '])';
        };
    };

    /**
     * Initialise all the inputs in a STACK question.
     *
     * @param {String} questionDivId id of the outer dic of the question.
     * @param {String} prefix prefix added to the input names for this question.
     * @param {String} qaid Moodle question_attempt id.
     * @param {String[]} inputs names of all the inputs that should have instant validation.
     */
    function initInputs(questionDivId, prefix, qaid, inputs) {
        var questionDiv = document.getElementById(questionDivId);

        // Initialise all inputs.
        var allok = true;
        for (var i = 0; i < inputs.length; i++) {
            allok = initInput(questionDiv, prefix, qaid, inputs[i]) && allok;
        }

        // With JS With instant validation, we don't need the Check button, so hide it.
        if (allok && (questionDiv.classList.contains('dfexplicitvaildate') ||
                questionDiv.classList.contains('dfcbmexplicitvaildate'))) {
            questionDiv.querySelector('.im-controls input.submit').hidden = true;
        }
    }

    /**
     * Initialise one input.
     *
     * @param {HTMLElement} questionDiv outer <div> of this question.
     * @param {String} prefix prefix added to the input names for this question.
     * @param {String} qaid Moodle question_attempt id.
     * @param {String} name the input to initialise.
     * @return {boolean} true if this input was successfully initialised, else false.
     */
    function initInput(questionDiv, prefix, qaid, name) {
        var validationDiv = document.getElementById(prefix + name + '_val');
        if (!validationDiv) {
            return false;
        }

        var inputTypeHandler = getInputTypeHandler(questionDiv, prefix, name);
        if (inputTypeHandler) {
            new StackInput(validationDiv, prefix, qaid, name, inputTypeHandler);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the input type handler for a named input.
     *
     * @param {HTMLElement} questionDiv outer <div> of this question.
     * @param {String} prefix prefix added to the input names for this question.
     * @param {String} name the input to initialise.
     * @return {?Object} the input hander, if we can handle it, else null.
     */
    function getInputTypeHandler(questionDiv, prefix, name) {
        // See if it is an ordinary input.
        var input = questionDiv.querySelector('[name="' + prefix + name + '"]');
        if (input) {
            if (input.nodeName === 'TEXTAREA') {
                return new StackTextareaInput(input);
            } else if (input.type === 'radio') {
                return new StackRadioInput(input.closest('.answer'));
            } else {
                return new StackSimpleInput(input);
            }
        }

        // See if it is a checkbox input.
        input = questionDiv.querySelector('[name="' + prefix + name + '_1"]');
        if (input && input.type === 'checkbox') {
            return new StackCheckboxInput(input.closest('.answer'));
        }

        // See if it is a matrix input.
        var matrix = document.getElementById(prefix + name + '_container');
        if (matrix) {
            return new StackMatrixInput(prefix + name, matrix);
        }

        return null;
    }

    /** Export our entry point. */
    return {
        /**
         * Initialise all the inputs in a STACK question.
         *
         * @param {String} questionDivId id of the outer dic of the question.
         * @param {String} prefix prefix added to the input names for this question.
         * @param {String} qaid Moodle question_attempt id.
         * @param {String[]} inputs names of all the inputs that should have instant validation.
         */
        initInputs: initInputs
    };
});
