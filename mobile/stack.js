// Minified using uglifyjs stack.js > stack.min.js

var that = this;
var result = {

    componentInit: function() {
        // This.question should be provided to us here.
        // This.question.html (string) is the main source of data, presumably prepared by the renderer.
        // There are also other useful objects with question like infoHtml which is used by the
        // page to display the question state, but with which we need do nothing.
        // This code just prepares bits of this.question.html storing it in the question object ready for
        // passing to the template (stack.html).
        if (!this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }
        // Create a temporary div to ease extraction of parts of the provided html.
        const div = this.CoreDomUtilsProvider.convertToElement(this.question.html);
        div.innerHTML = this.question.html;
        // Replace Moodle's correct/incorrect classes, feedback and icons with mobile versions.
        that.CoreQuestionHelperProvider.replaceCorrectnessClasses(div);
        that.CoreQuestionHelperProvider.replaceFeedbackClasses(div);
        that.CoreQuestionHelperProvider.treatCorrectnessIcons(div);

        // Get useful parts of the provided question html data.
        let questiontext = div.querySelector('.content');
        const multiAnswers = questiontext.querySelectorAll('.answer');
        const checkboxAnswers = Array.from(multiAnswers).filter(item => !item.querySelector('[type="radio"]'));
        const radioAnswers = Array.from(multiAnswers).filter(item => item.querySelector('[type="radio"]'));
        const dropdowns = questiontext.querySelectorAll('select');
        const dashLink = questiontext.querySelector('.questiontestslink');
        const validationerror = questiontext.querySelector('.validationerror');
        const inputs = questiontext.querySelectorAll('input[type="text"]');
        inputs.forEach(function(input) {
            let width = input.style.width;
            if (width.endsWith('em')) {
                width = width.replace('em', '');
                width = width * 1.3;
                width = width + 'em';
                input.style.width = width;
            }
        });

        if (validationerror) {
            // Hide validation error as App will display.
            validationerror.setAttribute('hidden', true);
        }
        if (dashLink) {
            // Remove STACK dashboard links.
            dashLink.parentNode.removeChild(dashLink);
        }
        const prompt = div.querySelector('.prompt');

        this.question.divId = div.querySelector('div').getAttribute('id');
        // Without the question text there is no point in proceeding.
        if (typeof questiontext.innerHTML === 'undefined') {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }
        if (prompt !== null) {
            this.question.prompt = prompt.innerHTML;
        }

        checkboxAnswers.forEach(function(checkboxset) {
            const options = checkboxset.querySelectorAll('.option');
            const optionOutput = [];
            options.forEach(function(option) {
                // Each answer option contains all the data for presentation, it just needs extracting.
                const label = option.querySelector('label').innerHTML;
                const name = option.querySelector('label').getAttribute('for');
                const checked = (option.querySelector('input[type=checkbox]').getAttribute('checked') ? true : false);
                const disabled = (option.querySelector('input').getAttribute('disabled') === 'disabled' ? true : false);
                const qclass = option.getAttribute('class');
                const value = option.querySelector('input').getAttribute('value');
                optionOutput.push({text: label, name: name, checked: checked, disabled: disabled, qclass: qclass, value: value});
            });
            // I would love to be building this in a template and the structure of this code is
            // due to attempting to produce an object to feed to an ionic template. That way of doing
            // things, however, just wasn't feasible as there's not a set order for STACK questions.
            // Ultimately, this way was the only way for dropdown inputs to be inline and to not
            // break HTML structures around checkboxes, dropdowns and radios.
            let replacement = '<ion-item class="answer"><div>';
            for (let option of optionOutput) {
                replacement += '<div class="flex-column">';
                replacement += '<ion-checkbox checked="' + option.checked + '" value="' + option.value +
                                '" name="' + option.name + '" disabled="' + option.disabled + '">';
                replacement += '<div class="' + option.class + '">';
                replacement += option.text + '</div>';
                replacement += '</ion-checkbox></div>';
            }
            replacement += '</div></ion-item>';
            const template = document.createElement('div');
            template.innerHTML = replacement;
            let nativeSelectElement = template.querySelector('ion-item');
            checkboxset.replaceWith(nativeSelectElement);
        });

        radioAnswers.forEach(function(radioset) {
            let options = radioset.querySelectorAll('.option');
            const radioOutput = {};
            options = Array.from(options).filter(item => item.querySelector('input[type="radio"]'));
            const optionOutput = [];
            options.forEach(function(option) {
                // Each answer option contains all the data for presentation, it just needs extracting.
                const label = option.querySelector('label').innerHTML;
                const name = option.querySelector('input').getAttribute('name');
                const disabled = (option.querySelector('input').getAttribute('disabled') === 'disabled' ? true : false);
                const qclass = option.getAttribute('class');
                const value = option.querySelector('input').getAttribute('value');
                if (option.querySelector('input').getAttribute('checked') === 'checked') {
                    radioOutput.initialValue = value;
                }
                optionOutput.push({text: label, name: name, disabled: disabled, qclass: qclass, value: value});
            });
            radioOutput.name = optionOutput[0].name;
            if (!radioOutput.initialValue) {
                radioOutput.initialValue = '';
            }
            let replacement = '<ion-item class="answer"><div class="flex-column"><ion-radio-group name="' + radioOutput.name +
                '" value="' + radioOutput.initialValue + '">';
            for (let option of optionOutput) {
                replacement += '<ion-radio justify="start" label-placement="end" value="' + option.value + '" disabled="' +
                                option.disabled + '" name="' + option.name + '">';
                replacement += '<div class="' + option.class + '">' + option.text + '</div></ion-radio>';
            }
            replacement += '</ion-radio-group></div></ion-item>';
            const template = document.createElement('div');
            template.innerHTML = replacement;
            let nativeSelectElement = template.querySelector('ion-item');
            radioset.replaceWith(nativeSelectElement);
        });

        dropdowns.forEach(function(dropdown) {
            const options = dropdown.querySelectorAll('option');
            const dropdownOutput = {};
            const optionOutput = [];
            dropdownOutput.id = dropdown.getAttribute('id');
            dropdownOutput.name = dropdown.getAttribute('name');
            options.forEach(function(option) {
                const label = option.innerHTML;
                const disabled = (option.getAttribute('disabled') === 'disabled' ? true : false);
                const value = option.getAttribute('value');
                if (option.getAttribute('selected') === 'selected') {
                    dropdownOutput.initialValue = value;
                }
                optionOutput.push({text: label, disabled: disabled, value: value});
            });
            dropdownOutput.options = optionOutput;
            if (!dropdownOutput.initialValue) {
                dropdownOutput.initialValue = '';
            }
            let replacement = '<ion-select class="stack-ion-select" id="' + dropdownOutput.id +
                '" value="' + dropdownOutput.initialValue + '" name="' + dropdownOutput.name + '">';
            for (let option of options) {
                replacement += '<ion-select-option value="' + option.value + '" disabled="' + option.disabled + '">';
                replacement += option.text + '</ion-select-option>';
            }
            replacement += '</ion-select>';
            const template = document.createElement('div');
            template.innerHTML = replacement;
            let nativeSelectElement = template.querySelector('ion-select');
            dropdown.replaceWith(nativeSelectElement);
        });

        this.question.text = questiontext.innerHTML;

        // Extract input initialisation data from scriptsCode.
        const scripts = this.question.scriptsCode;
        let initCalls = scripts.match(/amd\.initInputs\(.*\]\);/g);
        initCalls = initCalls ? initCalls : [];
        const inputInits = [];
        for (let currentInit of initCalls) {
            currentInit = currentInit.slice(15, -2);
            const initArgs = JSON.parse('[' + currentInit + ']');
            inputInits.push(initArgs);
        }

        // Extract iframe initialisation data from scriptsCode.
        let iframes = scripts.match(/stackjsvle\.create_iframe\(.*;\}\);;/g);
        iframes = iframes ? iframes : [];
        const iframesArgs = [];
        let siteUrl = that.CoreSitesProvider.currentSite.siteUrl;
        for (let iframe of iframes) {
            iframe = iframe.slice(25, -6);
            // Final parameter is optional but defaults to false. We need to set it explicitly to avoid broken JSON.
            if (iframe.endsWith(',')) {
                iframe += 'false';
            }
            const args = JSON.parse('[' + iframe + ']');
            // Load included style sheet separately due to relative location issues.
            const baseRef = siteUrl + "/question/type/stack/corsscripts/cors.php?name=";
            args[1] = args[1].replace(baseRef + 'sortable.min.css" rel="stylesheet">',
                baseRef + 'sortable.min.css" rel="stylesheet">'
                + '<link rel="stylesheet" href="' + baseRef + 'styles.css"></link>');
            // Scripts are now being loaded cross origin and Chrome complains.
            // It may just be a dev environment issue.
            args[1] = args[1].replace(/<img/g, '<img crossorigin=\'anonymous\'');
            args[1] = args[1].replace(/<script/g, '<script crossorigin=\'anonymous\'');
            args[1] = args[1].replace(/<link/g, '<link crossorigin=\'anonymous\'');
            iframesArgs.push(args);
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
                    for (const args of iframesArgs) {
                        create_iframe(...args);
                    }
                    // Make sure all the MathJax has rendered properly.
                    if (document.querySelector('.' + 'MathJax_Error')) {
                        window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub]);
                    }
                }
            });

            observer.observe(document.body, {childList: true, subtree: true});
        });

        // Mostly a cut and paste of input.js file with updates for multianswer.
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
         * Input type for inputs that are a single input.
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
         * Input type for inputs that are a single select.
         *
         * @constructor
         * @param {HTMLElement} input the HTML input that is this STACK input.
         */
        function StackSelectInput(input) {
            /**
             * Add the event handler to call when the user input changes.
             *
             * @param {Function} valueChanging the callback to call when we detect a value change.
             */
            this.addEventHandlers = function(valueChanging) {
                // The input event fires on any change in value, even if pasted in or added by speech
                // recognition to dictate text. Change only fires after loosing focus.
                // Should also work on mobile.
                input.addEventListener('ionChange', valueChanging);
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
        function StackRadioInput(questionDivId, prefix, name) {
            /**
             * Add the event handler to call when the user input changes.
             *
             * @param {Function} valueChanging the callback to call when we detect a value change.
             */
            this.addEventHandlers = function(valueChanging) {
                // The input event fires on any change in value, even if pasted in or added by speech
                // recognition to dictate text. Change only fires after loosing focus.
                // Should also work on mobile.
                const inputs = document.querySelectorAll('#' + questionDivId + ' ion-radio-group[name^="' + prefix + name + '"]');
                for (const input of inputs) {
                    input.addEventListener('ionChange', valueChanging);
                }
            };

            /**
             * Get the current value of this input.
             *
             * @return {String}.
             */
            this.getValue = function() {
                var selected = document.querySelector('#' + questionDivId + ' ion-radio-group[name^="' + prefix + name + '"]');
                return selected.value;
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
                    const input = questionDiv.querySelector('.im-controls input.submit, .im-controls button.submit');
                    if (input) {
                        input.hidden = true;
                    }
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
                } else if (input.tagName === 'ION-RADIO-GROUP') {
                    return new StackRadioInput(questionDivId, prefix, name);
                } else if (input.tagName === 'ION-SELECT') {
                    return new StackSelectInput(input);
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


        // Mostly a cut and paste of stackjsvle.js file with updates for multianswer.
        // Note the VLE specific include of logic.

        /* All the IFRAMES have unique identifiers that they give in their
            * messages. But we only work with those that have been created by
            * our logic and are found from this map.
            */
        let IFRAMES = {};

        /* For event handling, lists of IFRAMES listening particular inputs.
            */
        let INPUTS = {};

        /* For event handling, lists of IFRAMES listening particular inputs
            * and their input events. By default we only listen to changes.
            * We report input events as changes to the other side.
            */
        let INPUTS_INPUT_EVENT = {};

        /* A flag to disable certain things. */
        let DISABLE_CHANGES = false;

        const abortController = new AbortController();


        /**
         * Returns an element with a given id, if an only if that element exists
         * inside a portion of DOM that represents a question or its feedback.
         *
         * If not found or exists outside the restricted area then returns `null`.
         *
         * @param {String} id the identifier of the element we want.
         * @returns {null}
         */
        function vle_get_element(id) {
            /* In the case of Moodle we are happy as long as the element is inside
                something with the `formulation`-class. */
            let candidate = document.getElementById(id);
            let iter = candidate;
            while (iter && !iter.classList.contains('formulation') &&
                    !iter.classList.contains('outcome')) {
                iter = iter.parentElement;
            }
            if (iter && (iter.classList.contains('formulation') ||
                iter.classList.contains('outcome'))) {
                return candidate;
            }

            return null;
        }

        /**
         * Returns an input element with a given name, if and only if that element
         * exists inside a portion of DOM that represents a question or its feedback.
         *
         * Note that, the input element may have a name that multiple questions
         * use and to pick the preferred element one needs to pick the one
         * within the same question as the IFRAME.
         *
         * Note that the input can also be a select. In the case of radio buttons
         * returning one of the possible buttons is enough.
         *
         * If not found or exists outside the restricted area then returns `null`.
         *
         * @param {String} name the name of the input we want
         * @param {String} srciframe the identifier of the iframe wanting it
         * @param {boolean} outside do we expand the search beyound the src question?
         * @returns {null}
         */
        // eslint-disable-next-line require-jsdoc, complexity
        function vle_get_input_element(name, srciframe, outside) {
            /* In the case of Moodle we are happy as long as the element is inside
                something with the `formulation`-class. */
            if (outside === undefined) {
                // Old default was to search beyoudn the question.
                outside = true;
            }
            let initialcandidate = document.getElementById(srciframe);
            let iter = initialcandidate;
            while (iter && !iter.classList.contains('formulation') &&
                    !iter.classList.contains('outcome')) {
                iter = iter.parentElement;
            }
            if (iter && (iter.classList.contains('formulation') ||
                iter.classList.contains('outcome'))) {
                // iter now represents the borders of the question containing
                // this IFRAME.
                let possible = iter.querySelector('input[id$="_' + name + '"]');
                if (possible !== null) {
                    return possible;
                }
                possible = iter.querySelector('textarea[id$="_' + name + '"]');
                if (possible !== null) {
                    return possible;
                }
                // Radios have interesting ids, but the name makes sense
                possible = iter.querySelector('ion-radio-group[name$="_' + name + '"]');
                if (possible !== null) {
                    return possible;
                }
                // Same for checkboxes, ntoe that non STACK checkbox can be targetted by
                // just the id using the topmost case here.
                possible = iter.querySelector('ion-checkbox[name$="_' + name + '_1"]');
                if (possible !== null) {
                    return possible;
                }
                possible = iter.querySelector('ion-select[name$="_' + name + '"]');
                if (possible !== null) {
                    return possible;
                }
            }
            if (!outside) {
                return null;
            }
            // If none found within the question itself, search everywhere.
            let possible = document.querySelector('.formulation input[id$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }
            possible = document.querySelector('.formulation textarea[id$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }
            // Radios have interesting ids, but the name makes sense
            possible = document.querySelector('.formulation ion-radio-group[name$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }
            possible = document.querySelector('.formulation ion-checkbox[name$="_' + name + '_1"]');
            if (possible !== null) {
                return possible;
            }
            possible = document.querySelector('.formulation ion-select[name$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }

            // Also search from within the feedback and other "outcome".
            // Note that we do not search for STACK sourced checkboxes from the feedback,
            // they do not exist there so simply finding them with the id is enough.
            possible = document.querySelector('.outcome input[id$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }
            possible = document.querySelector('.outcome textarea[id$="_' + name + '"]');
            if (possible !== null) {
                return possible;
            }
            possible = document.querySelector('.outcome ion-select[name$="_' + name + '"]');
            return possible;
        }

        /**
         * Returns a list of input elements targetting the same thing.
         *
         * Note that STACK checkboxes have interesting naming for this.
         * And we assume we are getting the ones that `vle_get_input_element` would return.
         *
         * @param {element} input element of type=radio or type=checkbox
         * @returns {querySelectorAll}
         */
        function vle_get_others_of_same_input_group(input) {
            if (input.type === 'radio') {
                return document.querySelectorAll('.formulation input[name=' + CSS.escape(input.name) + ']');
            }
            // Is it a Moodle input or a fake? If Moodle then assume STACK and its pattern.
            if (input.name.startsWith('q') && input.name.indexOf(':') > -1 && input.name.endsWith('_1')) {
                return document.querySelectorAll('.formulation input[name^=' +
                    CSS.escape(input.name.substring(0, input.name.length - 1)) + ']');
            }
            return document.querySelectorAll('.formulation input[name=' + CSS.escape(input.name) + ']');
        }


        /**
         * Returns the input element or null for a question level submit button.
         * Basically, the "Check" button that behaviours like adaptive-mode in Moodle have.
         * Not all questions have such buttons, and the behaviour will affect that.
         *
         * Will only return the button of the question containing that iframe.
         *
         * @param {String} srciframe the identifier of the iframe wanting it
         * @returns {null}
         */
        function vle_get_submit_button(srciframe) {
            let initialcandidate = document.getElementById(srciframe);
            let iter = initialcandidate;
            // Note the submit button is most definitely not within "outcome".
            while (iter && !iter.classList.contains('formulation')) {
                iter = iter.parentElement;
            }
            if (iter && iter.classList.contains('formulation')) {
                // iter now represents the borders of the question containing
                // this IFRAME.
                // In Moodle inputs that are behaviour variables use `-` as a separator
                // for the name and usage id.
                let possible = iter.querySelector('.im-controls *[id$="-submit"][type=submit]');
                return possible;
            }
            return null;
        }

        /**
         * Triggers any VLE specific scripting related to updates of the given
         * input element.
         *
         * @param {HTMLElement} inputelement the input element that has changed
         */
        function vle_update_input(inputelement) {
            // Triggering a change event may be necessary.
            const c = new Event('change');
            inputelement.dispatchEvent(c);
            // Also there are those that listen to input events.
            const i = new Event('input');
            inputelement.dispatchEvent(i);
            if (inputelement.type === 'radio' || inputelement.type === 'checkbox') {
                const k = new Event('click');
                inputelement.dispatchEvent(k);
            }
        }

        /**
         * Triggers any VLE specific scripting related to DOM updates.
         *
         * @param {HTMLElement} modifiedsubtreerootelement element under which changes may have happened.
         */
        function vle_update_dom(modifiedsubtreerootelement) {
            CustomEvents.notifyFilterContentUpdated(modifiedsubtreerootelement);
        }

        /**
         * Does HTML-string cleaning, i.e., removes any script payload. Returns
         * a DOM version of the given input string. The DOM version returned is
         * an element of some sort containing the contents, possibly a `body`.
         *
         * This is used when receiving replacement content for a div.
         *
         * @param {String} src a raw string to sanitise
         * @returns {element}
         */
        function vle_html_sanitize(src) {
            // This can be implemented with many libraries or by custom code
            // however as this is typically a thing that a VLE might already have
            // tools for we have it at this level so that the VLE can use its own
            // tools that do things that the VLE developpers consider safe.

            // As Moodle does not currently seem to have such a sanitizer in
            // the core libraries, here is one implementation that shows what we
            // are looking for.

            // TO-DO: look into replacing this with DOMPurify or some such.

            let parser = new DOMParser();
            let doc = parser.parseFromString(src, "text/html");

            // First remove all <script> tags. Also <style> as we do not want
            // to include too much style.
            for (let el of doc.querySelectorAll('script, style')) {
                el.remove();
            }

            // Check all elements for attributes.
            for (let el of doc.querySelectorAll('*')) {
                for (let {name, value} of el.attributes) {
                    if (is_evil_attribute(name, value)) {
                        el.removeAttribute(name);
                    }
                }
            }

            return doc.body;
        }

        /**
         * Utility function trying to determine if a given attribute is evil
         * when sanitizing HTML-fragments.
         *
         * @param {String} name the name of an attribute.
         * @param {String} value the value of an attribute.
         * @returns {boolean}
         */
        function is_evil_attribute(name, value) {
            const lcname = name.toLowerCase();
            if (lcname.startsWith('on')) {
                // We do not allow event listeners to be defined.
                return true;
            }
            if (lcname === 'src' || lcname.endsWith('href')) {
                // Do not allow certain things in the urls.
                const lcvalue = value.replace(/\s+/g, '').toLowerCase();
                // Ignore es-lint false positive.
                /* eslint-disable no-script-url */
                if (lcvalue.includes('javascript:') || lcvalue.includes('data:text')) {
                    return true;
                }
            }

            return false;
        }


        /** ***********************************************************************
         * Above this are the bits that one would probably tune when porting.
         *
         * Below is the actuall message handling and it should be left alone.
         */
        window.addEventListener("message", eventCallback, {signal: abortController.signal});

        // eslint-disable-next-line require-jsdoc, complexity
        function eventCallback(e) {
            // NOTE! We do not check the source or origin of the message in
            // the normal way. All actions that can bypass our filters to trigger
            // something are largely irrelevant and all traffic will be kept
            // "safe" as anyone could be listening.

            // All messages we receive are strings, anything else is for someone
            // else and will be ignored.
            if (!(typeof e.data === 'string' || e.data instanceof String)) {
                return;
            }

            // That string is a JSON encoded dictionary.
            let msg = null;
            try {
                msg = JSON.parse(e.data);
            } catch (e) {
                // Only JSON objects that are parseable will work.
                return;
            }

            if (!IFRAMES[msg.src] || !IFRAMES[msg.src].contentWindow) {
                // When we move between questions the old event listener lingers
                // and is triggered by iframes with the same name.
                // Remove it.
                abortController.abort();
                return;
            }

            // All messages we handle contain a version field with a particular
            // value, for now we leave the possibility open for that value to have
            // an actual version number suffix...
            if (!(('version' in msg) && msg.version.startsWith('STACK-JS'))) {
                return;
            }

            // All messages we handle must have a source and a type,
            // and that source must be one of the registered ones.
            if (!(('src' in msg) && ('type' in msg) && (msg.src in IFRAMES))) {
                return;
            }
            let element = null;
            let input = null;

            let response = {
                version: 'STACK-JS:1.3.0'
            };

            switch (msg.type) {
            case 'register-input-listener':
                // 1. Find the input.
                input = vle_get_input_element(msg.name, msg.src, !msg['limit-to-question']);

                if (input === null) {
                    // Requested something that is not available.
                    response.type = 'error';
                    response.msg = 'Failed to connect to input: "' + msg.name + '"';
                    response.tgt = msg.src;
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                    return;
                }

                response.type = 'initial-input';
                response.name = msg.name;
                response.tgt = msg.src;

                // 2. What type of an input is this? Note that we do not
                // currently support all types in sensible ways. In particular,
                // anything with multiple values will be a problem.
                if (input.tagName.toLowerCase() === 'ion-select') {
                    response.value = input.value;
                    response['input-type'] = 'select';
                    response['input-readonly'] = input.hasAttribute('disabled');
                } else if (input.nodeName.toLowerCase() === 'textarea') {
                    response.value = input.value;
                    response['input-type'] = 'textarea';
                    response['input-readonly'] = input.hasAttribute('disabled');
                } else if (input.tagName.toLowerCase() === 'ion-checkbox') {
                    response.value = input.checked;
                    response['input-type'] = 'checkbox';
                    response['input-readonly'] = input.hasAttribute('disabled');
                } else {
                    response.value = input.value;
                    response['input-type'] = input.type;
                    response['input-readonly'] = input.hasAttribute('readonly');
                }
                if (input.tagName.toLowerCase() === 'ion-radio-group') {
                    response['input-readonly'] = input.hasAttribute('disabled');
                    response.value = input.value;
                }

                // 3. Add listener for changes of this input.
                if (input.id in INPUTS) {
                    if (msg.src in INPUTS[input.id]) {
                        // DO NOT BIND TWICE!
                        return;
                    }
                    INPUTS[input.id].push(msg.src);
                } else {
                    INPUTS[input.id] = [msg.src];
                    input.addEventListener('change', () => {
                        if (DISABLE_CHANGES) {
                            return;
                        }
                        let resp = {
                            version: 'STACK-JS:1.0.0',
                            type: 'changed-input',
                            name: msg.name
                        };
                        if (input.tagName.toLowerCase() === 'ion-checkbox') {
                            resp['value'] = input.checked;
                        } else {
                            resp['value'] = input.value;
                        }
                        for (let tgt of INPUTS[input.id]) {
                            resp['tgt'] = tgt;
                            IFRAMES[tgt].contentWindow.postMessage(JSON.stringify(resp), '*');
                        }
                    });
                }

                if (('track-input' in msg) && msg['track-input']) {
                    if (input.id in INPUTS_INPUT_EVENT) {
                        if (msg.src in INPUTS_INPUT_EVENT[input.id]) {
                            // DO NOT BIND TWICE!
                            return;
                        }
                        INPUTS_INPUT_EVENT[input.id].push(msg.src);
                    } else {
                        INPUTS_INPUT_EVENT[input.id] = [msg.src];

                        input.addEventListener('input', () => {
                            if (DISABLE_CHANGES) {
                                return;
                            }
                            let resp = {
                                version: 'STACK-JS:1.0.0',
                                type: 'changed-input',
                                name: msg.name
                            };
                            if (input.tagName.toLowerCase() === 'ion-checkbox') {
                                resp['value'] = input.checked;
                            } else {
                                resp['value'] = input.value;
                            }
                            for (let tgt of INPUTS_INPUT_EVENT[input.id]) {
                                resp['tgt'] = tgt;
                                IFRAMES[tgt].contentWindow.postMessage(JSON.stringify(resp), '*');
                            }
                        });
                    }
                }

                // 4. Let the requester know that we have bound things
                //    and let it know the initial value.
                if (!(msg.src in INPUTS[input.id])) {
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                }

                break;
            case 'changed-input':
                // 1. Find the input.
                input = vle_get_input_element(msg.name, msg.src);

                if (input === null) {
                    // Requested something that is not available.
                    const ret = {
                        version: 'STACK-JS:1.0.0',
                        type: 'error',
                        msg: 'Failed to modify input: "' + msg.name + '"',
                        tgt: msg.src
                    };
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(ret), '*');
                    return;
                }

                // Disable change events.
                DISABLE_CHANGES = true;

                // TO-DO: Radio buttons should we check that value is possible?
                if (input.tagName.toLowerCase() === 'ion-checkbox') {
                    input.checked = msg.value;
                } else {
                    input.value = msg.value;
                }

                // Trigger VLE side actions.
                vle_update_input(input);

                // Enable change tracking.
                DISABLE_CHANGES = false;

                // Tell all other frames, that care, about this.
                response.type = 'changed-input';
                response.name = msg.name;
                response.value = msg.value;

                for (let tgt of INPUTS[input.id]) {
                    if (tgt !== msg.src) {
                        response.tgt = tgt;
                        IFRAMES[tgt].contentWindow.postMessage(JSON.stringify(response), '*');
                    }
                }
                break;
            case 'clear-input':
                // 1. Find the input.
                input = vle_get_input_element(msg.name, msg.src);

                if (input.tagName.toLowerCase() === 'ion-checkbox') {
                    for (let inp of vle_get_others_of_same_input_group(input)) {
                        inp.checked = false;
                        vle_update_input(inp);
                    }
                } else if (input.type === 'radio') {
                    for (let inp of vle_get_others_of_same_input_group(input)) {
                        // If we have the clear value option select that.
                        inp.checked = inp.value === '';
                        vle_update_input(inp);
                    }
                } else {
                    if (input.value !== '') {
                        input.value = '';
                        vle_update_input(input);
                    }
                }

                vle_update_input(input);
                break;
            case 'register-button-listener':
                // 1. Find the element.
                element = vle_get_element(msg.target);

                if (element === null) {
                    // Requested something that is not available.
                    const ret = {
                        version: 'STACK-JS:1.2.0',
                        type: 'error',
                        msg: 'Failed to find element: "' + msg.target + '"',
                        tgt: msg.src
                    };
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(ret), '*');
                    return;
                }

                // 2. Add a listener, no need to do anything more
                // complicated than this.
                element.addEventListener('click', (event) => {
                    let resp = {
                        version: 'STACK-JS:1.2.0',
                        type: 'button-click',
                        name: msg.target,
                        tgt: msg.src
                    };
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(resp), '*');
                    // These listeners will stop the submissions of buttons which might be a problem.
                    event.preventDefault();
                });

                break;
            case 'toggle-visibility':
                // 1. Find the element.
                element = vle_get_element(msg.target);

                if (element === null) {
                    // Requested something that is not available.
                    const ret = {
                        version: 'STACK-JS:1.0.0',
                        type: 'error',
                        msg: 'Failed to find element: "' + msg.target + '"',
                        tgt: msg.src
                    };
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(ret), '*');
                    return;
                }

                // 2. Toggle display setting.
                if (msg.set === 'show') {
                    element.style.display = 'block';
                    // If we make something visible we should let the VLE know about it.
                    vle_update_dom(element);
                } else if (msg.set === 'hide') {
                    element.style.display = 'none';
                }

                break;
            case 'change-content':
                // 1. Find the element.
                element = vle_get_element(msg.target);

                if (element === null) {
                    // Requested something that is not available.
                    response.type = 'error';
                    response.msg = 'Failed to find element: "' + msg.target + '"';
                    response.tgt = msg.src;
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                    return;
                }

                // 2. Secure content.
                // 3. Switch the content. Note the contents coming from `vle_html_sanitize`
                // are wrapped in an element possibly `<body>` and will need to be unwrapped.
                // We can simply use innerHTML here to also disconnect the content from
                // whatever it was before being sanitized.
                element.innerHTML = vle_html_sanitize(msg.content).innerHTML;
                // If we tune something we should let the VLE know about it.
                vle_update_dom(element);

                break;
            case 'get-content':
                // 1. Find the element.
                element = vle_get_element(msg.target);
                // 2. Build the message.
                response.type = 'xfer-content';
                response.tgt = msg.src;
                response.target = msg.target;
                response.content = null;
                if (element !== null) {
                    // TO-DO: Should we sanitise the content? Probably not as using
                    // this to interrogate neighbouring questions only allows
                    // messing with the other questions and not anything outside
                    // them. If we do not sanitise it we allow some interesting
                    // question-analytics tooling, and if we do we really don't
                    // gain anything sensible.
                    // Matti's opinnion is to not sanitise at this point as
                    // interraction between questions is not inherently evil
                    // and could be of use even at the level of reading code from
                    // from other questions.
                    response.content = element.innerHTML;
                }
                IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                break;
            case 'resize-frame':
                // 1. Find the frames wrapper div.
                element = IFRAMES[msg.src].parentElement;

                // 2. Set the wrapper size.
                element.style.width = msg.width;
                element.style.height = msg.height;

                // 3. Reset the frame size.
                IFRAMES[msg.src].style.width = '100%';
                IFRAMES[msg.src].style.height = '100%';

                // Only touching the size but still let the VLE know.
                vle_update_dom(element);
                break;
            case 'ping':
                // This is for testing the connection. The other end will
                // send these untill it receives a reply.
                // Part of the logic for startup.
                response.type = 'ping';
                response.tgt = msg.src;

                IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                return;
            case 'query-submit-button':
                response.type = 'submit-button-info';
                response.tgt = msg.src;
                input = vle_get_submit_button(msg.src);
                if (input === null || input.hasAttribute('hidden')) {
                    response['value'] = null;
                } else {
                    response['value'] = input.value;
                }
                IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                return;
            case 'enable-submit-button':
                input = vle_get_submit_button(msg.src);
                if (input !== null) {
                    if (msg.enabled) {
                        input.removeAttribute('disabled');
                    } else {
                        input.disabled = true;
                    }
                } else {
                    // We generate this error just to push people to properly check if
                    // the button even exists before trying to tune it.
                    response.type = 'error';
                    response.msg = 'Could not find matching submit button for this question.';
                    response.tgt = msg.src;
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                }
                return;
            case 'relabel-submit-button':
                input = vle_get_submit_button(msg.src);
                if (input !== null) {
                    if (input.childNodes.length > 1) {
                        // If we happen to have some extra SR stuff...
                        input.childNodes.forEach((n) => {
                            if (n.nodeName == '#text') {
                                n.textContent = msg.name;
                            }
                        });
                    } else {
                        input.innerHTML = msg.name;
                    }
                    input.value = msg.name;
                } else {
                    // We generate this error just to push people to properly check if
                    // the button even exists before trying to tune it.
                    response.type = 'error';
                    response.msg = 'Could not find matching submit button for this question.';
                    response.tgt = msg.src;
                    IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
                }
                return;
            case 'submit-button-info':
            case 'initial-input':
            case 'error':
                // These message types are for the other end.
                break;

            default:
                // If we see something unexpected, lets let the other end know
                // and make sure that they know our version. Could be that this
                // end has not been upgraded.
                response.type = 'error';
                response.msg = 'Unknown message-type: "' + msg.type + '"';
                response.tgt = msg.src;

                IFRAMES[msg.src].contentWindow.postMessage(JSON.stringify(response), '*');
            }

        }


        /** To avoid any logic that forbids IFRAMEs in the VLE output one can
            also create and register that IFRAME through this logic. This
            also ensures that all relevant security settigns for that IFRAME
            have been correctly tuned.

            Here the IDs are for the secrect identifier that may be present
            inside the content of that IFRAME and for the question that contains
            it. One also identifies a DIV element that marks the position of
            the IFRAME and limits the size of the IFRAME (all IFRAMEs this
            creates will be 100% x 100%).

            @param {String} iframeid the id that the IFRAME has stored inside
                    it and uses for communication.
            @param {String} content the full HTML content of that IFRAME.
            @param {String} targetdivid the id of the element (div) that will
                    hold the IFRAME.
            @param {String} title a descriptive name for the iframe.
            @param {bool} scrolling whether we have overflow:scroll or
                    overflow:hidden.
            @param {bool} evil allows certain special cases to act without
                    sandboxing, this is a feature that will be removed so do
                    not rely on it only use it to test STACK-JS before you get your
                    thing to run in a sandbox.
            */
        function create_iframe(iframeid, content, targetdivid, title, scrolling, evil) {
            const frm = document.createElement('iframe');
            frm.id = iframeid;
            frm.style.width = '100%';
            frm.style.height = '100%';
            frm.style.border = 0;
            if (scrolling === false) {
                frm.scrolling = 'no';
                frm.style.overflow = 'hidden';
            } else {
                frm.scrolling = 'yes';
            }
            frm.title = title;
            // Somewhat random limitation.
            frm.referrerpolicy = 'no-referrer';
            // We include that allow-downloads as an example of XLS-
            // document building in JS has been seen.
            // UNDER NO CIRCUMSTANCES DO WE ALLOW-SAME-ORIGIN!
            // That would defeat the whole point of this.
            if (!evil) {
                frm.sandbox = 'allow-scripts allow-downloads';
            }

            // As the SOP is intentionally broken we need to allow
            // scripts from everywhere.

            // NOTE: this bit commented out as long as the csp-attribute
            // is not supported by more browsers.
            // frm.csp = "script-src: 'unsafe-inline' 'self' '*';";
            // frm.csp = "script-src: 'unsafe-inline' 'self' '*';img-src: '*';";

            // Plug the content into the frame.
            frm.srcdoc = content;

            // The target DIV will have its children removed.
            // This allows that div to contain some sort of loading
            // indicator until we plug in the frame.
            // Naturally the frame will then start to load itself.
            document.getElementById(targetdivid).replaceChildren(frm);
            IFRAMES[iframeid] = frm;
        }

        return true;
    }
};

// This next line is required as is (because of an eval step that puts this result object into the global scope).
result;
