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
 * @package    qtype_stack
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/event'], function($, ajax, coreevent) {

    "use strict";

    /**
     * Class constructor representing an input in a Stack question.
     *
     * @class StackInput
     * @constructor
     * @param {String} name The input name, for example ans1.
     * @param {Number} qaid The question-attempt id.
     * @param {Object} input An object representing the input element for this input.
     * @param {Object} validationDiv jQuery representation of the validation div.
     */
    var StackInput = function(name, qaid, input, validationDiv) {
        this.input = input;
        this.validationDiv = validationDiv;
        this.name = name;
        this.qaid = qaid;
        this.delayTimeoutHandle = null;
        this.input.addEventHanders(this);
        this.lastValidatedValue = this.getIntputValue();
        this.validationResults = {};
    };

    /**
     * @config TYPINGDELAY How long a pause in typing before we make an ajax validation request.
     */
    StackInput.prototype.TYPINGDELAY = 1000;

    /**
     * Cancel any typing pause timer.
     */
    StackInput.prototype.cancelTypingDelay = function() {
        if (this.delayTimeoutHandle) {
            clearTimeout(this.delayTimeoutHandle);
        }
        this.delayTimeoutHandle = null;
    };

    /**
     * Event handler that is fired when the input contents changes. Will do a
     * validation after TYPINGDELAY if nothing else happens.
     */
    StackInput.prototype.valueChanging = function() {
        this.cancelTypingDelay();
        var self = this;
        this.showWaiting();
        this.delayTimeoutHandle = setTimeout(function() {
            self.valueChanged();
        }, this.TYPINGDELAY);
        setTimeout(function() {
            self.checkNoChange();
        }, 0);
    };

    /**
     * After a small delay, detect the case where the user has got the input back
     * to where they started, so not validation is necessary.
     */
    StackInput.prototype.checkNoChange = function() {
        if (this.getIntputValue() === this.lastValidatedValue) {
            this.cancelTypingDelay();
            this.validationDiv.removeClass('waiting');
        }
    };

    /**
     * Event handler that is fired when the input contents should be validated immediately.
     */
    StackInput.prototype.valueChanged = function() {
        this.cancelTypingDelay();
        if (!this.showValidationResults()) {
            this.validateInput();
        }
    };

    /**
     * Make an ajax call to validate the input.
     */
    StackInput.prototype.validateInput = function() {
        var self = this;
        ajax.call([{
            methodname: 'qtype_stack_validate_input',
            args: {qaid: this.qaid, name: this.name, input: this.getIntputValue()},
            done: function(response) {
                self.validationReceived(response);
            },
            fail: function(response) {
                self.showValidationFailure(response);
            }
        }]);
        this.showLoading();
    };

    /**
     * Returns the current value of the input.
     *
     * @return {String}.
     */
    StackInput.prototype.getIntputValue = function() {
        return this.input.getValue();
    };

    /**
     * Update the validation div to show the results of the validation.
     *
     * @param {String} response The data that came back from the ajax validation call.
     */
    StackInput.prototype.validationReceived = function(response) {
        if (response.status === 'invalid') {
            this.showValidationFailure(response);
            return;
        }
        this.validationResults[response.input] = response;
        this.showValidationResults();
    };

    /**
     * Some browsers cannot execute JavaScript just by inserting script tags.
     * To avoid that problem, remove all script tags from the given content,
     * and run them later.
     *
     * @param {String} html HTML content
     * @param {String} scriptcommands An array of script tags for later use.
     * @return {String} HTML with JS removed
     */
    StackInput.prototype.extractScripts = function(html, scriptcommands) {
        var scriptregexp = /<script[^>]*>([\s\S]*?)<\/script>/g;
        var result;
        while ((result = scriptregexp.exec(html)) !== null) {
            scriptcommands.push(result[1]);
        }
        return html.replace(scriptregexp, '');
    };

    /**
     * Update the validation div to show the results of the validation.
     */
    StackInput.prototype.showValidationResults = function() {
        /*eslint no-eval: "off"*/
        var val = this.getIntputValue();
        if (!this.validationResults[val]) {
            this.showWaiting();
            return false;
        }
        var results = this.validationResults[val];
        this.lastValidatedValue = val;
        var scriptcommands = [];
        var html = this.extractScripts(results.message, scriptcommands);
        this.validationDiv.html(html);
        // Run script commands.
        for (var i = 0; i < scriptcommands.length; i++) {
            eval(scriptcommands[i]);
        }
        this.removeAllClasses();
        if (!results.message) {
            this.validationDiv.addClass('empty');
        }
        // This fires the Maths filters for content in the validation div.
        coreevent.notifyFilterContentUpdated(this.validationDiv[0]);
        return true;
    };

    /**
     * Update the validation div after an ajax validation call failed.
     *
     * @param {String} response The data that came back from the ajax validation call.
     */
    StackInput.prototype.showValidationFailure = function(response) {
        this.lastValidatedValue = '';
        // Reponse usually contains backtrace, debuginfo, errorcode, link, message and moreinfourl.
        this.validationDiv.html(response.message);
        this.removeAllClasses();
        this.validationDiv.addClass('error');
        // This fires the Maths filters for content in the validation div.
        coreevent.notifyFilterContentUpdated(this.validationDiv[0]);
    };

    /**
     * Display the loader icon.
     */
    StackInput.prototype.showLoading = function() {
        this.removeAllClasses();
        this.validationDiv.addClass('loading');
    };

    /**
     * Update the validation div to show that the input contents have changed,
     * so the validation results are no longer relevant.
     */
    StackInput.prototype.showWaiting = function() {
        this.removeAllClasses();
        this.validationDiv.addClass('waiting');
    };

    /**
     * Strip all our class names from the validation div.
     */
    StackInput.prototype.removeAllClasses = function() {
        this.validationDiv.removeClass('empty');
        this.validationDiv.removeClass('error');
        this.validationDiv.removeClass('loading');
        this.validationDiv.removeClass('waiting');
    };

    /**
     * Class constructor representing a simple input in a Stack question.
     *
     * @class StackSimpleInput
     * @constructor
     * @param {Object} input The input element wrapped in jquery.
     */
    var StackSimpleInput = function(input) {
        this.input = input;
    };

    /**
     * Add the event handler to call when the user input changes.
     *
     * @param {Object} validator A StackInput object
     */
    StackSimpleInput.prototype.addEventHanders = function(validator) {
        // The input event fires on any change in value, even if pasted in or added by speech
        // recognition to dictate text. Change only fires after loosing focus.
        // Should also work on mobile.
        this.input.on('input', null, null, validator.valueChanging.bind(validator));
    };

    /**
     * Get the current value of this input.
     *
     * @return {String}.
     */
    StackSimpleInput.prototype.getValue = function() {
        return this.input.val().replace(/^\s+|\s+$/g, '');
    };

    /**
     * Class constructor representing a textarea input.
     *
     * @class StackTextareaInput
     * @constructor
     * @param {Object} textarea The input element wrapped in jquery.
     */
    var StackTextareaInput = function(textarea) {
        this.textarea = textarea;
    };

    /**
     * Add the event handler to call when the user input changes.
     *
     * @param {Object} validator A StackInput object
     */
    StackTextareaInput.prototype.addEventHanders = function(validator) {
        this.textarea.on('input', null, null, validator.valueChanging.bind(validator));
    };

    /**
     * Get the current value of this input.
     *
     * @return {String}.
     */
    StackTextareaInput.prototype.getValue = function() {
        var raw = this.textarea.val().replace(/^\s+|\s+$/g, '');
        return raw.split(/\s*[\r\n]\s*/).join('<br>');
    };

    /**
     * Class constructor representing matrx inputs (one input).
     *
     * @class StackMatrixInput
     * @constructor
     * @param {String} idPrefix.
     * @param {Object} container jQuery object wrapping a matrix of inputs.
     */
    var StackMatrixInput = function(idPrefix, container) {
        this.container = container;
        this.idPrefix  = idPrefix;
        var numcol = 0;
        var numrow = 0;
        this.container.find('input[type=text]').each(function(i, element) {
            var name = $(element).attr('name');
            if (name.slice(0, idPrefix.length + 5) !== idPrefix + '_sub_') {
                return;
            }
            var bits = name.substring(idPrefix.length + 5).split('_');
            numrow = Math.max(numrow, parseInt(bits[0], 10) + 1);
            numcol = Math.max(numcol, parseInt(bits[1], 10) + 1);
        });
        this.numcol = numcol;
        this.numrow = numrow;
    };

    /**
     * Add the event handler to call when the user input changes.
     *
     * @param {Object} validator A StackInput object
     */
    StackMatrixInput.prototype.addEventHanders = function(validator) {
        this.container.delegate('input', 'input[type=text]', null, validator.valueChanging.bind(validator));
    };

    /**
     * Get the current value of this input.
     *
     * @return {String}.
     */
    StackMatrixInput.prototype.getValue = function() {
        var numcol = this.numcol;
        var numrow = this.numrow;
        var idPrefix = this.idPrefix;
        var values = new Array(numrow);
        for (var i = 0; i < numrow; i++) {
            values[i] = new Array(numcol);
        }
        this.container.find('input[type=text]').each(function(i, element) {
            var name = $(element).attr('name');
            if (name.slice(0, idPrefix.length + 5) !== idPrefix + '_sub_') {
                return;
            }
            var bits = name.substring(idPrefix.length + 5).split('_');
            values[bits[0]][bits[1]] = $(element).val().replace(/^\s+|\s+$/g, '');
        });
        return 'matrix([' + values.join('],[') + '])';
    };

    /**
     * The Stack question init return object.
     *
     * @alias qtype_stack/input
     */
    var t = {
        initInputs: function(inputs, qaid, prefix) {
            var allok = true;
            for (var i = 0; i < inputs.length; i++) {
                var name = inputs[i];
                allok = t.initInput(name, qaid, prefix) && allok;
            }
            var outerdiv = $('input[name="' + prefix + ':sequencecheck"]').parents('div.que.stack');
            if (allok && outerdiv && (outerdiv.hasClass('dfexplicitvaildate') || outerdiv.hasClass('dfcbmexplicitvaildate'))) {
                // With instant validation, we don't need the Check button, so hide it.
                var button = outerdiv.find('.im-controls input.submit');
                if (button.attr('id') === prefix + '-submit') {
                    button.hide();
                }
                t.initInput(inputs[i], qaid, prefix);
            }
        },

        initInput: function(name, qaid, prefix) {
            var valinput = $(document.getElementById(prefix + name + '_val')); // $('#' + prefix + name + '_val') does not work!
            if (!valinput) {
                return false;
            }
            // See if it is an ordinary input.
            var input = $(document.getElementById(prefix + name));
            if (input.length) {
                if (input[0].nodeName === 'TEXTAREA') {
                    new StackInput(name, qaid, new StackTextareaInput(input), valinput);
                } else {
                    // A new StackInput object is required.
                    new StackInput(name, qaid, new StackSimpleInput(input), valinput);
                }
                return true;
            }
            // See if it is a matrix input.
            var matrix = $(document.getElementById(prefix + name + '_container'));
            if (matrix.length) {
                new StackInput(name, qaid, new StackMatrixInput(prefix + name, matrix), valinput);
                return true;
            }
            // Give up.
            return false;
        }
    };
    return t;
});
