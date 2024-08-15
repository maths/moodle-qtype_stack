var that = this;
var result = {

    componentInit: function() {
        // This.question should be provided to us here.
        // This.question.html (string) is the main source of data, presumably prepared by the renderer.
        // There are also other useful objects with question like infoHtml which is used by the
        // page to display the question state, but with which we need do nothing.
        // This code just prepares bits of this.question.html storing it in the question object ready for
        // passing to the template (stack.html).
        // Note this is written in 'standard' javascript rather than ES6. Both work.
        if (!this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }

        // Create a temporary div to ease extraction of parts of the provided html.
        var div = this.CoreDomUtilsProvider.convertToElement(this.question.html);
        div.innerHTML = this.question.html;

        // Replace Moodle's correct/incorrect classes, feedback and icons with mobile versions.
        that.CoreQuestionHelperProvider.replaceCorrectnessClasses(div);
        that.CoreQuestionHelperProvider.replaceFeedbackClasses(div);
        that.CoreQuestionHelperProvider.treatCorrectnessIcons(div);

        // Get useful parts of the provided question html data.
        var questiontext = div.querySelector('.content');
        const answers = questiontext.querySelectorAll('.answer');
        const dashLink = questiontext.querySelector('.questiontestslink');
        if (dashLink) {
            dashLink.parentNode.removeChild(dashLink);
        }
        var prompt = div.querySelector('.prompt');

        // Add the useful parts back into the question object ready for rendering in the template.
        this.question.text = questiontext.innerHTML;
        this.question.divId = div.querySelector('div').getAttribute('id');
        // Without the question text there is no point in proceeding.
        if (typeof this.question.text === 'undefined') {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }
        if (prompt !== null) {
            this.question.prompt = prompt.innerHTML;
        }
        var checkboxsets = [];

        answers.forEach(function(checkboxset, i) {
            var options = checkboxset.querySelectorAll('.option');
            const o = [];
            options.forEach(function(option) {
                // Each answer option contains all the data for presentation, it just needs extracting.
                var label = option.querySelector('label').innerHTML;
                var name = option.querySelector('label').getAttribute('for');
                var checked = (option.querySelector('input[type=checkbox]').getAttribute('checked') ? true : false);
                var disabled = (option.querySelector('input').getAttribute('disabled') === 'disabled' ? true : false);
                var qclass = option.getAttribute('class');
                var value = option.querySelector('input').getAttribute('value');
                o.push({text: label, name: name, checked: checked, disabled: disabled, qclass: qclass, value: value});
            });
            checkboxsets.push(o);
            checkboxset.replaceWith('~~!!~~Checkbox:' + i + '~~!!');
        });
        var questionHTML = questiontext.innerHTML;
        var sectionsHTML = questionHTML.split('~~!!');
        const sections = [];
        sectionsHTML.forEach(function(sectionHTML) {
            const section = {};
            if (!sectionHTML.startsWith('~~')) {
                section.type = 'Text';
                section.content = sectionHTML;
            } else {
                const sectionInfo = sectionHTML.split(':');
                switch (sectionInfo[0]) {
                    case ('~~Checkbox'):
                        section.type = 'Checkbox';
                        section.options = checkboxsets[Number(sectionInfo[1])];
                        break;
                }
            }
            sections.push(section);
        });
        this.question.sections = sections;
        const scripts = this.question.scriptsCode;
        const initCalls = scripts.match(/amd\.initInputs\(.*\]\);/g);
        const inputInits = [];
        for (let currentInit of initCalls) {
            currentInit = currentInit.slice(15, -2);
            const initArgs = JSON.parse('[' + currentInit + ']');
            inputInits.push(initArgs);
        }

        /**
         * Class constructor representing an input in a Stack question.
         *
         * @constructor
         * @param {HTMLElement} validationDiv The div to display the validation in.
         * @param {String} prefix prefix added to the input name to get HTML ids.
         * @param {String} qaid id of the question_attempt.
         * @param {String} name the name of the input we are validating.
         * @param {Object} input An object representing the input element for this input.
         * @param {String} language display language for this attempt.
         * @param {Set} validationsInProgress names of inputs being validated for this question.
         */
        function StackInput(validationDiv, prefix, qaid, name, input, language, validationsInProgress) {
            /** @type {number} delay between the user stopping typing, and the ajax request being sent. */
            var TYPING_DELAY = 1000;

            /** @type {?int} if not null, the id of the timer for the typing delay. */
            var delayTimeoutHandle = null;

            /** @type {Object} cache of validation results we have already received. */
            var validationResults = {};

            /** @type {String} the last value that we sent to be validated. */
            var lastValidatedValue = getInputValue();
            /** @type {HTMLElement} the 'Check' button for this question if it exists. */
            var checkButton = document.getElementById(prefix + '-submit');

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
                    if (checkButton) {
                        validationsInProgress.delete(name);
                        if (validationsInProgress.size === 0) {
                            checkButton.disabled = false;
                        }
                    }
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
                const args = {
                    qaid: qaid,
                    name: name,
                    input: getInputValue(),
                    lang: language
                };
                that.CoreSitesProvider.getCurrentSite().read('qtype_stack_validate_input', args)
                .then(function(response) {
                    validationReceived(response);
                })
                .catch(function(response) {
                    showValidationFailure(response);
                });
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
                const filters = that.CoreFilterDelegate.getEnabledFilters('system');
                that.CoreFilterDelegate.handleHtml(validationDiv, filters);
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
                const filters = that.CoreFilterDelegate.getEnabledFilters('system');
                that.CoreFilterDelegate.handleHtml(validationDiv, filters);
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
                if (checkButton) {
                    validationsInProgress.add(name);
                    checkButton.disabled = true;
                }
            }

            /**
             * Strip all our class names from the validation div.
             */
            function removeAllClasses() {
                validationDiv.classList.remove('empty');
                validationDiv.classList.remove('error');
                validationDiv.classList.remove('loading');
                validationDiv.classList.remove('waiting');
                if (checkButton) {
                    validationsInProgress.delete(name);
                    if (validationsInProgress.size === 0) {
                        checkButton.disabled = false;
                    }
                }
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
        function StackCheckboxInput(questionDivId, prefix, name) {
            /**
             * Add the event handler to call when the user input changes.
             *
             * @param {Function} valueChanging the callback to call when we detect a value change.
             */
            this.addEventHandlers = function(valueChanging) {
                // The input event fires on any change in value, even if pasted in or added by speech
                // recognition to dictate text. Change only fires after loosing focus.
                // Should also work on mobile.
                const inputs = document.querySelectorAll('#' + questionDivId + ' [name^="' + prefix + name + '_"]');
                for (const input of inputs) {
                    input.addEventListener('click', valueChanging);
                }
            };

            /**
             * Get the current value of this input.
             *
             * @return {String}.
             */
            this.getValue = function() {
                var selected = document.querySelectorAll('#' + questionDivId + ' ion-checkbox[name^="' + prefix + name + '_"]');
                var result = [];
                for (var i = 0; i < selected.length; i++) {
                    if (selected[i].checked) {
                        result.push(selected[i].value);
                    }
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
                return JSON.stringify(values);
            };
        }

        /**
         * Initialise all the inputs in a STACK question.
         *
         * @param {String} questionDivId id of the outer div of the question.
         * @param {String} prefix prefix added to the input names for this question.
         * @param {String} qaid Moodle question_attempt id.
         * @param {String[]} inputs names of all the inputs that should have instant validation.
         */
        function initInputs(questionDivId, prefix, qaid, inputs) {
            var questionDiv = document.getElementById(questionDivId);
            var validationsInProgress = new Set();
            var language = null;
            var langInput = document.getElementsByName(prefix + 'step_lang');
            if (langInput.length > 0 && langInput[0].value) {
                language = langInput[0].value;
            }

            // Initialise all inputs.
            var allok = true;
            for (var i = 0; i < inputs.length; i++) {
                allok = initInput(questionDivId, prefix, qaid, inputs[i], language, validationsInProgress) && allok;
            }

            // With JS With instant validation, we don't need the Check button, so hide it.
            if (allok && (questionDiv.classList.contains('dfexplicitvaildate') ||
                    questionDiv.classList.contains('dfcbmexplicitvaildate'))) {
                            questionDiv.querySelector('.im-controls input.submit, .im-controls button.submit').hidden = true;
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
         * @param {String} language display language for this attempt.
         * @param {Set} validationsInProgress names of inputs being validated for this question.
         */
        function initInput(questionDivId, prefix, qaid, name, language, validationsInProgress) {
            var validationDiv = document.getElementById(prefix + name + '_val');
            if (!validationDiv) {
                return false;
            }
            var inputTypeHandler = getInputTypeHandler(questionDivId, prefix, name);
            if (inputTypeHandler) {
                new StackInput(validationDiv, prefix, qaid, name, inputTypeHandler, language, validationsInProgress);
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
        function getInputTypeHandler(questionDivId, prefix, name) {
            // See if it is an ordinary input.
            var input = document.querySelector('#' + questionDivId + ' [name="' + prefix + name + '"]');
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
            input = document.querySelector('#' + questionDivId + ' [name="' + prefix + name + '_1"]');
            if (input) {
                return new StackCheckboxInput(questionDivId, prefix, name);
            }

            // See if it is a matrix input.
            var matrix = document.getElementById(prefix + name + '_container');
            if (matrix) {
                return new StackMatrixInput(prefix + name, matrix);
            }

            return null;
        }

        // Perform this code after DOM rendered.
        setTimeout(() => {
            // We can't proceed until we can access the question div but when
            // moving between pages of a quiz, the data is loaded dynamically
            // and even using afterRender triggers before the div is available.
            // Sigh... Add this observer to set up validation ASAP.
            const observer = new MutationObserver(() => {
                if (document.querySelector('#' + this.question.divId)) {
                    observer.disconnect();
                    for (const args of inputInits) {
                        initInputs(...args);
                    }
                }
            });

            observer.observe(document.body, {childList: true, subtree: true});
        });
        return true;
    }
};

// This next line is required as is (because of an eval step that puts this result object into the global scope).
result;
