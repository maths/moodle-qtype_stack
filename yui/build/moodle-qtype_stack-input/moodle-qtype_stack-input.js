YUI.add('moodle-qtype_stack-input', function (Y, NAME) {

// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This YUI module handles the real-time validation of the input the student types
 * into STACK questions.
 */

/**
 * Constructor.
 * @param name the input name, for example ans1.
 * @param qaid the question-attempt id.
 * @param input a YUI Node object for the input element for this input.
 */
var stack_input = function(name, qaid, input, validationdiv) {
    this.input         = input;
    this.validationdiv = validationdiv;
    this.name          = name;
    this.qaid          = qaid;

    this.input.add_event_handers(this);

    this.lastvalidatedvalue = this.get_intput_value();
    this.validationresults = {};
};

/** Configuration. How long a pause in typing before we make an ajax validation request. */
stack_input.prototype.TYPING_DELAY = 1000,

/** YUI Node for the input we are validating. */
stack_input.prototype.input = null,

/** String name of the input we are validating. */
stack_input.prototype.name = null,

stack_input.prototype.lastvalidatedvalue = '',

/** Int question_attempt id. */
stack_input.prototype.qaid = null,

/** YUI Node for the div where the validation is displayed. */
stack_input.prototype.validationdiv = null,

/** Object known inputs => corresponding validation. */
stack_input.prototype.validationresults = {},

/** The timeout handle for the typing pause timer. */
stack_input.prototype.delay_timeout_handle = null,

/**
 * Cancel any typing pause timer.
 */
stack_input.prototype.cancel_typing_delay = function() {
    if (this.delay_timeout_handle) {
        clearTimeout(this.delay_timeout_handle);
    }
    this.delay_timeout_handle = null;
};

/**
 * Event handler that is fired when the input contents changes. Will do a
 * validation after TYPING_DELAY if nothing else happens.
 * @param e event.
 */
stack_input.prototype.value_changing = function() {
    this.cancel_typing_delay();
    var self = this;
    this.show_waiting();
    this.delay_timeout_handle = setTimeout(function() {
        self.value_changed(null);
    }, this.TYPING_DELAY);
    setTimeout(function() {
        self.check_no_change();
    }, 0);
};

/**
 * After a small delay, detect the case where the user has got the input back
 * to where they started, so not vaildation is necessary.
 */
stack_input.prototype.check_no_change = function() {
    if (this.get_intput_value() === this.lastvalidatedvalue) {
        this.cancel_typing_delay();
        this.validationdiv.removeClass('waiting');
    }
};

/**
 * Event handler that is fired when the input contents should be validated immediately.
 * @param e event.
 */
stack_input.prototype.value_changed = function() {
    this.cancel_typing_delay();

    if (!this.show_validation_results()) {
        this.validate_input();
    }
};

/**
 * Actually make the ajax call to validate the input.
 */
stack_input.prototype.validate_input = function() {
    var self = this;
    Y.io(M.cfg.wwwroot + "/question/type/stack/stack/input/ajax.php",
            {
                data: {
                    qaid:  this.qaid,
                    name:  this.name,
                    input: this.get_intput_value()
                },
                on: {
                    success: function(id, rawresponse) {
                        self.validation_received(rawresponse);
                    },
                    failure: function(id, rawresponse) {
                        self.show_validation_failure(rawresponse);
                    }
                }
            });
    this.show_loading();
};

/**
 * @return String the current value of the input.
 */
stack_input.prototype.get_intput_value = function() {
    return this.input.get_value();
};

/**
 * Update the validation div to show the results of the validation.
 * @param e the data that came back from the ajax validation call.
 */
stack_input.prototype.validation_received = function(rawresponse) {
    var response = Y.JSON.parse(rawresponse.responseText);
    if (response.error) {
        this.show_validation_failure(rawresponse);
        return;
    }
    this.validationresults[response.input] = response;
    this.show_validation_results();
};

/**
 * Some browsers cannot execute JavaScript just by inserting script tags.
 * To avoid that problem, remove all script tags from the given content,
 * and run them later.
 * @param
 * @param html HTML content
 * @return new text with JS removed
 */
stack_input.prototype.extract_scripts = function(html, scriptcommands) {
    var scriptregexp = /<script[^>]*>([\s\S]*?)<\/script>/g;

    var result;
    while ((result = scriptregexp.exec(html)) !== null) {
        scriptcommands.push(result[1]);
    }

    return html.replace(scriptregexp, '');
};

/**
 * Update the validation div to show the results of the validation.
 * @param e the data that came back from the ajax validation call.
 */
stack_input.prototype.show_validation_results = function() {
    var val = this.get_intput_value();
    if (!this.validationresults[val]) {
        this.show_waiting();
        return false;
    }

    var results = this.validationresults[val];
    this.lastvalidatedvalue = val;

    var scriptcommands = [];
    var html = this.extract_scripts(results.message, scriptcommands);
    this.validationdiv.setContent(html);

    // Run script commands.
    for (var i = 0; i < scriptcommands.length; i++) {
        eval(scriptcommands[i]);
    }

    this.remove_all_classes();
    if (!results.message) {
        this.validationdiv.addClass('empty');
    }

    Y.fire(M.core.event.FILTER_CONTENT_UPDATED, {nodes: (new Y.NodeList(this.validationdiv))});

    return true;
};

/**
 * Update the validation div to show that the ajax validation call failed.
 * @param e the data that came back from the ajax validation call.
 */
stack_input.prototype.show_validation_failure = function(rawresponse) {
    var response = Y.JSON.parse(rawresponse.responseText);
    this.lastvalidatedvalue = '';
    this.validationdiv.setContent(response.error);
    this.remove_all_classes();
    this.validationdiv.addClass('error');
};

/**
 * Update the validation div to show that validation is happening.
 */
stack_input.prototype.show_loading = function() {
    this.remove_all_classes();
    this.validationdiv.addClass('loading');
};

/**
 * Update the validation div to show that the input contents have changed,
 * so the validation results are no longer relevant.
 */
stack_input.prototype.show_waiting = function() {
    this.remove_all_classes();
    this.validationdiv.addClass('waiting');
};

/**
 * Strip all our class names from the validation div.
 */
stack_input.prototype.remove_all_classes = function() {
    this.validationdiv.removeClass('empty');
    this.validationdiv.removeClass('error');
    this.validationdiv.removeClass('loading');
    this.validationdiv.removeClass('waiting');
};

/**
 * Constructor. Represents simple inputs (one input).
 * @param input a YUI Node object for the input element for this input.
 */
var stack_simple_input = function(input) {
    this.input = input;
};

/**
 * Add the event handlers to call the value when things change.
 * @param stack_input validator
 */
stack_simple_input.prototype.add_event_handers = function(validator) {
    this.input.on('valuechange', validator.value_changing, validator);
    this.input.on('change', validator.value_changing, validator);
};

/**
 * Get the current value of this input.
 * @return string.
 */
stack_simple_input.prototype.get_value = function() {
    return this.input.get('value').replace(/^\s+|\s+$/g, '');
};

/**
 * Constructor. Represents textarea input.
 * @param textarea a YUI Node object for the textarea element for this input.
 */
var stack_textarea_input = function(textarea) {
    this.textarea = textarea;
};

/**
 * Add the event handlers to call the value when things change.
 * @param stack_input validator
 */
stack_textarea_input.prototype.add_event_handers = function(validator) {
    this.textarea.on('valuechange', validator.value_changing, validator);
    this.textarea.on('change', validator.value_changing, validator);
};

/**
 * Get the current value of this input.
 * @return string.
 */
stack_textarea_input.prototype.get_value = function() {
    var raw = this.textarea.get('value').replace(/^\s+|\s+$/g, '');
    return '[' + raw.split(/\s*[\r\n]\s*/).join(',') + ']';
};

/**
 * Constructor. Represents simple inputs (one input).
 * Constructor.
 * @param name the input name, for example ans1.
 * @param qaid the question-attempt id.
 * @param input a YUI Node object for the input element for this input.
 */
var stack_matrix_input = function(idprefix, container) {
    this.container = container;
    this.idprefix  = idprefix;

    this.numcol = 0;
    this.numrow = 0;

    this.container.all('input[type=text]').each(function(e) {
        var name = e.get('name');
        if (name.slice(0, this.idprefix.length + 5) !== this.idprefix + '_sub_') {
            return;
        }
        var bits = name.substring(this.idprefix.length + 5).split('_');
        this.numrow = Math.max(this.numrow, parseInt(bits[0], 10) + 1);
        this.numcol = Math.max(this.numcol, parseInt(bits[1], 10) + 1);
    }, this);
};

/**
 * Add the event handlers to call the value when things change.
 * @param stack_input validator
 */
stack_matrix_input.prototype.add_event_handers = function(validator) {
    this.container.delegate('valuechange', validator.value_changing, 'input[type=text]', validator);
    this.container.delegate('change', validator.value_changing, 'input[type=text]', validator);
};

/**
 * Get the current value of this input.
 * @return string.
 */
stack_matrix_input.prototype.get_value = function() {
    var values = new Array(this.numrow);
    for (var i = 0; i < this.numrow; i++) {
        values[i] = new Array(this.numcol);
    }

    this.container.all('input[type=text]').each(function(e) {
        var name = e.get('name');
        if (name.slice(0, this.idprefix.length + 5) !== this.idprefix + '_sub_') {
            return;
        }
        var bits = name.substring(this.idprefix.length + 5).split('_');
        values[bits[0]][bits[1]] = e.get('value').replace(/^\s+|\s+$/g, '');
    }, this);

    return 'matrix([' + values.join('],[') + '])';
};

// Provide an external API.
M.qtype_stack = M.qtype_stack || {};
M.qtype_stack.init_inputs = function(inputs, qaid, prefix) {
    var allok = true;
    for (var i = 0; i < inputs.length; i++) {
        var name = inputs[i];

        allok = M.qtype_stack.init_input(name, qaid, prefix) && allok;
    }

    var outerdiv = Y.one('input[name="' + prefix + ':sequencecheck"]').ancestor('div.que.stack');
    if (allok && outerdiv && (outerdiv.hasClass('dfexplicitvaildate') || outerdiv.hasClass('dfcbmexplicitvaildate'))) {
        // With instant validation, we don't need the Check button, so hide it.
        var button = outerdiv.one('.im-controls input.submit');
        if (button.get('id') === prefix + '-submit') {
            button.hide();
        }
    }
};

M.qtype_stack.init_input = function(name, qaid, prefix) {
    var valinput = Y.one(document.getElementById(prefix + name + '_val'));
    if (!valinput) {
        return false;
    }

    // See if it is an ordinary input:
    var input = Y.one(document.getElementById(prefix + name));
    if (input) {
        if (input.get('tagName') === 'TEXTAREA') {
            new stack_input(name, qaid, new stack_textarea_input(input), valinput);
        } else {
            new stack_input(name, qaid, new stack_simple_input(input), valinput);
        }
        return true;
    }

    // See if it is a matrix input:
    var matrix = Y.one(document.getElementById(prefix + name + '_container'));
    if (matrix) {
        new stack_input(name, qaid, new stack_matrix_input(prefix + name, matrix), valinput);
        return true;
    }

    // Give up.
    return false;
};


}, '@VERSION@', {"requires": ["node", "event-valuechange", "moodle-core-event", "io", "json-parse"]});
